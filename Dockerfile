# Gunakan image resmi PHP + Apache
FROM php:8.3-apache

# Install dependencies sistem
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    npm

# Install Node.js 20 (untuk Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set workdir ke /var/www/html
WORKDIR /var/www/html

# Copy semua file project ke image
COPY . .

# Install dependencies PHP & Node
RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

# Berikan permission ke storage & bootstrap/cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Enable mod_rewrite Apache
RUN a2enmod rewrite

# Copy Apache config supaya public folder jadi root
RUN echo "<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    </Directory>" > /etc/apache2/conf-available/allow-override.conf && \
    a2enconf allow-override

# Expose port (Railway pakai ini)
EXPOSE 8080

# Jalankan Laravel serve lewat Apache
CMD ["apache2-foreground"]
