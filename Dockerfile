FROM php:8.2-cli

# Install Node.js for admin dependencies
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && \
    apt-get clean

# Set working directory
WORKDIR /app

# Copy all files
COPY . .

# Install admin dependencies
RUN cd admin && npm ci --prefer-offline --no-audit

# Expose port
EXPOSE 8080

# Start PHP server
CMD php -S 0.0.0.0:8080 index.php
