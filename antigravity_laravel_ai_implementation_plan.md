# Anti Gravity — Laravel AI Implementation Plan

## Goal

Build a modern Laravel website with:
- strong backend architecture
- high frontend quality
- AI-assisted development workflow
- maintainable codebase
- scalable infrastructure
- automated testing and QA
- faster feature delivery

This plan focuses on combining:
- Laravel Boost
- MCP tools
- modern frontend stack
- automated quality systems
- AI coding workflows

---

# Existing Website Modernization Strategy

## Important Principle

Anti Gravity already has a working website.

The goal is NOT to rebuild the system or replace the core business logic.

The goal is to:
- improve reliability
- improve frontend quality
- improve user experience
- improve maintainability
- improve performance
- improve code quality
- improve developer workflow
- reduce bugs
- modernize safely

without breaking the existing system.

---

## Safe Modernization Approach

### DO NOT
- rewrite the entire backend
- replace core workflows abruptly
- redesign all database structures unnecessarily
- migrate frameworks without strong reason
- introduce major architectural risk

### DO
- improve incrementally
- refactor safely
- modernize page by page
- add tests before major changes
- preserve existing business logic
- preserve user familiarity
- optimize bottlenecks first

---

## Recommended Upgrade Priority

### Phase 1 — Stabilize Existing System

Focus:
- fix bugs
- improve code consistency
- improve performance
- improve frontend responsiveness
- improve accessibility
- improve error handling

Tasks:
- audit slow pages
- audit frontend UX pain points
- add loading states
- improve mobile responsiveness
- fix inconsistent UI
- improve validation messages
- improve notifications/toasts
- optimize database queries
- add missing indexes
- remove duplicated logic

---

### Phase 2 — Quality-of-Life Improvements

Focus on making the system feel modern and smooth.

Examples:
- faster navigation
- cleaner dashboards
- searchable tables
- filters/sorting
- better forms
- autosave where useful
- improved modals
- keyboard shortcuts
- dark mode
- activity indicators
- inline validation
- pagination improvements
- better upload experience
- smoother animations
- better empty states

These changes greatly improve perceived quality without changing the system core.

---

### Phase 3 — Internal Code Improvements

Improve maintainability gradually.

Examples:
- move duplicated logic into services
- introduce DTOs gradually
- improve naming consistency
- add Form Requests
- improve authorization structure
- improve queue handling
- improve logging
- improve error reporting

This should happen progressively rather than all at once.

---

### Phase 4 — Intelligent Modernization

Only modernize components that truly need it.

Examples:
- convert outdated pages into reusable components
- modernize frontend interactions
- improve dashboard analytics
- add real-time updates where valuable
- improve admin workflows

The system should evolve naturally instead of being rewritten.

---

# 1. Core Architecture

## Backend Stack

### Framework
- Laravel 12+
- PHP 8.3+

### Database
- PostgreSQL preferred
- Redis for cache/session/queues

### API Style
Use:
- REST API for standard systems
- Laravel API Resources
- Versioned APIs (`/api/v1`)

### Backend Structure

Recommended architecture:

```text
app/
 ├── Actions/
 ├── Services/
 ├── DTOs/
 ├── Repositories/
 ├── Policies/
 ├── Jobs/
 ├── Events/
 ├── Listeners/
 └── Domain/
```

Rules:
- thin controllers
- business logic inside Services/Actions
- Form Requests for validation
- Policies/Gates for authorization
- queued jobs for heavy tasks
- avoid fat models

---

# 2. Frontend Strategy

## Recommended Frontend Stack

### Option A (Recommended)
Laravel + Inertia + React

Benefits:
- excellent developer experience
- SPA feel without API complexity
- easier authentication/session handling
- great AI generation support

Stack:
- React
- TypeScript
- Tailwind CSS
- shadcn/ui
- Framer Motion
- TanStack Query

---

## Frontend Quality Standards

### Design System
Create:
- reusable UI components
- typography scale
- spacing system
- color tokens
- accessibility rules

### UI Rules

Every page should include:
- loading states
- empty states
- error states
- responsive layouts
- accessibility labels
- keyboard navigation

### Performance Targets

Target:
- Lighthouse 90+
- LCP under 2.5s
- CLS under 0.1
- optimized images
- lazy loading

---

# 3. AI Development Workflow

## Primary AI Stack

### Main Coding Assistant
Use one:
- Cursor
- Claude Code
- ChatGPT Desktop with MCP

### Laravel Intelligence
Install:

```bash
composer require laravel/boost --dev
php artisan boost:install
```

Laravel Boost helps the AI:
- inspect models
- inspect routes
- inspect schema
- inspect logs
- inspect configs
- understand Laravel conventions

---

# 4. MCP Server Strategy

## Recommended MCP Setup

### A. Laravel Boost MCP
Purpose:
- Laravel-aware AI coding
- schema inspection
- debugging
- project context

