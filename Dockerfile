# Combined Build Stage
FROM php:8.4-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    curl \
    bash \
    nginx \
    supervisor \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install Composer dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN php /usr/local/bin/composer install --optimize-autoloader

# Install NPM dependencies and build assets
# (Wayfinder needs PHP to run artisan, so we build in the same stage)
RUN npm install
RUN npm run build

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod +x docker/entrypoint.sh

# Copy Nginx and Supervisor configs
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Exposure
EXPOSE 80

# Start with entrypoint
ENTRYPOINT ["docker/entrypoint.sh"]
