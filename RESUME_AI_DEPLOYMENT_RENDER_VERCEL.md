# Resume AI Deployment: Render + Vercel

This app is deployed as:

- Render: Laravel backend, auth, resume generation, PDF rendering, Cloudinary uploads
- Vercel: optional Vite frontend/static client
- Aiven MySQL: production database
- NVIDIA NIM API: resume AI generation
- Cloudinary: image and PDF uploads

## Production Flow

```txt
Vercel frontend or Laravel Blade UI
    -> Render Laravel backend
    -> NVIDIA resume AI API
    -> Resume JSON
    -> Blade PDF template
    -> Browsershot/Puppeteer PDF
    -> Browser stream or Cloudinary URL
```

## Files Added For Deployment

```txt
Dockerfile
.dockerignore
apache.conf
start.sh
config/cors.php
routes/api.php
app/Services/NvidiaResumeService.php
resources/js/lib/api.js
```

The app already uses `config/resume_ai.php`, so no separate `config/nvidia.php` is needed.

## Render Backend Environment

Set these in Render. Keep secrets only on Render, not Vercel.

```env
APP_NAME=LvlUp
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-render-backend-url.onrender.com
FRONTEND_URL=https://your-vercel-frontend-url.vercel.app

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=mysql-1ae17810-lvlup.d.aivencloud.com
DB_PORT=your-aiven-port
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
MYSQL_ATTR_SSL_CA=

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
RUN_MIGRATIONS=true

CLOUDINARY_URL=cloudinary://api_key:api_secret@cloud_name

NVIDIA_API_KEY=
NVIDIA_BASE_URL=https://integrate.api.nvidia.com/v1
RESUME_AI_TIMEOUT=45
RESUME_AI_CONTENT_MODEL=mistral-medium-3.5-128b
RESUME_AI_LAYOUT_MODEL=mistral-small-4-119b-2603
RESUME_AI_LONG_CONTEXT_MODEL=nemotron-3-super-120b-a12b
RESUME_AI_LONG_CONTEXT_THRESHOLD=9000

PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium
```

Important:

- Use the exact Aiven MySQL port from your Aiven dashboard.
- If Aiven requires a CA certificate, mount or add the CA file and point `MYSQL_ATTR_SSL_CA` to it.
- Keep `NVIDIA_API_KEY`, `DB_PASSWORD`, and `CLOUDINARY_URL` off Vercel.

## Vercel Frontend Environment

```env
VITE_API_BASE_URL=https://your-render-backend-url.onrender.com
```

Do not put backend secrets in Vercel.

## Render Settings

Use a Render Web Service:

```txt
Runtime: Docker
Dockerfile Path: ./Dockerfile
Branch: UI-changes or main
```

The Docker image installs:

- PHP 8.2 Apache
- Composer dependencies
- Node 22
- Vite production assets
- Chromium runtime dependencies for Browsershot/Puppeteer

## Startup Script

`start.sh` clears and rebuilds config/view caches, optionally runs migrations, then starts Apache.

Route caching is intentionally skipped because the current app still contains closure routes. Add route caching later after closure routes are moved to controllers.

## CORS

`config/cors.php` allows:

```php
env('FRONTEND_URL', 'http://localhost:5173')
```

Set `FRONTEND_URL` to the exact Vercel production domain.

## API Endpoint For Vercel

The Vercel client can call:

```txt
POST /api/resume/generate
```

Payload:

```json
{
  "profile": {
    "name": "Test User",
    "skills": ["Laravel", "React"]
  },
  "target_role": "Web Developer"
}
```

The endpoint is throttled at `10` requests per minute and uses `config/resume_ai.php`.

## Local Testing

Backend:

```bash
php artisan serve
```

Frontend:

```bash
npm run dev
```

Local frontend env:

```env
VITE_API_BASE_URL=http://127.0.0.1:8000
```

Test API:

```bash
curl -X POST http://127.0.0.1:8000/api/resume/generate \
  -H "Content-Type: application/json" \
  -d "{\"profile\":{\"name\":\"Test User\",\"skills\":[\"Laravel\",\"React\"]},\"target_role\":\"Web Developer\"}"
```

## Production Checklist

```txt
[ ] APP_ENV=production
[ ] APP_DEBUG=false
[ ] APP_KEY is generated
[ ] APP_URL points to Render
[ ] FRONTEND_URL points to Vercel
[ ] VITE_API_BASE_URL points to Render
[ ] Aiven DB host, port, database, user, password are set
[ ] MYSQL_ATTR_SSL_CA is set if required by Aiven
[ ] CLOUDINARY_URL is set on Render
[ ] NVIDIA_API_KEY is set only on Render
[ ] php artisan migrate --force works
[ ] Resume generation works
[ ] PDF preview/download works
[ ] Uploads go to Cloudinary, not local permanent disk
```
