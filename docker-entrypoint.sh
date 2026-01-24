#!/bin/bash
# Startup script for Logistics Management App

# Ensure data directory exists
mkdir -p /var/www/html/data/mongodb
chmod -R 777 /var/www/html/data

# Initialize database if not already done
if [ ! -f /var/www/html/data/mongodb/admin.json ]; then
    php /var/www/html/setup-mongodb.php > /dev/null 2>&1
    echo "Database initialized with admin user"
fi

# Start Apache
exec apache2-foreground
