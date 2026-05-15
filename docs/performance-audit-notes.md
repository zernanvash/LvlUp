# Laravel Performance Audit Notes

Date: 2026-05-15

Reference used: Aqib Manzoor, "How to Speed Up a Slow Laravel Website (Complete Guide)".

## Scope

This audit focuses on safe improvements for the current LvlUp Laravel app: resume builder, PDF resume generation, AI resume assistant, skill tree, achievements, profiles, projects, Blade views, Vite assets, and Redis-ready deployment.

## Baseline Checks

- Local development now uses SQLite, file cache, file sessions, and sync queue to avoid requiring the PHP Redis extension on Windows.
- Production config remains Redis/Valkey-ready through `.env.example`, Docker `phpredis`, and `CACHE_STORE=failover`.
- Vite production build succeeds.
- Laravel feature suite passed before this audit pass: 75 tests, 224 assertions.
- Tailwind CDN and Alpine CDN references were removed from Blade pages in the prior UI pass.

## Existing Performance Strengths

- PDF generation already has a cache path/template metadata strategy on `resumes`, avoiding repeated Browsershot work for unchanged templates.
- Core frequently-used indexes exist for projects, resumes, certificates, displayed badges, user skill nodes, and skill-node hierarchy.
- Dashboard project list, public profile data, project index pages, user discovery, and skill tree nodes already use short TTL caches.
- Vite assets are built and fingerprinted for production.
- Redis/Valkey can be enabled for production cache, queue, and sessions once the runtime has `phpredis`.

## Improvements Made In This Pass

- Optimized achievements page badge progress calculation:
  - Before: badge progress could issue repeated project, skill, and unlocked-node count queries per badge.
  - After: the controller eager-loads `badges`, `projects.skills`, and `unlockedNodes`, precomputes counts once, caches the badge catalog for 10 minutes, and calculates progress in memory.
- Removed an extra equipped-badges query from the achievements Blade view by passing equipped badge data from the controller.

## High-Impact Recommendations

1. Keep production optimized with:

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   npm run build
   composer install --no-dev --optimize-autoloader
   ```

2. Use Aiven Valkey only where `phpredis` is available:

   ```env
   CACHE_STORE=failover
   SESSION_DRIVER=redis
   QUEUE_CONNECTION=redis
   REDIS_CLIENT=phpredis
   REDIS_URL=rediss://default:***@YOUR_AIVEN_HOST:17031
   ```

3. Run a real baseline before further changes:

   - Dashboard authenticated page TTFB
   - `/projects` with 50+ projects
   - `/achievements`
   - `/users` discovery/search
   - Resume AI generation time
   - Resume PDF preview/download first render vs cached render

4. Move long-running work to queues when ready:

   - AI resume generation
   - PDF rendering/upload
   - Certificate AI summary regeneration

   This should be done carefully because current UI expects immediate JSON responses.

5. Add production monitoring:

   - Slow query logging
   - Queue failure monitoring
   - HTTP response time monitoring for authenticated pages
   - PDF generation duration logging

## Deferred / Riskier Ideas

- Full-page caching is not recommended for authenticated dashboard/profile/edit pages because of private user-specific data.
- Laravel Octane may help later, but only after checking for request-global state, session assumptions, and PDF/AI side effects.
- Broad image conversion/compression should be implemented with upload handling and storage compatibility checks.

## Deployment Notes

- Local `.env` should stay file/session/sqlite based unless the local PHP Redis extension is installed.
- Deployment env can use Aiven Valkey with port `17031` and `rediss://`.
- After changing deployment env, clear and rebuild caches:

  ```bash
  php artisan optimize:clear
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```
