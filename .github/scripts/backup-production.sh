#!/bin/bash

# Production Database Backup Script
# Runs before migrations in the production deployment pipeline.
#
# Required environment variables (set via GitHub Actions secrets/vars):
#   DB_HOST         - Database host (e.g. 127.0.0.1)
#   DB_PORT         - Database port (e.g. 3306)
#   DB_DATABASE     - Database name
#   DB_USERNAME     - Database username
#   DB_PASSWORD     - Database password
#
#   LOCAL_BACKUP_DIR     - (optional) Override local backup path
#
#   BACKUP_PLESK_HOST    - Plesk1 SSH host (second remote copy)
#   BACKUP_PLESK_USER    - Plesk1 SSH username
#   BACKUP_PLESK_PATH    - Destination folder path on Plesk1
#
# Note: The local backup saved to ~/backups/production/ on the production server
# (Hetzner) is the first copy. Plesk1 is the second remote copy.

set -e

# Load shared logging functions
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/shared-functions.sh"

# ---------------------------------------------------------------------------
# Configuration
# ---------------------------------------------------------------------------

LOCAL_BACKUP_DIR="${LOCAL_BACKUP_DIR:-$HOME/backups/production}"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$LOCAL_BACKUP_DIR/backup_${TIMESTAMP}.sql"
RETENTION_DAYS=30

# ---------------------------------------------------------------------------
# Validate required environment variables
# ---------------------------------------------------------------------------

validate_env() {
    local missing=()

    for var in DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD; do
        if [ -z "${!var}" ]; then
            missing+=("$var")
        fi
    done

    for var in BACKUP_PLESK_HOST BACKUP_PLESK_USER BACKUP_PLESK_PATH; do
        if [ -z "${!var}" ]; then
            missing+=("$var")
        fi
    done

    if [ ${#missing[@]} -gt 0 ]; then
        log_error "Missing required environment variables:"
        for var in "${missing[@]}"; do
            log_error "  - $var"
        done
        exit 1
    fi
}

# ---------------------------------------------------------------------------
# Create backup
# ---------------------------------------------------------------------------

create_db_backup() {
    log_step "Creating backup directory: $LOCAL_BACKUP_DIR"
    mkdir -p "$LOCAL_BACKUP_DIR"

    log_step "Running mysqldump for database: $DB_DATABASE"
    mysqldump \
        -h "$DB_HOST" \
        -P "$DB_PORT" \
        -u "$DB_USERNAME" \
        -p"$DB_PASSWORD" \
        --single-transaction \
        --routines \
        --triggers \
        --no-tablespaces \
        "$DB_DATABASE" > "$BACKUP_FILE"

    log_success "Dump complete: $BACKUP_FILE"
}

# ---------------------------------------------------------------------------
# Validate backup file
# ---------------------------------------------------------------------------

validate_backup_file() {
    log_step "Validating backup file..."

    if [ ! -f "$BACKUP_FILE" ]; then
        log_error "Backup file does not exist: $BACKUP_FILE"
        exit 1
    fi

    if [ ! -s "$BACKUP_FILE" ]; then
        log_error "Backup file is empty: $BACKUP_FILE"
        exit 1
    fi

    local size
    size=$(du -sh "$BACKUP_FILE" | cut -f1)
    log_success "Backup validated — size: $size"
}

# ---------------------------------------------------------------------------
# Copy to remote locations
# ---------------------------------------------------------------------------

copy_to_plesk() {
    log_step "Copying backup to Plesk1 server (${BACKUP_PLESK_USER}@${BACKUP_PLESK_HOST})..."
    scp -o StrictHostKeyChecking=no \
        "$BACKUP_FILE" \
        "${BACKUP_PLESK_USER}@${BACKUP_PLESK_HOST}:${BACKUP_PLESK_PATH}/" \
    || { log_error "Failed to copy backup to Plesk1"; exit 1; }
    log_success "Plesk1 copy done"
}

# ---------------------------------------------------------------------------
# 30-day rolling retention
# ---------------------------------------------------------------------------

apply_retention() {
    log_step "Applying ${RETENTION_DAYS}-day retention policy on $LOCAL_BACKUP_DIR..."
    find "$LOCAL_BACKUP_DIR" -type f -name "*.sql" -mtime +${RETENTION_DAYS} -delete
    log_success "Retention cleanup done"
}

# ---------------------------------------------------------------------------
# Main
# ---------------------------------------------------------------------------

main() {
    log_info "🗄️  Starting production database backup..."
    log_info "📁 Backup destination: $LOCAL_BACKUP_DIR"

    validate_env
    create_db_backup
    validate_backup_file

    log_info "📁 Copy 1 of 2: Local backup on production server (Hetzner) — done ✅"
    copy_to_plesk
    log_info "📁 Copy 2 of 2: Plesk1 remote copy — done ✅"

    apply_retention

    log_success "🎉 Backup completed successfully: $(basename "$BACKUP_FILE")"
}

main "$@"
