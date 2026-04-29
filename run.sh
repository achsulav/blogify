#!/bin/bash

# Blogify Local Runner
# Stack: System Nginx + PHP-FPM + MariaDB + Dnsmasq + Vite
# No Docker, No Traefik, No PHP built-in server.


PROJECT_ROOT=$(pwd)

stop_all() {
    echo "🛑 Stopping all services..."
    sudo systemctl stop nginx    > /dev/null 2>&1 || true
    sudo systemctl stop mariadb  > /dev/null 2>&1 || true
    sudo systemctl stop php-fpm  > /dev/null 2>&1 || true
    pkill -f "npm run dev"       > /dev/null 2>&1 || true
    echo "✅ All services stopped."
}

# ── --stop flag ────────────────────────────────────────────────────────────────
if [[ "$1" == "--stop" ]]; then
    stop_all
    exit 0
fi

# ── Startup ────────────────────────────────────────────────────────────────────
echo ""
echo "🚀 Starting Blogify (Nginx + PHP-FPM + MariaDB + Dnsmasq)"
echo "──────────────────────────────────────────────────────────"

# 1. Stop anything that might conflict
echo "🧹 Cleaning up old processes..."
sudo systemctl stop nginx             > /dev/null 2>&1 || true
sudo systemctl stop mariadb           > /dev/null 2>&1 || true
# Kill Traefik so it stops holding ports 80/443
sudo pkill -f traefik                 > /dev/null 2>&1 || true
pkill -f "npm run dev"                > /dev/null 2>&1 || true
pkill -f "composer serve"             > /dev/null 2>&1 || true
pkill -f "php -S"                     > /dev/null 2>&1 || true
sleep 2  # wait for ports to be fully released

# 2. Check .env
if [ ! -f "$PROJECT_ROOT/.env" ]; then
    echo "📄 .env not found, copying from .env.example..."
    cp "$PROJECT_ROOT/.env.example" "$PROJECT_ROOT/.env"
fi

# 3. Start MariaDB
echo "🗄️  Starting MariaDB..."
sudo systemctl start mariadb
sleep 1
if systemctl is-active --quiet mariadb; then
    echo "   ✅ MariaDB is running"
else
    echo "   ❌ MariaDB failed to start. Check: sudo journalctl -u mariadb -n 20"
    exit 1
fi

# 4. Start PHP-FPM
echo "🐘 Starting PHP-FPM..."
sudo systemctl start php-fpm
sleep 1
if systemctl is-active --quiet php-fpm; then
    echo "   ✅ PHP-FPM is running"
else
    echo "   ❌ PHP-FPM failed to start. Check: sudo journalctl -u php-fpm -n 20"
    exit 1
fi

# 6. Verify nginx config then start Nginx
echo "🌍 Starting Nginx..."
sudo nginx -t > /dev/null 2>&1 || { echo "   ❌ Nginx config error. Run: sudo nginx -t"; exit 1; }
sudo systemctl start nginx
sleep 1
if systemctl is-active --quiet nginx; then
    echo "   ✅ Nginx is running (ports 80 + 443)"
else
    echo "   ❌ Nginx failed to start. Check: sudo journalctl -u nginx -n 20"
    exit 1
fi

# 7. Start Vite asset server (background)
echo "⚡ Starting Vite..."
nohup npm run dev > vite.log 2>&1 &
VITE_PID=$!
sleep 2
if kill -0 $VITE_PID 2>/dev/null; then
    echo "   ✅ Vite is running (PID $VITE_PID)"
else
    echo "   ⚠️  Vite may have failed. Check: tail -f vite.log"
fi

# ── Done ───────────────────────────────────────────────────────────────────────
echo ""
echo "──────────────────────────────────────────────────────────"
echo "  ✨ Blogify is ready!"
echo "──────────────────────────────────────────────────────────"
echo "  🔗 URL      : https://blogify.dev"
echo "  📋 Vite log : tail -f $PROJECT_ROOT/vite.log"
echo "  🛑 To stop  : ./run.sh --stop"
echo "──────────────────────────────────────────────────────────"
echo ""

# Keep script alive so Vite log is available; Ctrl+C stops Vite cleanly.
wait $VITE_PID
