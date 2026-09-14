# TalentFlow Dashboard — Azure App Service Deployment Guide

This document contains step-by-step instructions for deploying the **TalentFlow Laravel 12 Dashboard** (`system-dashboard/`) to **Azure App Service Linux** running PHP 8.2+.

---

## 1. Overview & Architecture

- **Runtime**: Azure App Service Linux (PHP 8.2+ built-in container)
- **Web Server**: Nginx with PHP-FPM
- **Document Root**: `/home/site/wwwroot/public`
- **Database**: Existing Supabase PostgreSQL (`pgsql` connection driver)
- **Authentication**: Supabase Auth (via API / client tokens)
- **Migrations**: **NOT run automatically** on App Service startup. Database schema is managed via Supabase.

---

## 2. Nginx Configuration (`default`)

Azure App Service Linux PHP container defaults to serving `/home/site/wwwroot`. Laravel requires the document root to be `/home/site/wwwroot/public`.

The repository contains a custom Nginx configuration file named `default` at the project root:

```nginx
server {
    listen 80 default_server;
    listen [::]:80 default_server;

    root /home/site/wwwroot/public;
    index index.php index.html index.htm;

    server_name _;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        try_files $fastcgi_script_name =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    location ~ /\. {
        deny all;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }

    gzip on;
    gzip_comp_level 5;
    gzip_min_length 256;
    gzip_types application/javascript application/json application/xml text/css text/plain text/xml;
}
```

---

## 3. Azure App Service Startup Command

In the Azure Portal, navigate to:
**App Service > Settings > Configuration > General settings > Startup Command**

Enter the following Startup Command:

```bash
bash /home/site/wwwroot/startup.sh
```

*(Alternatively: `cp /home/site/wwwroot/default /etc/nginx/sites-available/default && service nginx reload`)*

The `startup.sh` script automatically:
1. Creates required writable directories: `storage/framework/{sessions,views,cache}`, `storage/logs`, and `bootstrap/cache`.
2. Sets proper permissions (775) on `storage/` and `bootstrap/cache/`.
3. Copies `default` to `/etc/nginx/sites-available/default` and reloads Nginx.
4. **Does NOT run database migrations** (`php artisan migrate`).

---

## 4. Required Azure Environment Variables (App Settings)

Configure the following application settings in **App Service > Settings > Environment variables**:

| Variable Name | Example Value | Description |
| :--- | :--- | :--- |
| `APP_NAME` | `TalentFlow Dashboard` | Application display name |
| `APP_ENV` | `production` | Production environment flag |
| `APP_KEY` | `base64:...` | 32-character encryption key (`php artisan key:generate --show`) |
| `APP_DEBUG` | `false` | Must be `false` in production |
| `APP_URL` | `https://<your-app-name>.azurewebsites.net` | Canonical production URL |
| `DB_CONNECTION` | `pgsql` | Database driver (PostgreSQL) |
| `DB_HOST` | `aws-0-us-east-1.pooler.supabase.com` | Supabase DB host / connection pooler |
| `DB_PORT` | `6543` | Connection pooler port (or `5432` direct) |
| `DB_DATABASE` | `postgres` | Supabase database name |
| `DB_USERNAME` | `postgres.<project-ref>` | Supabase database username |
| `DB_PASSWORD` | `<your-db-password>` | Supabase database password |
| `DB_SSLMODE` | `require` | Enforce SSL for PostgreSQL connection |
| `SESSION_DRIVER` | `database` | Storage for HTTP sessions |
| `SESSION_LIFETIME` | `120` | Session timeout in minutes |
| `SESSION_SECURE_COOKIE` | `true` | Send cookies over HTTPS only |
| `CACHE_STORE` | `database` | Cache storage backend |
| `QUEUE_CONNECTION` | `database` | Queue worker engine |
| `LOG_CHANNEL` | `stderr` | Route application logs to Azure Log Stream |
| `SUPABASE_URL` | `https://<project-ref>.supabase.co` | Supabase project API URL |
| `SUPABASE_ANON_KEY` | `<your-anon-key>` | Supabase public anonymous API key |
| `SUPABASE_SERVICE_ROLE_KEY` | `<your-service-role-key>` | Server-side only Supabase service role key |

> [!WARNING]
> Never commit `.env` files or expose Supabase service role keys to browser clients. All secrets MUST be set via Azure App Service environment variables.

---

## 5. Deployment Build & Optimization Steps

Run the following optimization commands during build/deployment pipeline (e.g. GitHub Actions, Azure DevOps, or local deployment prep):

```bash
# 1. Install production PHP dependencies without dev packages
composer install --no-dev --optimize-autoloader

# 2. Install Node dependencies and compile frontend assets
npm ci
npm run build

# 3. Cache production configurations, routes, and views
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> [!NOTE]
> All application code retrieves environment variables via `config(...)` helpers instead of calling `env(...)` directly outside `config/*.php`, guaranteeing compatibility with `php artisan config:cache`.

---

## 6. Pre-Flight Deployment Checklist

- [ ] `.env` is listed in `.gitignore` and ignored by Git.
- [ ] Custom Nginx configuration file `default` exists at the project root.
- [ ] Startup script `startup.sh` exists at the project root and is executable.
- [ ] Startup Command in Azure Portal is set to `bash /home/site/wwwroot/startup.sh`.
- [ ] App Settings in Azure Portal are populated with production credentials (`APP_KEY`, Supabase DB, Supabase Auth keys).
- [ ] Database migrations are **skipped** on startup to preserve Supabase schema contract.
- [ ] Production build assets compiled via `npm run build`.
- [ ] Caches generated via `config:cache`, `route:cache`, and `view:cache`.
- [ ] `APP_DEBUG` is set to `false`.
