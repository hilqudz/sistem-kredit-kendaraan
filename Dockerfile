# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Create Laravel required directories
RUN mkdir -p storage/logs \
    && mkdir -p storage/framework/{cache,sessions,views} \
    && mkdir -p bootstrap/cache

# Install dependencies using Composer create-project approach
RUN composer install --no-dev --optimize-autoloader --no-interaction || \
    (composer create-project laravel/laravel . "11.*" --prefer-dist --no-interaction && \
     cp -r app/ resources/ routes/ database/ config/ . 2>/dev/null || true)

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy Apache configuration
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Expose port 80
EXPOSE 80

# Start with railway script
CMD ["./railway-start.sh"]