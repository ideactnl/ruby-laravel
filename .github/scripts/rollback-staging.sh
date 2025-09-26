#!/bin/bash

# Staging Rollback Script
# Rolls back the staging environment to a previous backup

set -e

# Load shared functions
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/shared-functions.sh"

# Configuration
PROJECT_PATH="${PROJECT_PATH:-/usr/home/ideacts/public_html/ruby-staging}"
BACKUP_DIR="${BACKUP_DIR:-/usr/home/ideacts/public_html/ruby_staging_backups}"
ENVIRONMENT="staging"
MAINTENANCE_SECRET="${MAINTENANCE_SECRET:-}"

# Rollback to specific backup or latest
BACKUP_NAME="${BACKUP_NAME:-}"

# Main rollback function
main() {
    log_info "🔄 Starting staging rollback..."
    log_info "📁 Project: $PROJECT_PATH"
    log_info "💾 Backup Directory: $BACKUP_DIR"
    
    # Check if project directory exists
    if [ ! -d "$PROJECT_PATH" ]; then
        log_error "Project directory not found: $PROJECT_PATH"
        exit 1
    fi
    
    # List available backups
    list_backups "$BACKUP_DIR" "$ENVIRONMENT"
    
    # Determine which backup to use
    if [ -n "$BACKUP_NAME" ]; then
        # Specific backup requested
        BACKUP_PATH="$BACKUP_DIR/$BACKUP_NAME"
        log_info "🎯 Using specified backup: $BACKUP_NAME"
    else
        # Use most recent backup
        BACKUP_PATH=$(select_backup_interactive "$BACKUP_DIR" "ruby-staging")
        if [ -z "$BACKUP_PATH" ]; then
            log_error "No suitable backup found"
            exit 1
        fi
        log_info "🎯 Using most recent backup: $(basename "$BACKUP_PATH")"
    fi
    
    # Confirm rollback (in interactive mode)
    if [ -t 0 ]; then
        log_warning "⚠️  This will replace the current staging environment!"
        log_info "📦 Rollback to: $(basename "$BACKUP_PATH")"
        read -p "Continue? (y/N): " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            log_info "Rollback cancelled"
            exit 0
        fi
    fi
    
    # Perform the rollback
    perform_rollback "$PROJECT_PATH" "$BACKUP_PATH" "$ENVIRONMENT"
    
    # Cleanup old safety backups
    cleanup_safety_backups "$BACKUP_DIR"
    
    log_success "🎉 Staging rollback completed successfully!"
    log_info "🌐 Staging site: http://ruby-staging.ideact-server.nl"
}

# Show usage information
usage() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  -b, --backup NAME    Rollback to specific backup"
    echo "  -l, --list          List available backups only"
    echo "  -h, --help          Show this help message"
    echo ""
    echo "Environment Variables:"
    echo "  PROJECT_PATH        Path to staging project (default: /usr/home/ideacts/public_html/ruby-staging)"
    echo "  BACKUP_DIR          Path to backup directory (default: /usr/home/ideacts/public_html/ruby_staging_backups)"
    echo "  MAINTENANCE_SECRET  Secret for maintenance mode bypass"
    echo ""
    echo "Examples:"
    echo "  $0                           # Rollback to most recent backup"
    echo "  $0 -b ruby-staging-backup-20240926_123456  # Rollback to specific backup"
    echo "  $0 -l                        # List available backups"
}

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        -b|--backup)
            BACKUP_NAME="$2"
            shift 2
            ;;
        -l|--list)
            list_backups "$BACKUP_DIR" "$ENVIRONMENT"
            exit 0
            ;;
        -h|--help)
            usage
            exit 0
            ;;
        *)
            log_error "Unknown option: $1"
            usage
            exit 1
            ;;
    esac
done

# Run main function
main "$@"
