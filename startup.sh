#!/bin/bash
set -e

echo "=== TalentFlow Azure App Service Startup ==="

# Define application root directory
APP_DIR="/home/site/wwwroot"

# Ensure required storage and bootstrap cache directories exist
echo "Ensuring required storage and cache directories exist..."
mkdir -p "$APP_DIR/storage/app/public" \
         "$APP_DIR/storage/framework/sessions" \
         "$APP_DIR/storage/framework/views" \
         "$APP_DIR/storage/framework/cache" \
         "$APP_DIR/storage/logs" \
         "$APP_DIR/bootstrap/cache"

# Ensure storage and bootstrap cache directories are writable by web process
echo "Setting permissions on storage and bootstrap/cache..."
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" || true
chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" || true

# Apply custom Nginx configuration if present
if [ -f "$APP_DIR/default" ]; then
    echo "Applying custom Nginx configuration from $APP_DIR/default..."
    cp "$APP_DIR/default" /etc/nginx/sites-available/default
    service nginx reload || service nginx restart
else
    echo "WARNING: $APP_DIR/default file not found. Using default container Nginx config."
fi

# NOTE: Database migrations are intentionally skipped here because the application
# connects to an existing Supabase PostgreSQL database schema.

echo "=== TalentFlow Startup Completed Successfully ==="
