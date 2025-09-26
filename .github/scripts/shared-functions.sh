#!/bin/bash

# Shared deployment functions
# Used by both staging and production deployment scripts

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging functions
log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

log_step() {
    echo -e "${BLUE}🔄 $1${NC}"
}

# Check if deployment lock exists and handle stale locks
check_deployment_lock() {
    local lock_file=$1
    local lock_name=$2
    
    if [ -f "$lock_file" ]; then
        LOCK_TIME=$(cat "$lock_file")
        LOCK_AGE=$(($(date +%s) - $(date -d "$LOCK_TIME" +%s 2>/dev/null || echo 0)))
        if [ $LOCK_AGE -gt 1800 ]; then
            log_warning "Removing stale deployment lock (older than 30 minutes)"
            rm -f "$lock_file"
        else
            log_error "Another $lock_name deployment is already running. Exiting."
            log_info "Lock created at: $LOCK_TIME"
            exit 0
        fi
    fi
}

# Create deployment lock
create_deployment_lock() {
    local lock_file=$1
    echo "$(date)" > "$lock_file"
}

# Remove deployment lock
remove_deployment_lock() {
    local lock_file=$1
    rm -f "$lock_file"
}

# Create backup directory if it doesn't exist
ensure_backup_directory() {
    local backup_dir=$1
    if [ ! -d "$backup_dir" ]; then
        log_info "Creating backup directory: $backup_dir"
        mkdir -p "$backup_dir"
    fi
}

# Create backup of existing project
create_backup() {
    local project_path=$1
    local backup_path=$2
    
    if [ -f "$project_path/artisan" ]; then
        log_step "Creating backup..."
        cp -r "$project_path" "$backup_path"
        log_success "Backup created"
        return 0
    else
        log_info "Fresh installation detected - no backup needed"
        return 1
    fi
}

# Enable maintenance mode
enable_maintenance_mode() {
    local maintenance_secret=$1
    log_step "Enabling maintenance mode..."
    
    if [ -n "$maintenance_secret" ]; then
        php artisan down --retry=60 --secret="$maintenance_secret" --render="errors::503" || true
    else
        log_warning "No maintenance secret provided, using default maintenance mode"
        php artisan down --retry=60 --render="errors::503" || true
    fi
    
    log_success "Maintenance mode enabled"
}

# Disable maintenance mode
disable_maintenance_mode() {
    log_step "Disabling maintenance mode..."
    php artisan up
    log_success "Application is now live"
}

# Update git repository
update_code() {
    local branch=$1
    log_step "Updating code..."
    git fetch origin "$branch"
    git reset --hard "origin/$branch"
    git clean -fd
    log_success "Code updated"
}

# Set proper Laravel permissions
set_permissions() {
    log_step "Setting proper permissions..."
    chmod -R 755 storage bootstrap/cache
    chmod -R 775 storage/logs storage/framework storage/app 2>/dev/null || true
    log_success "Permissions set"
}

# Smart dependency management for Composer
manage_composer_dependencies() {
    log_step "Checking Composer dependencies..."
    
    if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
        log_step "Installing Composer dependencies..."
        composer install --no-dev --optimize-autoloader --no-interaction
    else
        log_step "Composer dependencies exist, updating..."
        composer update --no-dev --optimize-autoloader --no-interaction
    fi
    log_success "Composer dependencies ready"
}

# Smart dependency management for NPM
manage_npm_dependencies() {
    log_step "Checking NPM dependencies..."
    
    if [ ! -d "node_modules" ] || [ ! -f "package-lock.json" ]; then
        log_step "Installing NPM dependencies..."
        npm ci
    else
        log_step "Node modules exist, updating..."
        npm update
    fi
    log_success "NPM dependencies ready"
}

# Setup Node.js environment and build assets
setup_nodejs_and_build() {
    export PATH="/usr/bin:/usr/local/bin:/opt/nodejs/bin:/usr/home/ideacts/.nvm/versions/node/v22.19.0/bin:$PATH"
    
    log_step "Checking Node.js availability..."
    which node || log_error "Node.js not found in PATH"
    which npm || log_error "npm not found in PATH"
    
    manage_npm_dependencies
    
    log_step "Building assets..."
    npm run build
    log_success "Assets built successfully"
    
    log_step "Generating API documentation..."
    php artisan scribe:generate
    log_success "API documentation generated"
}

# Normalize URL (remove trailing slash)
normalize_url() {
    local url=$1
    echo "$url" | sed 's|/$||'
}

# Update environment variables
update_env_var() {
    local key=$1
    local value=$2
    
    # Normalize APP_URL to prevent double slashes
    if [ "$key" = "APP_URL" ]; then
        value=$(normalize_url "$value")
    fi
    
    if grep -q "^${key}=" .env; then
        sed -i "s|^${key}=.*|${key}=${value}|" .env
    else
        echo "${key}=${value}" >> .env
    fi
}

# Create storage symbolic link
create_storage_link() {
    log_step "Creating storage link..."
    php artisan storage:link || true
    log_success "Storage link created"
}

