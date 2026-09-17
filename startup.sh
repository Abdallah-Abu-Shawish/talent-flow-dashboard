#!/bin/bash
set -e

echo "=== TalentFlow Azure App Service Startup ==="

# ============================================================
# APPLICATION ROOT
# ============================================================

APP_DIR="/home/site/wwwroot"

# ============================================================
# REQUIRED LARAVEL DIRECTORIES
# ============================================================

echo "Ensuring required storage and cache directories exist..."

mkdir -p "$APP_DIR/storage/app/public" \
         "$APP_DIR/storage/framework/sessions" \
         "$APP_DIR/storage/framework/views" \
         "$APP_DIR/storage/framework/cache" \
         "$APP_DIR/storage/logs" \
         "$APP_DIR/bootstrap/cache"

# ============================================================
# LARAVEL PERMISSIONS
# ============================================================

echo "Setting permissions on storage and bootstrap/cache..."

chmod -R 775 \
    "$APP_DIR/storage" \
    "$APP_DIR/bootstrap/cache" || true

chown -R www-data:www-data \
    "$APP_DIR/storage" \
    "$APP_DIR/bootstrap/cache" || true


# ============================================================
# ANDROID APP LINKS
# /.well-known/assetlinks.json
# ============================================================

echo "Preparing Android App Links verification..."

mkdir -p "$APP_DIR/public/.well-known"

chmod 755 \
    "$APP_DIR/public/.well-known" || true

chown -R www-data:www-data \
    "$APP_DIR/public/.well-known" || true

if [ -f "$APP_DIR/public/.well-known/assetlinks.json" ]; then

    chmod 644 \
        "$APP_DIR/public/.well-known/assetlinks.json" || true

    echo "assetlinks.json found and permissions configured."

else

    echo "WARNING: assetlinks.json not found at:"
    echo "$APP_DIR/public/.well-known/assetlinks.json"

fi


# ============================================================
# NGINX CONFIGURATION
# ============================================================

if [ -f "$APP_DIR/default" ]; then

    echo "Applying custom Nginx configuration from $APP_DIR/default..."

    cp "$APP_DIR/default" \
       /etc/nginx/sites-available/default

    echo "Testing Nginx configuration..."

    nginx -t

    echo "Reloading Nginx..."

    service nginx reload || service nginx restart

else

    echo "WARNING: $APP_DIR/default file not found."
    echo "Using default container Nginx config."

fi


# ============================================================
# DATABASE
# ============================================================

# Database migrations are intentionally skipped.
# TalentFlow connects to the existing Supabase PostgreSQL schema.


echo "=== TalentFlow Startup Completed Successfully ==="