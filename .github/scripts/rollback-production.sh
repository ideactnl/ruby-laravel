#!/bin/bash

# Production Rollback Script
# Rolls back the production environment to a previous backup

set -e

# Load shared functions
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/shared-functions.sh"

# Configuration
PROJECT_PATH="${PROJECT_PATH:-/usr/home/ideacts/public_html/ruby}"
BACKUP_DIR="${BACKUP_DIR:-/usr/home/ideacts/public_html/ruby_backups}"
ENVIRONMENT="production"
MAINTENANCE_SECRET="${MAINTENANCE_SECRET:-}"

# Rollback to specific backup or latest
BACKUP_NAME="${BACKUP_NAME:-}"

# Production safety checks
FORCE_ROLLBACK="${FORCE_ROLLBACK:-false}"

# Main rollback function
main() {
    log_info "🔄 Starting PRODUCTION rollback..."
    log_info "📁 Project: $PROJECT_PATH"
    log_info "💾 Backup Directory: $BACKUP_DIR"
    
    # Production safety warning
    if [ "$FORCE_ROLLBACK" != "true" ]; then
        log_warning "⚠️  PRODUCTION ROLLBACK - HIGH RISK OPERATION!"
        log_warning "This will affect live users and data!"
        
        if [ -t 0 ]; then
            read -p "Are you absolutely sure? Type 'ROLLBACK' to continue: " -r
            if [ "$REPLY" != "ROLLBACK" ]; then
                log_info "Production rollback cancelled for safety"
                exit 0
            fi
        else
            log_error "Production rollback requires FORCE_ROLLBACK=true for automated execution"
            exit 1
        fi
    fi
    
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
        BACKUP_PATH=$(select_backup_interactive "$BACKUP_DIR" "ruby")
        if [ -z "$BACKUP_PATH" ]; then
            log_error "No suitable backup found"
            exit 1
        fi
        log_info "🎯 Using most recent backup: $(basename "$BACKUP_PATH")"
    fi
    
    # Final confirmation for production
    if [ -t 0 ] && [ "$FORCE_ROLLBACK" != "true" ]; then
        log_warning "🚨 FINAL CONFIRMATION FOR PRODUCTION ROLLBACK"
        log_info "📦 Rollback to: $(basename "$BACKUP_PATH")"
        log_warning "This will immediately affect live users!"
        read -p "Proceed with PRODUCTION rollback? (y/N): " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            log_info "Production rollback cancelled"
            exit 0
        fi
    fi
    
    # Perform the rollback
    perform_rollback "$PROJECT_PATH" "$BACKUP_PATH" "$ENVIRONMENT"
    
    # Cleanup old safety backups (keep more for production)
    log_step "🧹 Cleaning up old safety backups..."
    cd "$BACKUP_DIR"
    ls -t | grep "^safety-backup-" | tail -n +6 | xargs -r rm -rf
    log_success "Safety backup cleanup completed (kept 5 most recent)"
    
    log_success "🎉 Production rollback completed successfully!"
    log_warning "🔍 Please verify the production site immediately!"
    log_info "🌐 Production site: http://ruby.ideact-server.nl"
}

# Show usage information
usage() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "⚠️  WARNING: This script performs PRODUCTION rollbacks!"
    echo ""
    echo "Options:"
    echo "  -b, --backup NAME    Rollback to specific backup"
    echo "  -f, --force         Force rollback without interactive confirmation"
    echo "  -l, --list          List available backups only"
    echo "  -h, --help          Show this help message"
    echo ""
    echo "Environment Variables:"
    echo "  PROJECT_PATH        Path to production project (default: /usr/home/ideacts/public_html/ruby)"
    echo "  BACKUP_DIR          Path to backup directory (default: /usr/home/ideacts/public_html/ruby_backups)"
    echo "  MAINTENANCE_SECRET  Secret for maintenance mode bypass"
    echo "  FORCE_ROLLBACK      Set to 'true' to skip safety confirmations"
    echo ""
    echo "Examples:"
    echo "  $0                           # Rollback to most recent backup (interactive)"
    echo "  $0 -b ruby-backup-20240926_123456    # Rollback to specific backup"
    echo "  $0 -f                        # Force rollback without confirmation"
    echo "  $0 -l                        # List available backups"
    echo ""
    echo "Automated Usage (CI/CD):"
    echo "  FORCE_ROLLBACK=true $0 -b backup-name"
}

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        -b|--backup)
            BACKUP_NAME="$2"
            shift 2
            ;;
        -f|--force)
            FORCE_ROLLBACK="true"
            shift
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
