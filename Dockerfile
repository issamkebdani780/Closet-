FROM php:8.2-apache

# Install mysqli extension to connect to the database
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache URL rewriting (good practice)
RUN a2enmod rewrite

# Copy all your PHP/HTML files to the Apache server folder
COPY . /var/www/html/

# Create the uploads directory and give it read/write permissions
RUN mkdir -p /var/www/html/uploads && chmod -R 777 /var/www/html/uploads

# Expose port 80 for Railway
EXPOSE 80
