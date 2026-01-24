FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo pdo_mysql pdo_pgsql pgsql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node.js for admin dependencies
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy all files
COPY . /var/www/html/

# Create MongoDB data directory and initialize data
RUN mkdir -p /var/www/html/data/mongodb && \
    chown -R www-data:www-data /var/www/html/data

# Initialize MongoDB collections with admin user and settings
RUN php /var/www/html/setup-mongodb.php > /dev/null 2>&1 || true

# Install admin dependencies
RUN cd admin && npm ci --prefer-offline --no-audit || true

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 777 /var/www/html/data && \
    chmod +x /var/www/html/docker-entrypoint.sh

# Expose port
EXPOSE 80

# Start with entrypoint script
CMD ["/var/www/html/docker-entrypoint.sh"]
