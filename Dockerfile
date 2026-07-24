# ============================================================================
# TATVAM PUBLICATION - PRODUCTION DOCKERFILE FOR RENDER
# Base Image: PHP 8.2 Apache
# ============================================================================

FROM php:8.2-apache

# Install System Dependencies & Build Tools
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql gd mbstring \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Required Apache Modules
RUN a2enmod rewrite headers

# Copy Application Source Files to Apache DocumentRoot
COPY public_html/ /var/www/html/

# Set Proper Permissions for Apache and File Uploads
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/Uploads/cover /var/www/html/Uploads/author_photos /var/www/html/Uploads/files \
    && mkdir -p /var/www/html/uploads/cover /var/www/html/uploads/author_photos /var/www/html/uploads/files \
    && chmod -R 777 /var/www/html/Uploads /var/www/html/uploads

# Expose HTTP Port
EXPOSE 80

# Start Apache Server
CMD ["apache2-foreground"]
