FROM richarvey/nginx-php-fpm:3.1.6

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Copy custom nginx config
COPY conf/nginx/nginx-site.conf /etc/nginx/sites-available/default

# Copy deploy script
COPY scripts/00-laravel-deploy.sh /docker-entrypoint-init.d/00-laravel-deploy.sh
RUN chmod +x /docker-entrypoint-init.d/00-laravel-deploy.sh

# Environment variables (can be overridden in Render dashboard)
ENV WEBROOT=/var/www/html/public \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    PHP_ERRORS_STDERR=1 \
    RUN_SCRIPTS=1 \
    COMPOSER_ALLOW_SUPERUSER=1

# Expose port 80
EXPOSE 80

# Start services (handled by base image)
CMD ["/start.sh"]
