# Fix 419 Page Expired Error

## The Problem
The 419 error occurs when Laravel's CSRF token expires or the session isn't working properly. This is common when:
- The session has expired
- Browser cache is stale
- Session storage isn't configured correctly

## Solution Steps

### Step 1: Clear All Laravel Caches
Run these commands in order:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Step 2: Restart Development Server
Stop your current server (Ctrl+C) and restart:
```bash
php artisan serve
```

### Step 3: Clear Browser Cache
In your browser:
1. Press `Ctrl+Shift+Delete` (or `Cmd+Shift+Delete` on Mac)
2. Select "Cookies and other site data" and "Cached images and files"
3. Clear data for "Last hour" or "All time"
4. Or simply do a hard refresh: `Ctrl+Shift+R` (or `Cmd+Shift+R`)

### Step 4: Try Again
1. Go to http://localhost:8000/login
2. Enter credentials:
   - Email: `test@lvlup.dev`
   - Password: `password`
3. Submit the form

## Alternative Solution: Switch to File Sessions

If the issue persists, database sessions might be causing problems. Switch to file-based sessions:

1. Open `.env` file
2. Find the line: `SESSION_DRIVER=database`
3. Change it to: `SESSION_DRIVER=file`
4. Run: `php artisan config:clear`
5. Restart server: `php artisan serve`

## Verify Session Configuration

Check if sessions are working:
```bash
php artisan tinker
```

Then run:
```php
echo "Sessions table: " . (Schema::hasTable('sessions') ? 'EXISTS' : 'MISSING');
echo "\nSession driver: " . config('session.driver');
echo "\nApp key set: " . (config('app.key') ? 'YES' : 'NO');
exit
```

All three should show positive results.

## Still Not Working?

### Check APP_KEY
```bash
php artisan key:generate
php artisan config:clear
```

### Check Storage Permissions
On Windows, this is usually not an issue, but verify:
```bash
dir storage\framework\sessions
```

The directory should exist. If not:
```bash
mkdir storage\framework\sessions
```

### Nuclear Option: Fresh Start
```bash
# Stop server
# Delete database
del database\database.sqlite

# Recreate everything
php artisan migrate:fresh --seed
php artisan config:clear
php artisan cache:clear
php artisan serve
```

Then clear browser cache and try again.

## Prevention

To avoid this in the future:
1. Don't leave the login page open for extended periods
2. Clear cache after making configuration changes
3. Use `SESSION_LIFETIME=120` (2 hours) in `.env` for longer sessions
4. Consider using `SESSION_DRIVER=file` for local development
