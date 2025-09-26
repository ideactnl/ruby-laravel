#!/bin/bash

# Production Deployment Script
# Deploys the application to production environment

set -e

# Load shared functions
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/shared-functions.sh"

# Configuration
PROJECT_PATH="${PROJECT_PATH:-/usr/home/ideacts/public_html/ruby}"
BACKUP_DIR="${BACKUP_DIR:-/usr/home/ideacts/public_html/ruby_backups}"
BACKUP_PATH="$BACKUP_DIR/ruby-backup-$(date +%Y%m%d_%H%M%S)"
LOCK_FILE="/tmp/production_deployment_lock"
BACKUP_PATH_FILE="/tmp/production_backup_path"

# Environment variables with defaults
APP_ENV="${APP_ENV:-production}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://ruby.ideact-server.nl}"
DB_DATABASE="${DB_DATABASE:-ruby_production}"
QUEUE_CONNECTION="${QUEUE_CONNECTION:-database}"
LOG_LEVEL="${LOG_LEVEL:-error}"
MAINTENANCE_SECRET="${MAINTENANCE_SECRET:-}"

# Main deployment function
main() {
    log_info "🚀 Starting production deployment..."
    log_info "📁 Project: $PROJECT_PATH"
    log_info "💾 Backup: $BACKUP_PATH"
    
    # Ensure backup directory exists
    ensure_backup_directory "$BACKUP_DIR"
    
    # Check for existing deployment
    check_deployment_lock "$LOCK_FILE" "production"
    
    # Create deployment lock
    create_deployment_lock "$LOCK_FILE"
    
    # Change to project directory
    cd "$PROJECT_PATH"
    
    # Create backup and enable maintenance mode if project exists
    if create_backup "$PROJECT_PATH" "$BACKUP_PATH"; then
        enable_maintenance_mode "$MAINTENANCE_SECRET"
    fi
    
    # Store backup path for potential rollback
    echo "$BACKUP_PATH" > "$BACKUP_PATH_FILE"
    
    # Set trap for automatic rollback on error
    trap 'rollback_deployment "$PROJECT_PATH" "$BACKUP_PATH_FILE" "$LOCK_FILE"' ERR
    
    # Update code from main branch
    update_code "main"
    
    # Set proper permissions
    set_permissions
    
    # Manage dependencies
    manage_composer_dependencies
    
    # Setup Node.js and build assets
    setup_nodejs_and_build
    
    # Update environment variables
    log_step "⚙️ Updating environment..."
    update_env_var "APP_ENV" "$APP_ENV"
    update_env_var "APP_DEBUG" "$APP_DEBUG"
    update_env_var "APP_URL" "$APP_URL"
    update_env_var "DB_DATABASE" "$DB_DATABASE"
    update_env_var "QUEUE_CONNECTION" "$QUEUE_CONNECTION"
    update_env_var "LOG_LEVEL" "$LOG_LEVEL"
    
    # Create storage link
    create_storage_link
    
    # Run Laravel optimizations
    run_laravel_optimizations
    
    # Disable maintenance mode
    disable_maintenance_mode
    
    # Clean up old backups (keep last 10 for production)
    log_step "🧹 Cleaning up old backups..."
    cd "$BACKUP_DIR"
    ls -t | tail -n +11 | xargs -r rm -rf
    log_success "Old backups cleaned up"
    
    # Remove deployment lock
    remove_deployment_lock "$LOCK_FILE"
    
    log_success "🎉 Production deployment completed successfully!"
}

# Run main function
main "$@"
