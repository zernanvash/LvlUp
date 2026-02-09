# 🎮 LvlUp - Gamified Knowledge Management System

A revolutionary web-based Information Management System that transforms skill acquisition into an RPG-like experience. Track your growth through an interactive skill tree, earn achievements, and generate AI-powered resumes - all while leveling up your developer profile.

![Version](https://img.shields.io/badge/version-2.0.0-purple)
![Laravel](https://img.shields.io/badge/laravel-11+-red)
![PHP](https://img.shields.io/badge/php-8.2+-blue)
![License](https://img.shields.io/badge/license-MIT-green)

## 🌟 Features

### Core Gamification
- **Experience Points (XP)**: Earn XP for every project you add
- **Level System**: Progress through levels with increasing XP requirements
- **Skill Points**: Unlock powerful skills in the skill tree
- **Gacha Currency (Primogems)**: Earn premium currency through daily logins and achievements
- **Rank System**: Bronze → Silver → Gold → Platinum → Diamond → Master

### Interactive Skill Tree
- **Node-based visualization**: See your progression path clearly
- **Parent-child dependencies**: Unlock advanced skills by mastering basics
- **Tier system**: Core → Basic → Advanced → Master → Legendary skills
- **Dynamic unlocking**: Spend skill points to unlock new abilities

### Achievement Engine
- **Badge system**: Earn badges for milestones (projects, skills, streaks)
- **Rarity tiers**: Common → Rare → Epic → Legendary → Mythic
- **Automatic triggers**: Database events unlock achievements automatically
- **Rewards**: Each badge grants XP and Primogems

### AI-Powered Resume Builder
- **Smart matching**: Paste a job description and get matched projects
- **Keyword analysis**: AI filters your best work for each application
- **Auto-generation**: Creates professional PDFs with selected content
- **Match scoring**: See how well you fit the role (percentage)

### Daily Rewards System
- **Login streaks**: Build momentum with consecutive day bonuses
- **Escalating rewards**: More rewards for longer streaks (7-day milestones)
- **Visual feedback**: Gacha-style reward animations

## 🎨 Design Philosophy

**Inspired by**: Genshin Impact, Honkai Star Rail, and modern gacha games

**Aesthetic Features**:
- Animated gradient backgrounds with particle effects
- Star-field backdrop for depth
- Glow effects for rarity tiers
- Shimmer animations on XP bars
- Smooth transitions and hover effects
- Custom fonts: Orbitron (display) + Rajdhani (body)

## 🛠️ Tech Stack

- **Framework**: Laravel 11+
- **Database**: MySQL
- **Frontend**: Tailwind CSS + Alpine.js
- **Icons**: Font Awesome 6
- **Fonts**: Google Fonts (Orbitron, Rajdhani)

## 📦 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 8.0+
- Node.js 18+ (optional, for asset compilation)

### Local Setup

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/lvlup.git
cd lvlup
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database** (Edit `.env`)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lvlup
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Seed database** (optional)
```bash
php artisan db:seed
```

7. **Start development server**
```bash
php artisan serve
```

Visit: `http://localhost:8000`

## 🚀 Ubuntu Server Deployment

### Option 1: Traditional LAMP Stack

#### 1. Update system
```bash
sudo apt update && sudo apt upgrade -y
```

#### 2. Install LAMP
```bash
# Apache
sudo apt install apache2 -y

# MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation

# PHP 8.2+
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd -y
```

#### 3. Install Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### 4. Setup project
```bash
cd /var/www
sudo git clone https://github.com/yourusername/lvlup.git
cd lvlup
sudo composer install --optimize-autoloader --no-dev
```

#### 5. Configure permissions
```bash
sudo chown -R www-data:www-data /var/www/lvlup
sudo chmod -R 755 /var/www/lvlup
sudo chmod -R 775 /var/www/lvlup/storage
sudo chmod -R 775 /var/www/lvlup/bootstrap/cache
```

#### 6. Setup environment
```bash
sudo cp .env.example .env
sudo nano .env  # Edit with your production settings
php artisan key:generate
php artisan migrate --force
```

#### 7. Configure Apache
```bash
sudo nano /etc/apache2/sites-available/lvlup.conf
```

Add:
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    ServerAdmin admin@your-domain.com
    DocumentRoot /var/www/lvlup/public

    <Directory /var/www/lvlup/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/lvlup-error.log
    CustomLog ${APACHE_LOG_DIR}/lvlup-access.log combined
</VirtualHost>
```

Enable site:
```bash
sudo a2ensite lvlup.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### 8. Setup SSL (Let's Encrypt)
```bash
sudo apt install certbot python3-certbot-apache -y
sudo certbot --apache -d your-domain.com
```

### Option 2: Docker Deployment

Create `docker-compose.yml`:
```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: lvlup-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    networks:
      - lvlup-network
    environment:
      - DB_HOST=db
      - DB_DATABASE=lvlup
      - DB_USERNAME=lvlup
      - DB_PASSWORD=secret

  webserver:
    image: nginx:alpine
    container_name: lvlup-nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx:/etc/nginx/conf.d
    networks:
      - lvlup-network

  db:
    image: mysql:8.0
    container_name: lvlup-db
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: lvlup
      MYSQL_USER: lvlup
      MYSQL_PASSWORD: secret
      MYSQL_ROOT_PASSWORD: rootsecret
    volumes:
      - dbdata:/var/lib/mysql
    networks:
      - lvlup-network

networks:
  lvlup-network:
    driver: bridge

volumes:
  dbdata:
```

Deploy:
```bash
docker-compose up -d
docker-compose exec app php artisan migrate --force
```

### Option 3: Cloud Platforms

#### DigitalOcean App Platform
1. Connect GitHub repo
2. Set build command: `composer install && php artisan migrate --force`
3. Set run command: `heroku-php-apache2 public/`

#### AWS EC2
Follow LAMP stack instructions, then:
- Configure Security Groups (ports 80, 443)
- Setup Elastic IP
- Configure RDS for MySQL (recommended)

#### Heroku
```bash
heroku create lvlup-app
heroku addons:create cleardb:ignite
heroku config:set APP_KEY=$(php artisan key:generate --show)
git push heroku main
heroku run php artisan migrate --force
```

## 📊 Database Schema

### Core Tables

**users**: User profiles with gamification stats
- id, name, email, password
- level, xp, total_xp, skill_points
- rank, gacha_currency, streak_days
- avatar, bio, title

**skills**: Technology/skill definitions
- id, name, slug, icon, color
- category, rarity, required_level

**projects**: User portfolio items
- id, user_id, name, description
- url, github_url, language, thumbnail
- xp_reward, is_featured

**skill_nodes**: Skill tree visualization data
- id, skill_id, parent_node_id
- title, description, x_position, y_position
- tier, required_level, skill_point_cost

**badges**: Achievement definitions
- id, title, slug, description, icon
- rarity, category, required_skill_id
- threshold, xp_reward, gacha_currency_reward

**resumes**: AI-generated resume records
- id, user_id, job_title
- target_keywords, job_description
- selected_project_ids, selected_skill_ids
- pdf_path, match_score

### Pivot Tables
- project_skill: Links projects to skills
- user_badges: Tracks earned badges
- user_skill_nodes: Tracks unlocked skill nodes
- daily_rewards: Login streak rewards

## 🎮 Gamification Formulas

### XP Requirements
```php
// XP needed for next level
$xp = 100 * pow($level, 1.5);

// Level 1 → 2: 100 XP
// Level 10 → 11: 3,162 XP
// Level 50 → 51: 35,355 XP
```

### Rank Progression
- **Bronze**: Levels 1-9
- **Silver**: Levels 10-24
- **Gold**: Levels 25-49
- **Platinum**: Levels 50-74
- **Diamond**: Levels 75-99
- **Master**: Level 100+

### Daily Rewards
```php
$baseXP = 50;
$baseCurrency = 20;
$multiplier = floor($streakDays / 7) + 1;

// Day 1-6: 50 XP, 20 Primogems
// Day 7-13: 100 XP, 40 Primogems
// Day 14-20: 150 XP, 60 Primogems
```

## 🔧 Configuration

### Environment Variables
```env
# App
APP_NAME=LvlUp
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lvlup
DB_USERNAME=root
DB_PASSWORD=

# Mail (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null

# Optional: Queue (for heavy tasks)
QUEUE_CONNECTION=database
```

### Performance Optimization
```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload -o

# Enable OPcache (php.ini)
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

## 📱 API Endpoints (Future)

```
GET    /api/user                 - Get user profile
POST   /api/projects              - Create project
GET    /api/skills                - List all skills
POST   /api/skill-tree/unlock     - Unlock skill node
GET    /api/achievements          - Get badges
POST   /api/daily-reward/claim    - Claim daily reward
POST   /api/resume/generate       - Generate resume
```

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📝 License

This project is licensed under the MIT License.

## 👥 Credits

**Developers**:
- Jerico F. Abulencia
- Zernan Vash Arrive

**Course**: Software Engineering 2 (32-CSE-01)
**Date**: January 2, 2026

## 🎯 Roadmap

- [ ] Mobile app (React Native/Flutter)
- [ ] Social features (friend system, leaderboards)
- [ ] Actual gacha system for skill unlocks
- [ ] Integration with GitHub API for auto-project import
- [ ] AI chatbot career advisor
- [ ] Skill recommendations based on job market
- [ ] Export portfolio as PDF/website
- [ ] Multi-language support

## 🐛 Troubleshooting

### Common Issues

**500 Error on Laravel**
```bash
php artisan cache:clear
php artisan config:clear
chmod -R 775 storage bootstrap/cache
```

**Database connection failed**
- Check MySQL service: `sudo systemctl status mysql`
- Verify .env credentials
- Test connection: `php artisan tinker` → `DB::connection()->getPdo();`

**Permissions errors**
```bash
sudo chown -R www-data:www-data /var/www/lvlup
sudo chmod -R 755 /var/www/lvlup
sudo chmod -R 775 storage bootstrap/cache
```

## 📧 Support

For issues or questions:
- Open an issue on GitHub
- Email: support@lvlup.dev
- Discord: [Join our server](#)

---

Made with 💜 by Group 2
