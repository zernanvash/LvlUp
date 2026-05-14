FROM php:8.2-apache

WORKDIR /var/www/html

ENV PUPPETEER_SKIP_DOWNLOAD=true
ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium

RUN apt-get update && apt-get install -y \
    ca-certificates \
    curl \
    git \
    gnupg \
    unzip \
    zip \
    chromium \
    fonts-liberation \
    libasound2 \
    libatk-bridge2.0-0 \
    libatk1.0-0 \
    libcups2 \
    libdrm2 \
    libgbm1 \
    libgtk-3-0 \
    libjpeg-dev \
    libnss3 \
    libonig-dev \
    libpng-dev \
    libpq-dev \
    libxcomposite1 \
    libxdamage1 \
    libxfixes3 \
    libxml2-dev \
    libxrandr2 \
    libzip-dev \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install bcmath exif gd mbstring pcntl pdo_mysql pdo_pgsql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

RUN if [ -f package-lock.json ]; then npm ci; elif [ -f package.json ]; then npm install; fi \
    && if [ -f package.json ]; then npm run build; fi

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x start.sh \
    && a2enmod rewrite

COPY apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["bash", "start.sh"]
