# 🚀 LvlUp Quick Start Guide

Get your gamified portfolio up and running in minutes!

## 🎯 Choose Your Deployment Path

### Path 1: Local Development (Fastest)

**Prerequisites**: PHP 8.2+, Composer, MySQL

```bash
# 1. Clone the repository
git clone https://github.com/yourusername/lvlup.git
cd lvlup

# 2. Install dependencies
composer install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure database in .env
DB_DATABASE=lvlup
DB_USERNAME=root
DB_PASSWORD=your_password

# 5. Run migrations and seed data
php artisan migrate
php artisan db:seed

# 6. Start server
php artisan serve
```

Visit: `http://localhost:8000`

### Path 2: Ubuntu Server (Production)

**One-Line Install** (Root access required):

```bash
# Download and run deployment script
wget https://raw.githubusercontent.com/yourusername/lvlup/main/deploy.sh
sudo bash deploy.sh
```

The script will:
- ✅ Install Apache, PHP 8.2, MySQL
- ✅ Clone and configure the application
- ✅ Setup database automatically
- ✅ Configure SSL with Let's Encrypt
- ✅ Optimize for production

### Path 3: Docker (Cross-Platform)

```bash
# 1. Clone repository
git clone https://github.com/yourusername/lvlup.git
cd lvlup

# 2. Start containers
docker-compose up -d

# 3. Run migrations
docker-compose exec app php artisan migrate --seed
```

Visit: `http://localhost`

---

## 🎮 First Steps After Installation

### 1. Register Your Account
- Navigate to `/register`
- Create your account
- Claim your first daily reward! (+50 XP, +20 Primogems)

### 2. Create Your First Project
- Click "New Project" on dashboard
- Fill in project details
- Paste some code to auto-detect skills
- Submit and earn 100-500 XP!

### 3. Unlock Skills
- Visit the Skill Tree (`/skill-tree`)
- Spend your skill points
- Unlock powerful abilities

### 4. Earn Achievements
- Create 5 projects → "Portfolio Builder" badge
- Login 7 days straight → "Committed" badge
- Reach level 10 → Unlock advanced skills

---

## 🛠️ Customization

### Change App Name
Edit `.env`:
```env
APP_NAME="YourName's Portfolio"
```

### Customize Colors
Edit `resources/views/layouts/app.blade.php`:
```css
/* Change primary gradient */
from-purple-600 to-pink-600
/* To your colors */
from-blue-600 to-cyan-600
```

### Add More Skills
Run in Tinker:
```php
php artisan tinker

Skill::create([
    'name' => 'Kubernetes',
    'slug' => 'kubernetes',
    'icon' => 'fas fa-dharmachakra',
    'category' => 'devops',
    'rarity' => 'legendary'
]);
```

---

## 📊 Admin Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Re-optimize for production
php artisan optimize

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Create a new admin user
php artisan tinker
>>> $user = User::create([...]);
>>> $user->level = 100;
>>> $user->save();
```

---

## 🐛 Troubleshooting

### "500 Server Error"
```bash
chmod -R 775 storage bootstrap/cache
php artisan cache:clear
```

### "Database Connection Failed"
- Check MySQL is running: `sudo systemctl status mysql`
- Verify credentials in `.env`
- Test: `mysql -u lvlup -p`

### "Page Not Found (404)"
```bash
# Enable mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### XP Not Calculating
```bash
# Re-run migration
php artisan migrate:fresh --seed
```

---

## 🎨 UI Customization Examples

### Change Font
In `app.blade.php`:
```html
<!-- Replace Orbitron with your choice -->
<link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">

.font-display {
    font-family: 'Press Start 2P', monospace;
}
```

### Add New Rarity Tier
In `app.blade.php`:
```css
.rarity-cosmic {
    box-shadow: 0 0 60px rgba(147, 51, 234, 1);
}
```

In models:
```php
// Add to badge/skill enums
'cosmic' => '#9333ea'
```

---

## 📱 Mobile Responsiveness

The app is fully responsive out of the box:
- ✅ Hamburger menu on mobile
- ✅ Touch-friendly buttons
- ✅ Optimized card layouts
- ✅ Responsive skill tree (scroll enabled)

---

## 🔐 Security Best Practices

**Before Going Live:**

1. **Disable Debug Mode**
```env
APP_DEBUG=false
APP_ENV=production
```

2. **Setup HTTPS** (included in deploy script)
```bash
sudo certbot --apache -d yourdomain.com
```

3. **Secure .env File**
```bash
chmod 600 .env
```

4. **Enable CSRF Protection** (Already enabled in Laravel)

5. **Rate Limiting** (Customize in `routes/web.php`)
```php
Route::middleware('throttle:60,1')->group(function () {
    // Your routes
});
```

---

## 🚀 Performance Optimization

```bash
# 1. Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. Optimize autoloader
composer dump-autoload -o

# 3. Enable OPcache
# Edit /etc/php/8.2/apache2/php.ini
opcache.enable=1
opcache.memory_consumption=256
```

---

## 🎯 What's Next?

- [ ] Add more skills to the skill tree
- [ ] Create custom badges for your journey
- [ ] Integrate GitHub API for auto-import
- [ ] Build the AI resume generator
- [ ] Share your profile publicly
- [ ] Add social features

---

## 💬 Need Help?

- 📧 Email: support@lvlup.dev
- 💬 Discord: [Join Server](#)
- 🐛 Issues: [GitHub Issues](https://github.com/yourusername/lvlup/issues)
- 📖 Docs: [Full Documentation](https://docs.lvlup.dev)

---

Made with 💜 by Group 2 | Software Engineering 2
