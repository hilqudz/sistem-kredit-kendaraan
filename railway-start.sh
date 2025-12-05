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

DB_CONNECTION=sqlite
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

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

# Wait for database to be ready
echo "⏳ Waiting for database..."
if [ -n "$DB_HOST" ] && [ -n "$DB_USERNAME" ]; then
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
            echo "🔄 Falling back to SQLite..."
            # Update .env to use SQLite
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

DB_CONNECTION=sqlite
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

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
            # Ensure database directory exists
            mkdir -p database
            touch database/database.sqlite
            chmod 666 database/database.sqlite
            echo "✅ SQLite fallback configured"
            break
        fi
        echo "Database not ready, waiting... (attempt $attempt/$max_attempts)"
        sleep 10
    done
else
    echo "No database configuration found, using SQLite fallback..."
    # Ensure database directory exists
    mkdir -p database
    # Create SQLite database file
    touch database/database.sqlite
    chmod 666 database/database.sqlite
    echo "✅ SQLite configured as fallback"
fi

# Clear and cache configuration
echo "⚙️ Optimizing application..."
if [ -f artisan ]; then
    # Clear cache first to ensure clean state
    php artisan config:clear 2>/dev/null || true
    php artisan cache:clear 2>/dev/null || true
    php artisan route:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true
    
    # Cache configurations
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
fi

# Set correct permissions
echo "🔒 Setting permissions..."
chmod -R 755 storage bootstrap/cache 2>/dev/null || true

echo "✅ Railway deployment completed!"

# Start Apache
echo "🌐 Starting Apache server..."
apache2-foreground