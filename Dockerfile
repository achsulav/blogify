# Stage 1: Build Frontend Assets
FROM node:20-slim AS asset-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Production PHP + Nginx Environment
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    gettext \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Fix path for php-fpm to match potential dashboard overrides
RUN ln -s /usr/local/sbin/php-fpm /usr/sbin/php-fpm



# Set working directory
WORKDIR /var/www

# Copy application files
COPY . /var/www

# Copy built assets from Stage 1
COPY --from=asset-builder /app/public/build /var/www/public/build



# Copy Nginx configuration
COPY docker/nginx/render.conf /etc/nginx/sites-available/default

# Ensure storage and uploads are writable (if they exist)
RUN chmod -R 775 /var/www/public/uploads || true

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Start script
EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]