# Ensure Laravel app key exists
ensure_app_key() {
    log_step "Ensuring Laravel app key exists..."
    
    # Check if APP_KEY is set in .env
    if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null || [ -z "$(grep "^APP_KEY=" .env | cut -d'=' -f2)" ]; then
        log_step "Generating Laravel application key..."
        php artisan key:generate --force
        log_success "Application key generated"
    else
        log_success "Application key already exists"
    fi
}

# Run Laravel optimizations
run_laravel_optimizations() {
    log_step "Running Laravel optimizations..."
    
    # Clear all caches first (in case of stale config)
    php artisan config:clear
    php artisan cache:clear || true
    php artisan route:clear || true
    php artisan view:clear || true
    
    # Run migrations
    php artisan migrate --force
    
    # Rebuild caches
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan queue:restart || true
    
    log_success "Laravel optimizations completed"
}

# Rollback function
rollback_deployment() {
    local project_path=$1
    local backup_path_file=$2
    local lock_file=$3
    
    log_error "Deployment failed! Rolling back..."
    
    if [ -f "$backup_path_file" ]; then
        BACKUP_PATH=$(cat "$backup_path_file")
        if [ -d "$BACKUP_PATH" ]; then
            log_step "Restoring from backup: $BACKUP_PATH"
            rm -rf "$project_path"/*
            cp -r "$BACKUP_PATH"/* "$project_path"/
            cd "$project_path"
            disable_maintenance_mode
            log_success "Rollback completed"
            remove_deployment_lock "$lock_file"
            exit 1
        fi
    fi
    
    log_error "Rollback failed - manual intervention required"
    remove_deployment_lock "$lock_file"
    exit 1
}

# List available backups
list_backups() {
    local backup_dir=$1
    local environment=$2
    
    log_info "📋 Available $environment backups:"
    if [ -d "$backup_dir" ]; then
        cd "$backup_dir"
        ls -lt | grep "^d" | head -10 | while read -r line; do
            backup_name=$(echo "$line" | awk '{print $NF}')
            backup_date=$(echo "$line" | awk '{print $6, $7, $8}')
            log_info "  📦 $backup_name (created: $backup_date)"
        done
    else
        log_warning "No backup directory found at: $backup_dir"
    fi
}

# Validate backup exists
validate_backup() {
    local backup_path=$1
    
    if [ ! -d "$backup_path" ]; then
        log_error "Backup not found: $backup_path"
        return 1
    fi
    
    if [ ! -f "$backup_path/artisan" ]; then
        log_error "Invalid backup: Missing artisan file in $backup_path"
        return 1
    fi
    
    log_success "Backup validated: $backup_path"
    return 0
}

# Perform rollback to specific backup
perform_rollback() {
    local project_path=$1
    local backup_path=$2
    local environment=$3
    
    log_step "🔄 Starting rollback to: $(basename "$backup_path")"
    
    # Validate backup
    if ! validate_backup "$backup_path"; then
        return 1
    fi
    
    # Enable maintenance mode
    cd "$project_path"
    enable_maintenance_mode "${MAINTENANCE_SECRET:-}"
    
    # Create safety backup of current state
    local safety_backup="$backup_path/../safety-backup-$(date +%Y%m%d_%H%M%S)"
    log_step "Creating safety backup of current state..."
    cp -r "$project_path" "$safety_backup"
    log_success "Safety backup created: $safety_backup"
    
    # Perform rollback
    log_step "Restoring files from backup..."
    rm -rf "$project_path"/*
    cp -r "$backup_path"/* "$project_path"/
    
    cd "$project_path"
    
    # Set permissions
    set_permissions
    
    # Run Laravel optimizations (without migrations to avoid conflicts)
    log_step "Running Laravel optimizations..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan queue:restart || true
    log_success "Laravel optimizations completed"
    
    # Disable maintenance mode
    disable_maintenance_mode
    
    log_success "🎉 Rollback completed successfully!"
    log_info "📦 Restored from: $(basename "$backup_path")"
    log_info "🛡️ Safety backup available at: $safety_backup"
}

# Interactive backup selection
select_backup_interactive() {
    local backup_dir=$1
    local environment=$2
    
    if [ ! -d "$backup_dir" ]; then
        log_error "Backup directory not found: $backup_dir"
        return 1
    fi
    
    cd "$backup_dir"
    backups=($(ls -t | grep "^$environment.*backup" | head -10))
    
    if [ ${#backups[@]} -eq 0 ]; then
        log_error "No backups found in $backup_dir"
        return 1
    fi
    
    log_info "📋 Available backups:"
    for i in "${!backups[@]}"; do
        backup_date=$(stat -c %y "${backups[$i]}" 2>/dev/null | cut -d' ' -f1,2 | cut -d'.' -f1)
        log_info "  $((i+1)). ${backups[$i]} (created: $backup_date)"
    done
    
    # For automated rollback, return the most recent backup
    echo "$backup_dir/${backups[0]}"
}

# Cleanup old safety backups
cleanup_safety_backups() {
    local backup_dir=$1
    
    log_step "🧹 Cleaning up old safety backups..."
    cd "$backup_dir"
    ls -t | grep "^safety-backup-" | tail -n +4 | xargs -r rm -rf
    log_success "Safety backup cleanup completed"
}
