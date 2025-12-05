#!/usr/bin/env bash
set -e

echo "🚀 Starting Railway deployment..."

# Copy environment file if not exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
fi

# Generate application key if not exists
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Wait for database to be ready
echo "⏳ Waiting for database..."
until php artisan migrate:status > /dev/null 2>&1; do
  echo "Database not ready, waiting..."
  sleep 5
done

# Clear and cache configuration
echo "⚙️ Optimizing application..."
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Set correct permissions
echo "🔒 Setting permissions..."
chmod -R 755 storage bootstrap/cache

echo "✅ Railway deployment completed!"

# Start Apache
echo "🌐 Starting Apache server..."
apache2-foreground