FROM php:8.2-apache

# Install mysqli extension to connect to the database
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache URL rewriting (good practice)
RUN a2enmod rewrite

# FIX for Railway: Disable conflicting MPM modules to prevent AH00534 error
RUN a2dismod mpm_event mpm_worker || true
RUN a2enmod mpm_prefork

# Copy all your PHP/HTML files to the Apache server folder
COPY . /var/www/html/

# Create the uploads directory and give it read/write permissions
RUN mkdir -p /var/www/html/uploads && chmod -R 777 /var/www/html/uploads

# Set the start command to use Railway's dynamic PORT variable before starting Apache
CMD sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && apache2-foreground
