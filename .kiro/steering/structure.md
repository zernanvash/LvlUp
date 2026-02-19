# Project Structure

## Directory Organization

```
lvlup/
├── app/                          # Application core
│   ├── Http/
│   │   ├── Controllers/          # Request handlers
│   │   │   ├── Auth/            # Authentication controllers (Breeze)
│   │   │   ├── BadgeController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── ProjectController.php
│   │   │   ├── ResumeController.php
│   │   │   └── SkillTreeController.php
│   │   └── Requests/            # Form request validation
│   ├── Models/                  # Eloquent models
│   │   ├── Badge.php
│   │   ├── DailyReward.php
│   │   ├── Project.php
│   │   ├── Resume.php
│   │   ├── Skill.php
│   │   ├── SkillNode.php
│   │   └── User.php
│   ├── Providers/               # Service providers
│   └── View/Components/         # Blade components
├── bootstrap/                    # Framework bootstrap
├── config/                       # Configuration files
├── database/
│   ├── factories/               # Model factories for testing
│   ├── migrations/              # Database migrations (timestamped)
│   ├── seeders/                 # Database seeders
│   └── database.sqlite          # SQLite database file
├── public/                       # Web root (index.php, assets)
├── resources/
│   ├── css/                     # Tailwind CSS source
│   ├── js/                      # JavaScript source
│   └── views/                   # Blade templates
│       ├── achievements/
│       ├── auth/                # Authentication views (Breeze)
│       ├── components/          # Reusable Blade components
│       ├── layouts/             # Layout templates
│       ├── profile/
│       ├── projects/
│       ├── skill-tree/
│       ├── dashboard.blade.php
│       └── welcome.blade.php
├── routes/
│   ├── web.php                  # Web routes
│   └── auth.php                 # Authentication routes (Breeze)
├── storage/                      # Generated files, logs, cache
├── tests/
│   ├── Feature/                 # Feature tests (HTTP, integration)
│   └── Unit/                    # Unit tests (isolated logic)
└── vendor/                       # Composer dependencies
```

## Key Conventions

### Controllers
- Use resource controllers for CRUD operations (`Route::resource()`)
- Keep controllers thin - delegate business logic to models or services
- Group related routes with `Route::middleware()` and `Route::group()`

### Models
- Located in `app/Models/`
- Use Eloquent relationships (hasMany, belongsTo, belongsToMany)
- Define fillable/guarded properties for mass assignment protection
- Add custom methods for business logic (e.g., `User::xpNeededForNextLevel()`)

### Views
- Blade templates in `resources/views/`
- Use layouts (`layouts/app.blade.php`, `layouts/guest.blade.php`)
- Component-based architecture (`components/` directory)
- Naming: `resource.action.blade.php` (e.g., `projects.create.blade.php`)

### Migrations
- Timestamped format: `YYYY_MM_DD_HHMMSS_description.php`
- Use descriptive names: `create_users_table`, `add_xp_to_users_table`
- Always include `down()` method for rollback
- Pivot tables: alphabetically ordered (e.g., `project_skill`, not `skill_project`)

### Routes
- RESTful naming conventions
- Protected routes use `auth` and `verified` middleware
- API routes prefixed with `/api` and use `auth:sanctum`
- Route names follow pattern: `resource.action` (e.g., `projects.store`)

### Database Schema Patterns
- Primary keys: `id` (auto-increment)
- Foreign keys: `{model}_id` (e.g., `user_id`, `project_id`)
- Timestamps: `created_at`, `updated_at` (automatic with `$table->timestamps()`)
- Soft deletes: `deleted_at` (use `$table->softDeletes()`)
- Pivot tables: no `id`, use composite keys

## Naming Conventions

### PHP/Laravel
- Classes: PascalCase (`UserController`, `ProjectModel`)
- Methods: camelCase (`claimDailyReward`, `xpNeededForNextLevel`)
- Variables: camelCase (`$userId`, `$totalXp`)
- Database tables: snake_case, plural (`users`, `skill_nodes`, `project_skill`)
- Database columns: snake_case (`created_at`, `gacha_currency`, `skill_point_cost`)

### Frontend
- Blade files: kebab-case (`skill-tree.blade.php`)
- CSS classes: Tailwind utility classes
- JavaScript: camelCase for variables/functions
- Alpine.js directives: `x-data`, `x-show`, `x-on:click`

## Testing Structure
- Feature tests: Test HTTP requests, authentication, full workflows
- Unit tests: Test isolated model methods, helpers, utilities
- Use Pest syntax: `it('does something', function() { ... })`
- Database: Use factories and seeders for test data
- Assertions: `expect($value)->toBe()`, `assertStatus()`, `assertDatabaseHas()`

## Asset Pipeline
- Source files: `resources/css/app.css`, `resources/js/app.js`
- Vite compiles to: `public/build/`
- Reference in Blade: `@vite(['resources/css/app.css', 'resources/js/app.js'])`
- Tailwind config: `tailwind.config.js` in project root

## Configuration Files
- Environment: `.env` (never commit), `.env.example` (commit)
- App config: `config/app.php`
- Database: `config/database.php`
- Auth: `config/auth.php`
- Custom config: Create new file in `config/` and access via `config('filename.key')`
