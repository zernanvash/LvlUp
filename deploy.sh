#!/bin/bash

# LvlUp Deployment Script for Ubuntu Server
# Run with: sudo bash deploy.sh

set -e  # Exit on error

echo "================================================"
echo "🎮 LvlUp - Automated Deployment Script"
echo "================================================"
echo ""

# Color codes
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Variables
DOMAIN=""
DB_NAME="lvlup"
DB_USER="lvlup"
DB_PASS=""
APP_PATH="/var/www/lvlup"

echo -e "${BLUE}Enter your domain name (e.g., lvlup.com):${NC}"
read DOMAIN

echo -e "${BLUE}Enter database password:${NC}"
read -s DB_PASS
echo ""

echo -e "${BLUE}Enter your email for SSL certificate:${NC}"
read EMAIL

echo ""
echo -e "${GREEN}[1/10] Updating system packages...${NC}"
apt update && apt upgrade -y

echo -e "${GREEN}[2/10] Installing Apache...${NC}"
apt install apache2 -y
systemctl start apache2
systemctl enable apache2

echo -e "${GREEN}[3/10] Installing MySQL...${NC}"
apt install mysql-server -y

echo -e "${GREEN}[4/10] Setting up MySQL database...${NC}"
mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME};"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

echo -e "${GREEN}[5/10] Installing PHP 8.2...${NC}"
apt install software-properties-common -y
add-apt-repository ppa:ondrej/php -y
apt update
apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath -y

echo -e "${GREEN}[6/10] Installing Composer...${NC}"
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

echo -e "${GREEN}[7/10] Cloning and setting up Laravel application...${NC}"
if [ -d "$APP_PATH" ]; then
    echo -e "${YELLOW}Directory exists. Pulling latest changes...${NC}"
    cd $APP_PATH
    git pull
else
    echo -e "${BLUE}Enter your Git repository URL:${NC}"
    read REPO_URL
    git clone $REPO_URL $APP_PATH
    cd $APP_PATH
fi

# Install dependencies
composer install --optimize-autoloader --no-dev

# Setup environment
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
fi

# Update .env file
sed -i "s/APP_ENV=.*/APP_ENV=production/" .env
sed -i "s/APP_DEBUG=.*/APP_DEBUG=false/" .env
sed -i "s|APP_URL=.*|APP_URL=https://${DOMAIN}|" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=${DB_NAME}/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=${DB_USER}/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=${DB_PASS}/" .env

echo -e "${GREEN}[8/10] Running migrations...${NC}"
php artisan migrate --force
php artisan db:seed --force

echo -e "${GREEN}[9/10] Setting permissions...${NC}"
chown -R www-data:www-data $APP_PATH
chmod -R 755 $APP_PATH
chmod -R 775 $APP_PATH/storage
chmod -R 775 $APP_PATH/bootstrap/cache

# Create Apache virtual host
echo -e "${GREEN}Configuring Apache...${NC}"
cat > /etc/apache2/sites-available/lvlup.conf <<EOF
<VirtualHost *:80>
    ServerName ${DOMAIN}
    ServerAdmin admin@${DOMAIN}
    DocumentRoot ${APP_PATH}/public

    <Directory ${APP_PATH}/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/lvlup-error.log
    CustomLog \${APACHE_LOG_DIR}/lvlup-access.log combined
</VirtualHost>
EOF

a2ensite lvlup.conf
a2enmod rewrite
a2dissite 000-default.conf
systemctl restart apache2

echo -e "${GREEN}[10/10] Installing SSL certificate...${NC}"
apt install certbot python3-certbot-apache -y
certbot --apache -d $DOMAIN --non-interactive --agree-tos --email $EMAIL

# Cache optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create cron job for scheduled tasks
(crontab -l 2>/dev/null; echo "* * * * * cd ${APP_PATH} && php artisan schedule:run >> /dev/null 2>&1") | crontab -

echo ""
echo -e "${GREEN}================================================${NC}"
echo -e "${GREEN}✅ Deployment Complete!${NC}"
echo -e "${GREEN}================================================${NC}"
echo ""
echo -e "${BLUE}Your LvlUp application is now live at:${NC}"
echo -e "${YELLOW}https://${DOMAIN}${NC}"
echo ""
echo -e "${BLUE}Database Details:${NC}"
echo "  Name: ${DB_NAME}"
echo "  User: ${DB_USER}"
echo ""
echo -e "${BLUE}Application Path:${NC} ${APP_PATH}"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo "1. Register a new account at https://${DOMAIN}/register"
echo "2. Start adding projects and earning XP!"
echo "3. Check logs: tail -f /var/log/apache2/lvlup-error.log"
echo ""
echo -e "${GREEN}Happy LvlUp-ing! 🎮🚀${NC}"
echo ""
