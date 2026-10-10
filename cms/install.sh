#!/bin/bash
# ─────────────────────────────────────────────────────────────────────────────
# Newsportal CMS — Fresh Install Script
# Run: bash install.sh
# ─────────────────────────────────────────────────────────────────────────────

set -e  # Exit immediately on any error

echo ""
echo "╔══════════════════════════════════════════════╗"
echo "║       Newsportal CMS — Install Script        ║"
echo "╚══════════════════════════════════════════════╝"
echo ""

# ── Step 1: Install PHP dependencies ──────────────────────────────────────────
echo "📦 Installing PHP dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# ── Step 2: Setup environment ─────────────────────────────────────────────────
if [ ! -f ".env" ]; then
    echo "⚙️  Creating .env file..."
    cp .env.example .env
    php artisan key:generate
    echo "✅ .env created. Please configure DB, Redis, and other settings."
    echo "   Then re-run: bash install.sh"
    exit 0
fi

# ── Step 3: Install frontend dependencies ─────────────────────────────────────
echo "📦 Installing Node.js dependencies..."
npm install --ignore-scripts

# ── Step 4: Run migrations ────────────────────────────────────────────────────
echo "🗃️  Running database migrations..."
php artisan migrate --force

# ── Step 5: Seed initial data ─────────────────────────────────────────────────
echo "🌱 Seeding initial data..."
php artisan db:seed --class=DatabaseSeeder --force

# ── Step 6: Publish vendor assets ─────────────────────────────────────────────
echo "📂 Publishing vendor assets..."
php artisan vendor:publish --tag=sanctum-migrations --force
php artisan vendor:publish --tag=spatie-permission-migrations --force
php artisan vendor:publish --tag=medialibrary-migrations --force
php artisan vendor:publish --tag=activitylog-migrations --force
php artisan vendor:publish --tag=horizon-assets --ansi --force
php artisan vendor:publish --tag=inertia-config --force

# Re-run migrations after vendor publishes
php artisan migrate --force

# ── Step 7: Create storage symlink ────────────────────────────────────────────
echo "🔗 Creating storage symlink..."
php artisan storage:link

# ── Step 8: Build frontend assets ─────────────────────────────────────────────
echo "🏗️  Building frontend assets..."
npm run build

# ── Step 9: Cache for performance ─────────────────────────────────────────────
echo "⚡ Caching config, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "╔══════════════════════════════════════════════╗"
echo "║          ✅ Installation Complete!           ║"
echo "║                                              ║"
echo "║  Run: php artisan serve                      ║"
echo "║  Dashboard: http://localhost:8000/dashboard  ║"
echo "╚══════════════════════════════════════════════╝"
echo ""
