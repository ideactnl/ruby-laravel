# Ruby-Laravel API Project

A comprehensive Laravel API application for medical data management with automated deployment workflows and comprehensive testing.

## 🚀 Features

- **RESTful API** - Complete API endpoints for medical specialist and participant data
- **Authentication** - Secure user authentication with Laravel Sanctum
- **Role-based Access** - Medical specialist and participant role management
- **Data Export** - PDF and Excel export functionality
- **API Documentation** - Auto-generated documentation with Scribe
- **Automated Testing** - PHPStan, Laravel Pint, and PHPUnit integration
- **CI/CD Pipeline** - Automated deployment to staging and production
- **Backup System** - Automated backups with rollback capabilities

## 🛠️ Tech Stack

- **Backend**: Laravel 12.x, PHP 8.3+
- **Database**: MySQL
- **Frontend Assets**: Vite, Node.js 22+
- **Testing**: PHPUnit, PHPStan, Laravel Pint
- **Documentation**: Scribe API Documentation
- **Deployment**: GitHub Actions, SSH deployment

## 📋 Prerequisites

- PHP 8.3 or higher
- Composer
- Node.js 22+ and npm
- MySQL database
- Git

## 🔧 Local Development Setup

> **💡 Laravel Sail Alternative:** This project includes a `docker-compose.yml` file for Laravel Sail. If you're familiar with Sail, you can use `./vendor/bin/sail up -d` for a complete Docker-based development environment with MySQL, Redis, and other services. The instructions below are for traditional local setup.

### 1. Clone and Install Dependencies

```bash
# Clone the repository
git clone <repository-url>
cd ruby-laravel

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 2. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your .env file with:
# - Database credentials
# - Application URL
# - Mail settings (if needed)
# - Any other environment-specific settings
```

### 3. Database Setup

```bash
# Run database migrations
php artisan migrate

# Seed the database (optional)
php artisan db:seed
```

### 4. Build Frontend Assets

```bash
# Development build
npm run dev

# Production build
npm run build

# Watch for changes (development)
npm run dev -- --watch
```

### 5. Generate API Documentation

```bash
# Generate API documentation
php artisan scribe:generate

# Access documentation at: /docs
```

## 🧪 Testing

### Run All Tests

```bash
# Run PHPUnit tests
php artisan test

# Run static analysis
composer stan

# Run code formatting
composer lint

# OR Run all of the above together
composer ci
```

### Individual Test Commands

```bash
# PHPStan (Static Analysis)
./vendor/bin/phpstan analyse

# Laravel Pint (Code Formatting)
./vendor/bin/pint

# PHPUnit (Unit & Feature Tests)
./vendor/bin/phpunit
```

## 🚀 Deployment

### Automated Deployment

The project uses GitHub Actions for automated deployment:

- **Staging**: Deploys automatically on push to `staging` branch
- **Production**: Deploys automatically on push to `main` branch

### Manual Deployment

For manual deployment, ensure you have:

1. **Server Requirements**:
   - PHP 8.3+
   - Composer
   - Node.js 22+
   - MySQL
   - Web server (Apache/Nginx)

2. **Deployment Steps**:
   ```bash
   # On your server
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## 🔄 Rollback

### Automated Rollback

Use the GitHub Actions "Manual Rollback" workflow:

1. Go to **Actions** tab in GitHub
2. Select **"Manual Rollback"** workflow
3. Click **"Run workflow"**
4. Choose environment (`staging` or `production`)
5. Optionally specify backup timestamp

### Manual Rollback

```bash
# List available backups
ls -la /path/to/backups/

# Restore from backup
cp -r /path/to/backup/* /path/to/project/
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📚 API Documentation

- **Local**: `http://localhost/docs`
- **Staging**: `https://staging-domain/docs`
- **Production**: `https://production-domain/docs`

## 🔧 Development Commands

```bash
# Clear all caches
php artisan optimize:clear

# Generate IDE helper files
php artisan ide-helper:generate
php artisan ide-helper:models

# Queue management
php artisan queue:work
php artisan queue:restart

# Maintenance mode
php artisan down
php artisan up
```

### Code Standards

- Follow **PSR-12** coding standards
- Write **tests** for new features
- Update **documentation** as needed
- Run **quality checks** before committing

## 📝 License
This project is proprietary and confidential.
---

**Happy coding!** 🎉