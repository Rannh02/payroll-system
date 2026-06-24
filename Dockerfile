FROM php:8.2-apache

# 1. Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# 2. Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 4. Enable Apache mod_rewrite
RUN a2enmod rewrite

# 5. Set working directory
WORKDIR /var/www/html

# 6. Copy current directory contents
COPY . .

# 7. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 8. Set Apache DocumentRoot to Laravel /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 9. Install Laravel dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 10. Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 11. Expose port (Render uses $PORT, Apache uses 80 by default)
EXPOSE 80

# 12. Command to run
CMD ["apache2-foreground"]
