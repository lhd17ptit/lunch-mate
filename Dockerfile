# Sử dụng PHP image chính thức
FROM php:8.2

# Install các extension cần thiết cho Laravel
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy toàn bộ project vào container
COPY . .

# Cài đặt các package PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN php artisan storage:link || true

# Copy entrypoint vào image
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Chạy entrypoint thay cho CMD cũ
ENTRYPOINT ["docker-entrypoint.sh"]