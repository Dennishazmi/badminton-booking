FROM php:8.1-fpm-alpine

WORKDIR /app

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    npm \
    mysql-client

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql

# Copy composer installer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies
RUN npm install

# Build assets
RUN npm run build

# Generate app key
RUN php artisan key:generate

# Expose port
EXPOSE 8000

# Start Laravel
CMD php artisan serve --host=0.0.0.0 --port=8000
