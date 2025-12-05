#!/usr/bin/env bash
set -e

echo "🚀 Starting Railway deployment..."

# Create .env file with Railway environment variables
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cat > .env << EOF
APP_NAME="${APP_NAME:-Credit System}"
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=${APP_URL}

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"
EOF
fi

# Generate application key if not exists
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Wait for database to be ready
echo "⏳ Waiting for database..."
max_attempts=30
attempt=0
until php -r "
try {
    \$pdo = new PDO('mysql:host=${DB_HOST};port=${DB_PORT:-3306}', '${DB_USERNAME}', '${DB_PASSWORD}');
    echo 'Database connection successful';
    exit(0);
} catch (Exception \$e) {
    echo 'Database connection failed: ' . \$e->getMessage();
    exit(1);
}
" > /dev/null 2>&1; do
    attempt=$((attempt + 1))
    if [ $attempt -ge $max_attempts ]; then
        echo "❌ Database connection timeout after $max_attempts attempts"
        break
    fi
    echo "Database not ready, waiting... (attempt $attempt/$max_attempts)"
    sleep 10
done

# Clear and cache configuration
echo "⚙️ Optimizing application..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan config:cache
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Set correct permissions
echo "🔒 Setting permissions..."
chmod -R 755 storage bootstrap/cache 2>/dev/null || true

echo "✅ Railway deployment completed!"

# Start Apache
echo "🌐 Starting Apache server..."
apache2-foreground