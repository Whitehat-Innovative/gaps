FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    git \
    cron \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip 

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/html

# ✅ Copy Laravel project files into the container
COPY . .

# ✅ Install Laravel dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Expose Laravel serve port
# EXPOSE 8000

# Serve Laravel directly
# CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

# CMD ["php-fpm"]



# Render configuration for ng

# Install Nginx + envsubst
RUN apt-get update && apt-get install -y nginx gettext-base

# Copy Nginx config template
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf.template

# Render exposes a dynamic port
EXPOSE ${PORT}

# Start Nginx and PHP-FPM using PORT
CMD envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf \
    && nginx \
    && php-fpm


