#!/bin/bash

# Blogify One-Command Setup Script

echo "🚀 Starting Blogify setup..."

# 1. Generate SSL Certificates if they don't exist
if [ ! -f app/SSL/_wildcard.blogify.dev+1.pem ]; then
    echo "🔐 Generating SSL certificates..."
    mkdir -p app/SSL
    mkcert -cert-file app/SSL/_wildcard.blogify.dev+1.pem -key-file app/SSL/_wildcard.blogify.dev+1-key.pem "*.blogify.dev" blogify.dev
else
    echo "✅ SSL certificates already exist."
fi

# 2. Check for Port 80 Conflict
if ss -tuln | grep -q -E ":80\b" ; then
    echo "⚠️  Error: Port 80 is already in use by another process (likely local Nginx)."
    echo "👉 Run 'sudo systemctl stop nginx' to fix this."
    exit 1
fi

# 3. Start Docker Containers
echo "🐳 Starting Docker containers..."
docker-compose up -d

# 4. Wait for DB to be ready (Dynamic Wait)
echo "⏳ Waiting for database to be ready..."
MAX_RETRIES=30
COUNT=0
until docker exec blogify_php php -r "new PDO('mysql:host=mysql;dbname=blogify', 'phpuser', 'root');" > /dev/null 2>&1 || [ $COUNT -eq $MAX_RETRIES ]; do
    sleep 2
    COUNT=$((COUNT + 1))
    echo "Retrying database connection... ($COUNT/$MAX_RETRIES)"
done

if [ $COUNT -eq $MAX_RETRIES ]; then
    echo "❌ Database took too long to start. Check 'docker logs blogify-mysql-1'"
    exit 1
fi

echo "✅ Database is ready!"

# 5. Run Migrations and Seeds
echo "📂 Setting up database schema..."
docker exec blogify_php php migrate.php
docker exec blogify_php php seed_categories.php

echo "✨ Setup complete! Access your project at https://blogify.dev or http://localhost"
