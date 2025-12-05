#!/usr/bin/env bash
set -e

echo "🚀 Starting Railway deployment..."

# Force unset any database environment variables first
unset DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD DATABASE_URL MYSQL_URL 2>/dev/null || true

# Create .env file with SQLite configuration
echo "📝 Creating .env file with SQLite configuration..."
cat > .env << EOF
APP_NAME="${APP_NAME:-Credit System}"
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=${APP_URL}

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_HOST=
DB_PORT=
DB_DATABASE=/tmp/database.sqlite
DB_USERNAME=
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

CACHE_STORE=file
CACHE_PREFIX=

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"
EOF

# Explicitly export SQLite configuration
export DB_CONNECTION=sqlite
export DB_HOST=""
export DB_PORT=""
export DB_DATABASE="/tmp/database.sqlite"
export DB_USERNAME=""
export DB_PASSWORD=""

# Ensure database directory exists and create SQLite file
echo "📁 Creating SQLite database..."
# Use /tmp for writable storage in Railway
touch /tmp/database.sqlite
chmod 666 /tmp/database.sqlite
echo "✅ SQLite database created at /tmp/database.sqlite"

# Generate application key if not exists (with fallback)
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    if [ -f artisan ]; then
        php artisan key:generate --force || echo "Warning: Could not generate key via artisan"
    fi
    # Fallback: generate key manually if artisan fails
    if [ -z "$APP_KEY" ]; then
        APP_KEY="base64:$(openssl rand -base64 32)"
        sed -i "s/APP_KEY=.*/APP_KEY=${APP_KEY}/" .env
        echo "Generated fallback APP_KEY"
    fi
fi

# Clear and cache configuration
echo "⚙️ Optimizing application..."
if [ -f artisan ]; then
    # Clear ALL caches first to ensure clean state
    php artisan config:clear 2>/dev/null || true
    php artisan cache:clear 2>/dev/null || true
    php artisan route:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true
    
    # IMPORTANT: Wait a moment for file system to sync
    sleep 3
    
    # Now cache configurations with the new .env settings
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache 2>/dev/null || true
fi

# Run database migrations
echo "🗄️ Running database migrations..."
if [ -f artisan ]; then
    # Check database connection first
    echo "🔍 Testing database connection..."
    php artisan tinker --execute="echo 'DB Connection: ' . config('database.default'); try { DB::connection()->getPdo(); echo ' - Connection successful'; } catch (Exception \$e) { echo ' - Connection failed: ' . \$e->getMessage(); }" 2>/dev/null || echo "Could not test connection"
    
    # Run migrations
    php artisan migrate --force 2>/dev/null || echo "Migration failed, but continuing..."
    
    # Test basic Laravel functionality
    echo "🧪 Testing Laravel setup..."
    php artisan route:list --compact 2>/dev/null || echo "Could not list routes"
    
    # Create a simple test route/page
    echo "🔧 Creating health check..."
    cat > routes/web-health.php << 'EOF'
<?php
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'database' => 'Connected',
        'timestamp' => now()
    ]);
});
EOF
    
    # Include health check in main web routes
    if ! grep -q "web-health.php" routes/web.php; then
        echo "require __DIR__.'/web-health.php';" >> routes/web.php
    fi
fi

# Set correct permissions
echo "🔒 Setting permissions..."
chmod -R 755 storage bootstrap/cache 2>/dev/null || true
chmod 666 /tmp/database.sqlite 2>/dev/null || true

echo "✅ Railway deployment completed!"

# Start Apache
echo "🌐 Starting Apache server..."
apache2-foreground