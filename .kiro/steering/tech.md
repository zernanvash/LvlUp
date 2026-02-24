# Tech Stack

## Backend
- **Framework**: Laravel 12+ (PHP 8.2+)
- **Database**: SQLite (default), MySQL/MariaDB supported
- **Authentication**: Laravel Breeze
- **Testing**: Pest PHP (preferred over PHPUnit)

## Frontend
- **CSS Framework**: Tailwind CSS 3+ with @tailwindcss/forms
- **JavaScript**: Alpine.js 3+ for interactivity
- **Build Tool**: Vite 7+
- **Icons**: Font Awesome 6
- **Fonts**: Google Fonts (Orbitron, Rajdhani)

## Additional Libraries
- **HTTP Client**: Axios
- **Firebase**: For future real-time features
- **Laravel Pail**: Log viewing
- **Laravel Tinker**: REPL for debugging

## Development Tools
- **Code Style**: Laravel Pint (PHP CS Fixer)
- **Local Development**: Laravel Sail (Docker) or Artisan serve
- **Process Management**: Concurrently for running multiple dev processes

## Common Commands

### Setup
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

### Development
```bash
# Start all dev services (server, queue, logs, vite)
composer dev

# Or individually:
php artisan serve              # Development server (port 8000)
npm run dev                    # Vite dev server with HMR
php artisan queue:listen       # Queue worker
php artisan pail               # Log viewer
```

### Testing
```bash
composer test                  # Run Pest test suite
php artisan test               # Alternative test command
php artisan test --filter=UserTest  # Run specific test
```

### Code Quality
```bash
./vendor/bin/pint              # Format code with Laravel Pint
php artisan config:clear       # Clear config cache
php artisan cache:clear        # Clear application cache
php artisan view:clear         # Clear compiled views
```

### Database
```bash
php artisan migrate            # Run migrations
php artisan migrate:fresh      # Drop all tables and re-migrate
php artisan migrate:fresh --seed  # Re-migrate and seed
php artisan db:seed            # Run seeders
php artisan tinker             # Interactive REPL
```

### Production Optimization
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload -o
```

## Environment Configuration

Default database is SQLite (`database/database.sqlite`). For MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lvlup
DB_USERNAME=root
DB_PASSWORD=
```

## Testing Environment
Tests use in-memory SQLite (`:memory:`) with array drivers for cache, queue, and mail.
