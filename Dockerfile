# Use official PHP Apache image
FROM php:8.1-apache

# Install MySQL extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy application files to container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Give Apache permission to read/write files
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]