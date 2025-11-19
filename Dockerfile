FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    unzip \
    git \
    curl \
    libxml2-dev \
    libssl-dev \
    libicu-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql zip bcmath intl \
    && docker-php-ext-enable pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies for production
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Create JWT directory
RUN mkdir -p config/jwt

# Generate JWT keys (will be overridden by host-mounted keys in production)
RUN openssl genrsa -out config/jwt/private.pem 2048 && \
    openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod 600 config/jwt/private.pem \
    && chmod 644 config/jwt/public.pem

# Enable Apache modules and configure
RUN a2enmod rewrite && \
    a2dissite 000-default.conf && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Configure Apache for Symfony and port 8000
RUN sed -i 's/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/html\/public/' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's/Listen 80/Listen 8000/' /etc/apache2/ports.conf

# Expose port
EXPOSE 8000

# Start Apache in foreground
CMD ["apache2-foreground"]