Use for:
- route generation
- migrations
- Eloquent queries
- validation
- controllers
- debugging

---

### B. Browser Automation MCP

Recommended:
- Playwright MCP

Purpose:
- automated frontend testing
- UI validation
- console log inspection
- screenshot testing
- accessibility testing

AI can:
- click through pages
- verify forms
- inspect broken layouts
- detect JS errors

---

### C. Documentation MCP

Purpose:
- internal documentation lookup
- architecture rules
- coding standards

Store:
- API conventions
- frontend rules
- naming standards
- deployment process

---

### D. Database MCP

Purpose:
- query inspection
- schema awareness
- optimization support

Use for:
- relationship validation
- indexing checks
- query performance

---

# 5. Quality Control System

## Static Analysis

Install:

```bash
composer require --dev larastan/larastan
```

Run:

```bash
php artisan test
./vendor/bin/phpstan analyse
```

Purpose:
- catch bugs early
- improve type safety
- improve AI-generated code quality

---

## Code Formatting

Install:

```bash
composer require laravel/pint --dev
```

Run automatically before commits.

---

## Automated Testing

Use:
- Pest PHP
- Playwright

Coverage:
- feature tests
- auth tests
- payment tests
- API tests
- frontend interaction tests

Target:
- 70%+ critical flow coverage

---

# 6. Security Plan

## Backend Security

Requirements:
- CSRF protection
- rate limiting
- authorization policies
- request validation
- encrypted secrets
- secure file uploads

### Authentication
Use:
- Laravel Sanctum
or
- Laravel Passport

---

## Security Scanning

Add:
- Dependabot
- composer audit
- GitHub security alerts

---

# 7. Performance Optimization

## Backend Performance

Use:
- Redis caching
- queue workers
- eager loading
- query indexing
- Horizon monitoring

Avoid:
- N+1 queries
- large synchronous jobs
- repeated DB calls

---

## Frontend Performance

Use:
- Vite
- code splitting
- image optimization
- lazy loading
- CDN

---

# 8. DevOps & Deployment

## Hosting

Recommended:
- Laravel Cloud
- Forge + VPS
- DigitalOcean
- Hetzner

---

## CI/CD Pipeline

GitHub Actions pipeline:

```text
Push
 → Pint
 → PHPStan
 → Pest Tests
 → Frontend Build
 → Deploy
```

---

## Monitoring

Use:
- Laravel Pulse
- Sentry
- Telescope (dev only)
- Horizon

Track:
- slow queries
- failed jobs
- frontend errors
- API latency

---

# 9. Can We Use Google Stitch?

Yes — if you mean Google Stitch AI/UI generation tools.

Possible use cases:
- generate UI ideas
- rapid design prototypes
- create component layouts
- improve visual consistency

But do NOT rely on it alone.

Best workflow:

```text
Google Stitch / AI UI tools
        ↓
Export UI concepts
        ↓
Implement properly in React + Tailwind
        ↓
Validate with Playwright
        ↓
Optimize accessibility/performance
```

AI-generated UI is best used for:
- inspiration
- rapid prototyping
- layout generation

Not as the final production system.

---

# 10. Recommended Development Rules

## Backend Rules

Always:
- use Form Requests
- use DTOs for complex data
- write feature tests
- use queues for heavy work
- use policies for authorization
- use service classes

Never:
- massive controllers
- duplicated queries
- inline validation everywhere
- direct business logic in routes

---

## Frontend Rules

Always:
- use reusable components
- use TypeScript
- use accessibility labels
- optimize images
- use responsive layouts
- handle loading/error states

Never:
- hardcoded duplicated UI
- large monolithic components
- unoptimized images
- inconsistent spacing/colors

---

# 11. Recommended Weekly Workflow

## Daily
- AI-assisted coding with Boost
- automated tests before merge
- Playwright smoke testing

## Weekly
- Lighthouse audit
- database optimization review
- dependency updates
- accessibility checks
- security review

## Monthly
- architecture cleanup
- component refactoring
- performance benchmark review

---

# 12. Final Recommended Stack

## Core Stack

### Backend
- Laravel 12
- PostgreSQL
- Redis
- Horizon

### Frontend
- React
- Inertia
- TypeScript
- Tailwind
- shadcn/ui

### AI Stack
- Laravel Boost
- Playwright MCP
- Cursor or Claude Code

### Quality
- Pest
- PHPStan
- Pint
- Lighthouse

### Infrastructure
- GitHub Actions
- Forge or Laravel Cloud
- Sentry
- Pulse

---

# 13. Outcome Goals

With this setup, Anti Gravity should achieve:

- cleaner architecture
- faster development
- fewer regressions
- better frontend consistency
- improved performance
- easier onboarding
- AI-assisted productivity
- scalable long-term maintenance
- production-grade quality standards

