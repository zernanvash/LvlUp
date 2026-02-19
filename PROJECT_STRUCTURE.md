# Project Structure

This document outlines the directory structure and organization of the lvlup project.

## Root Level

```
lvlup/
├── artisan                 # Laravel command-line tool
├── autoload.php           # PHP autoloader
├── composer.json          # PHP dependencies configuration
├── package.json           # Node.js dependencies configuration
├── vite.config.js         # Vite build tool configuration
├── tailwind.config.js     # Tailwind CSS configuration
├── postcss.config.js      # PostCSS configuration
├── phpunit.xml            # PHPUnit testing configuration
├── deploy.sh              # Deployment script
├── README.md              # Project overview
├── QUICKSTART.md          # Quick start guide
├── SUMMARY.md             # Project summary
├── CHANGELOG.md           # Version changelog
├── FILE_ORGANIZATION.md   # File organization documentation
└── PROJECT_STRUCTURE.md   # This file
```

## Directory Structure

### `/app` - Application Source Code
Main Laravel application code containing business logic.

```
app/
├── Http/
│   ├── Controllers/       # Request handlers and business logic
│   └── Requests/          # Form request validation classes
├── Models/                # Eloquent ORM models
│   ├── AllModels.php      # Model index/registry
│   ├── Badge.php          # Badge model
│   ├── DailyReward.php    # Daily reward model
│   ├── Project.php        # Project model
│   ├── Resume.php         # Resume model
│   ├── Skill.php          # Skill model
│   ├── SkillNode.php      # Skill node model
│   └── User.php           # User model
├── Providers/             # Service providers
│   └── AppServiceProvider.php  # Main application service provider
└── View/
    └── Components/        # Reusable view components
```

### `/bootstrap` - Bootstrap Scripts
Initializes the Laravel application and loads configuration.

```
bootstrap/
├── app.php               # Application instance bootstrap
├── providers.php         # Service provider registration
└── cache/
    ├── packages.php      # Cached package configuration
    └── services.php      # Cached service configuration
```

### `/config` - Configuration Files
Application configuration settings.

```
config/
├── app.php              # Application configuration
├── auth.php             # Authentication configuration
├── cache.php            # Caching configuration
├── database.php         # Database configuration
├── filesystems.php      # File storage configuration
├── logging.php          # Logging configuration
├── mail.php             # Email configuration
├── queue.php            # Queue configuration
├── services.php         # Third-party services configuration
└── session.php          # Session configuration
```

### `/database` - Database Management
Database migrations, factories, and seeders for data initialization.

```
database/
├── migrations/          # Database schema migrations
│   ├── 2024_01_01_000001_create_users_table.php
│   ├── 2024_01_01_000002_create_skills_table.php
│   ├── 2024_01_01_000003_create_projects_table.php
│   ├── 2024_01_01_000004_create_project_skill_table.php
│   ├── 2024_01_01_000005_create_skill_nodes_table.php
│   ├── 2024_01_01_000006_create_user_skill_nodes_table.php
│   ├── 2024_01_01_000007_create_badges_table.php
│   ├── 2024_01_01_000008_create_user_badges_table.php
│   ├── 2024_01_01_000009_create_resumes_table.php
│   └── ... (additional migrations)
├── factories/           # Model factories for testing
│   └── UserFactory.php
└── seeders/             # Database seeders for populating data
```

### `/public` - Public Web Root
Publicly accessible files served by the web server.

```
public/
├── index.php            # Application entry point
├── robots.txt           # Search engine crawl instructions
└── build/               # Compiled assets (CSS, JS)
```

### `/resources` - Frontend Assets and Views
Source files for frontend (views, CSS, JavaScript).

```
resources/
├── css/                 # Stylesheets
├── js/                  # JavaScript files
└── views/               # Blade template views
```

### `/routes` - Application Routes
URL routing definitions.

```
routes/
├── web.php              # Web routes
├── auth.php             # Authentication routes
└── console.php          # Artisan command routes
```

### `/storage` - Storage Directory
Generated files and application-created data.

```
storage/
├── app/                 # Application files
├── framework/           # Framework-generated files
└── logs/                # Application log files
```

### `/tests` - Test Suite
Automated testing files.

```
tests/
├── Pest.php             # Pest testing framework setup
├── TestCase.php         # Base test case class
├── Feature/             # Feature tests
└── Unit/                # Unit tests
```

### `/vendor` - Composer Dependencies
Third-party PHP packages installed by Composer.

```
vendor/
├── autoload.php         # Composer autoloader
├── pest-plugins.json    # Pest plugin configuration
├── bin/                 # Composer executable binaries
└── [various packages]   # Installed dependencies
```

### `/LvlUp` - Nested Project Directory
A nested Laravel project instance (possibly for modular structure or separate environment).

```
LvlUp/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
└── [configuration files]
```

## Key Application Models

The application uses the following Eloquent ORM models:

- **User** - Represents a user in the system
- **Skill** - Represents a skill that users can learn
- **SkillNode** - Represents individual nodes within a skill tree
- **Project** - Represents projects (likely user projects or portfolio items)
- **Badge** - Achievement badges users can earn
- **DailyReward** - Daily reward system data
- **Resume** - User resume/CV information

## Build and Development Files

- **vite.config.js** - Asset bundling and development server configuration
- **tailwind.config.js** - Tailwind CSS utility framework configuration
- **postcss.config.js** - CSS post-processing configuration
- **package.json** - Node.js dependencies and scripts
- **composer.json** - PHP dependencies via Composer
- **phpunit.xml** - PHP unit testing framework configuration

## Deployment

- **deploy.sh** - Automated deployment script for server deployment

