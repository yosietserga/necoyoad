# New Necoyoad — Dockerfile (FrankenPHP + PHP 8.3)
FROM dunglas/frankenphp:latest-php8.3

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Create application directory structure
RUN mkdir -p bootstrap/cache storage/logs

# Copy application
COPY . .

# Install PHP dependencies, skipping problematic post-autoload-dump
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts && \
    mkdir -p bootstrap/cache && \
    composer run-script post-autoload-dump || true

# Set final permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# FrankenPHP serves on port 80
EXPOSE 80

# Start FrankenPHP
CMD ["frankenphp", "run"]
