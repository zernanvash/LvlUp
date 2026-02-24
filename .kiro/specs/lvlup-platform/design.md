# Design Document: LvlUp Platform

## Overview

The LvlUp platform is a gamified portfolio management system built on Laravel 12+ with a modern frontend stack (Tailwind CSS, Alpine.js). The system transforms traditional portfolio building into an engaging RPG-like experience through XP progression, an interactive skill tree, achievement badges, and AI-powered resume generation.

The architecture follows Laravel's MVC pattern with Eloquent ORM for data persistence, Blade templating for views, and Alpine.js for reactive UI components. The system uses SQLite as the default database for simplicity and portability.

Key design principles:
- **Separation of concerns**: Business logic in models, thin controllers, reusable Blade components
- **Event-driven architecture**: Model events trigger XP awards, badge checks, and skill tree unlocks
- **Progressive enhancement**: Core functionality works without JavaScript, Alpine.js enhances interactivity
- **Responsive design**: Mobile-first approach with Tailwind CSS utilities
- **Gamification psychology**: Immediate feedback, clear progression paths, visible achievements

## Architecture

### System Components

```
┌─────────────────────────────────────────────────────────────┐
│                        Web Browser                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │ Blade Views  │  │  Alpine.js   │  │  Tailwind    │     │
│  │  Templates   │  │  Components  │  │     CSS      │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    Laravel Application                       │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                    Controllers                        │  │
│  │  ProjectController │ BadgeController │ SkillTree...  │  │
│  └──────────────────────────────────────────────────────┘  │
│                            │                                 │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                   Business Logic                      │  │
│  │  User Model │ Project Model │ SkillNode Model        │  │
│  │  - addXP()  │ - checkBadges() │ - canBeUnlockedBy() │  │
│  └──────────────────────────────────────────────────────┘  │
│                            │                                 │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                  Eloquent ORM                         │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    SQLite Database                           │
│  users │ projects │ skills │ skill_nodes │ badges           │
│  project_skill │ user_badges │ user_skill_nodes             │
└─────────────────────────────────────────────────────────────┘
```

### Request Flow

1. **User Action**: User submits form (create project, unlock node, etc.)
2. **Route Handling**: Laravel routes request to appropriate controller
3. **Controller**: Validates input, delegates to model methods
4. **Model Logic**: Executes business logic, triggers events
5. **Event Handlers**: Award XP, check badges, update skill tree
6. **Database**: Persist changes via Eloquent ORM
7. **Response**: Return view with updated data or JSON for AJAX
8. **View Rendering**: Blade compiles template, Alpine.js adds interactivity

### Technology Stack Integration

- **Laravel Breeze**: Provides authentication scaffolding (login, register, password reset)
- **Eloquent ORM**: Handles all database operations with relationships
- **Blade Components**: Reusable UI components (badge-card, skill-node, xp-bar)
- **Alpine.js**: Client-side reactivity for modals, tooltips, dynamic forms
- **Tailwind CSS**: Utility-first styling with custom theme for gamification
- **Vite**: Asset bundling and hot module replacement for development

## Components and Interfaces

### Core Models

#### User Model

**Responsibilities**:
- Manage user authentication and profile data
- Calculate XP progression and level-ups
- Track rank advancement based on level
- Maintain relationships with projects, badges, and skill nodes

**Key Methods**:
```php
public function addXP(int $amount): void
public function xpNeededForNextLevel(): int
public function xpProgress(): float
private function updateRank(): void
```

**Relationships**:
- `hasMany(Project::class)` - User's portfolio projects
- `belongsToMany(Badge::class)` - Earned badges with pivot data
- `belongsToMany(SkillNode::class)` - Unlocked skill tree nodes
- `hasMany(Resume::class)` - Generated resumes

**Database Fields**:
- Authentication: `name`, `email`, `password`, `email_verified_at`
- Profile: `avatar`, `bio`, `title`
- Gamification: `level`, `xp`, `total_xp`, `rank`
- Timestamps: `last_login`, `created_at`, `updated_at`

#### Project Model

**Responsibilities**:
- Store project portfolio data
- Manage skill associations with proficiency levels
- Trigger XP awards and badge checks on creation
- Support skill suggestion from code analysis

**Key Methods**:
```php
public function attachSkillsFromTags(array $tags): void
public function analyzeCodeAndSuggestSkills(string $code): array
private function checkBadges(): void
```

**Relationships**:
- `belongsTo(User::class)` - Project owner
- `belongsToMany(Skill::class)` - Associated technologies with proficiency

**Database Fields**:
- Core: `name`, `description`, `url`, `github_url`, `language`
- Media: `thumbnail`
- Gamification: `xp_reward`, `is_featured`
- Metadata: `metadata` (JSON for extensibility)

#### SkillNode Model

**Responsibilities**:
- Represent nodes in the skill tree graph
- Enforce unlock requirements (level, parent, tasks)
- Track user unlock status
- Position nodes for visual rendering

**Key Methods**:
```php
public function isUnlockedBy(User $user): bool
public function canBeUnlockedBy(User $user): bool
public function checkTaskRequirements(User $user): array
```

**Relationships**:
- `belongsTo(Skill::class)` - Associated skill/technology
- `belongsTo(SkillNode::class, 'parent_node_id')` - Parent node
- `hasMany(SkillNode::class, 'parent_node_id')` - Child nodes
- `belongsToMany(User::class)` - Users who unlocked this node

**Database Fields**:
- Identity: `title`, `description`
- Hierarchy: `parent_node_id`, `tier`
- Requirements: `required_level`, `task_requirements` (JSON)
- Positioning: `x_position`, `y_position`
- Reference: `skill_id`

#### Badge Model

**Responsibilities**:
- Define achievement criteria
- Store rarity and category classification
- Provide reward values (XP)
- Support automatic awarding logic

**Key Methods**:
```php
public function getRarityColorAttribute(): string
public function checkEligibility(User $user): bool
```

**Relationships**:
- `belongsToMany(User::class)` - Users who earned this badge
- `belongsTo(Skill::class, 'required_skill_id')` - Skill requirement (optional)

**Database Fields**:
- Identity: `title`, `slug`, `description`, `icon`
- Classification: `rarity`, `category`
- Requirements: `required_skill_id`, `threshold`
- Rewards: `xp_reward`

#### Skill Model

**Responsibilities**:
- Catalog available technologies and skills
- Categorize skills by domain (frontend, backend, etc.)
- Support rarity classification
- Link to projects and skill tree nodes

**Relationships**:
- `belongsToMany(Project::class)` - Projects using this skill
- `hasMany(SkillNode::class)` - Skill tree nodes for this skill
- `hasMany(Badge::class)` - Badges requiring this skill

**Database Fields**:
- Identity: `name`, `slug`, `description`
- Visual: `icon`, `color`
- Classification: `category`, `rarity`, `required_level`

#### Resume Model

**Responsibilities**:
- Store resume generation parameters
- Track selected projects and skills
- Calculate job description match scores
- Manage PDF generation and storage

**Key Methods**:
```php
public function getSelectedProjects(): Collection
public function getSelectedSkills(): Collection
public function generatePDF(string $template): string
```

**Relationships**:
- `belongsTo(User::class)` - Resume owner

**Database Fields**:
- Job Info: `job_title`, `job_description`, `target_keywords`
- Selection: `selected_project_ids` (JSON), `selected_skill_ids` (JSON)
- Output: `pdf_path`, `match_score`

### Controllers

#### ProjectController

**Responsibilities**:
- Handle CRUD operations for projects
- Validate project input data
- Trigger skill attachment and XP awards
- Return project views and JSON responses

**Key Actions**:
- `index()` - List user's projects with filtering
- `create()` - Show project creation form
- `store()` - Create new project, award XP, check badges
- `edit()` - Show project edit form
- `update()` - Update project details
- `destroy()` - Delete project (preserve earned XP)

#### SkillTreeController

**Responsibilities**:
- Render skill tree visualization
- Handle node unlock requests
- Check unlock eligibility
- Track task requirement progress

**Key Actions**:
- `index()` - Display skill tree with user's progress
- `show($nodeId)` - Show node details and requirements
- `unlock($nodeId)` - Attempt to unlock node, verify requirements
- `progress()` - Return JSON of user's unlock progress

#### BadgeController

**Responsibilities**:
- Display badge collection
- Handle badge equipping/unequipping
- Show badge details and progress
- Manage badge display slots (max 6)

**Key Actions**:
- `index()` - Show all badges (earned and unearned)
- `show($badgeId)` - Display badge details
- `equip($badgeId)` - Equip badge to profile
- `unequip($badgeId)` - Remove badge from profile

#### ResumeController

**Responsibilities**:
- Handle resume generation workflow
- Analyze job descriptions for keywords
- Match projects to job requirements
- Generate and serve PDF resumes

**Key Actions**:
- `index()` - List user's resumes
- `create()` - Show resume builder form
- `analyze()` - Analyze job description, suggest projects
- `store()` - Save resume configuration
- `generate($resumeId)` - Generate PDF
- `download($resumeId)` - Serve PDF file

#### ProfileController

**Responsibilities**:
- Display user profiles (own and public)
- Handle profile updates
- Manage privacy settings
- Show equipped badges and stats

**Key Actions**:
- `show($username)` - Display public profile
- `edit()` - Show profile edit form
- `update()` - Update profile data
- `toggleVisibility()` - Switch public/private

### Services and Helpers

#### ResumeAnalyzer Service

**Responsibilities**:
- Extract keywords from job descriptions
- Score projects against keywords
- Rank projects by relevance
- Calculate overall match score

**Key Methods**:
```php
public function extractKeywords(string $jobDescription): array
public function scoreProject(Project $project, array $keywords): float
public function rankProjects(Collection $projects, array $keywords): Collection
public function calculateMatchScore(User $user, array $keywords): float
```

**Algorithm**:
1. Tokenize job description, remove stop words
2. Extract technology keywords (case-insensitive matching)
3. For each project, count keyword matches in name, description, skills
4. Weight matches: skill tags (3x), description (2x), name (1x)
5. Normalize scores to 0-100 scale
6. Return top N projects sorted by score

#### SkillTreeBuilder Service

**Responsibilities**:
- Generate skill tree structure from database
- Calculate node positions for visualization
- Determine node states (locked, available, unlocked)
- Track task requirement progress

**Key Methods**:
```php
public function buildTree(User $user): array
public function getNodeState(SkillNode $node, User $user): string
public function calculateTaskProgress(SkillNode $node, User $user): array
```

**Node States**:
- `locked` - Requirements not met (gray, no interaction)
- `available` - Requirements met, can unlock (glowing, clickable)
- `unlocked` - Already unlocked by user (colored, shows completion)

#### XPCalculator Service

**Responsibilities**:
- Calculate XP rewards for actions
- Determine level-up thresholds
- Apply XP multipliers for special events

**Key Methods**:
```php
public function calculateProjectXP(Project $project): int
public function calculateBadgeXP(Badge $badge): int
public function xpForLevel(int $level): int
```

**XP Formulas**:
- Base project XP: 100 points
- Skill multiplier: +20 per skill tag (max 5 skills)
- Featured project: +50 bonus
- Badge XP: Varies by rarity (Common: 50, Rare: 150, Epic: 300, Legendary: 500, Mythic: 1000)
- Level threshold: `100 * (level ^ 1.5)`

## Data Models

### Database Schema

#### users table
```sql
id: bigint (PK)
name: string
email: string (unique)
email_verified_at: timestamp (nullable)
password: string
avatar: string (nullable)
bio: text (nullable)
title: string (nullable)
level: integer (default: 1)
xp: integer (default: 0)
total_xp: integer (default: 0)
rank: string (default: 'Bronze')
last_login: date (nullable)
remember_token: string (nullable)
created_at: timestamp
updated_at: timestamp
```

#### projects table
```sql
id: bigint (PK)
user_id: bigint (FK -> users.id)
name: string
description: text
url: string (nullable)
github_url: string (nullable)
language: string (nullable)
thumbnail: string (nullable)
xp_reward: integer (default: 100)
is_featured: boolean (default: false)
metadata: json (nullable)
created_at: timestamp
updated_at: timestamp
```

#### skills table
```sql
id: bigint (PK)
name: string
slug: string (unique)
icon: string (default: 'fa-code')
color: string (default: '#6366f1')
description: text (nullable)
category: string (default: 'backend')
rarity: string (default: 'common')
required_level: integer (default: 1)
created_at: timestamp
updated_at: timestamp
```

#### skill_nodes table
```sql
id: bigint (PK)
skill_id: bigint (FK -> skills.id, nullable)
parent_node_id: bigint (FK -> skill_nodes.id, nullable)
title: string
description: text
x_position: integer
y_position: integer
tier: integer (default: 1)
required_level: integer (default: 1)
task_requirements: json (nullable)
created_at: timestamp
updated_at: timestamp
```

#### badges table
```sql
id: bigint (PK)
title: string
slug: string (unique)
description: text
icon: string
rarity: string (default: 'common')
category: string
required_skill_id: bigint (FK -> skills.id, nullable)
threshold: integer (default: 1)
xp_reward: integer (default: 50)
created_at: timestamp
updated_at: timestamp
```

#### resumes table
```sql
id: bigint (PK)
user_id: bigint (FK -> users.id)
job_title: string
target_keywords: text (nullable)
job_description: text
selected_project_ids: json (nullable)
selected_skill_ids: json (nullable)
pdf_path: string (nullable)
match_score: float (nullable)
created_at: timestamp
updated_at: timestamp
```

#### Pivot Tables

**project_skill**
```sql
project_id: bigint (FK -> projects.id)
skill_id: bigint (FK -> skills.id)
proficiency: integer (default: 1, range: 1-5)
created_at: timestamp
updated_at: timestamp
PRIMARY KEY (project_id, skill_id)
```

**user_badges**
```sql
user_id: bigint (FK -> users.id)
badge_id: bigint (FK -> badges.id)
earned_at: timestamp
is_displayed: boolean (default: false)
created_at: timestamp
updated_at: timestamp
PRIMARY KEY (user_id, badge_id)
```

**user_skill_nodes**
```sql
user_id: bigint (FK -> users.id)
skill_node_id: bigint (FK -> skill_nodes.id)
unlocked_at: timestamp
created_at: timestamp
updated_at: timestamp
PRIMARY KEY (user_id, skill_node_id)
```

### Task Requirements Data Structure

Task requirements are stored as JSON in the `skill_nodes.task_requirements` column:

```json
[
  {
    "type": "project_count",
    "description": "Upload 3 projects",
    "required": 3
  },
  {
    "type": "skill_projects",
    "description": "Upload 2 projects with Python",
    "skill_slug": "python",
    "required": 2
  },
  {
    "type": "badge_count",
    "description": "Earn 5 badges",
    "required": 5
  },
  {
    "type": "level_requirement",
    "description": "Reach level 10",
    "required": 10
  }
]
```

**Task Types**:
- `project_count` - Total number of projects uploaded
- `skill_projects` - Projects with specific skill tag
- `badge_count` - Total badges earned
- `level_requirement` - User level threshold
- `node_unlock` - Specific node must be unlocked

### Relationships Diagram

```
User ──┬─< Projects ──< project_skill >── Skills
       │
       ├─< user_badges >── Badges ──> Skills (optional)
       │
       ├─< user_skill_nodes >── SkillNodes ──> Skills (optional)
       │                              │
       │                              └─> SkillNodes (parent)
       │
       └─< Resumes
```



## Correctness Properties

A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.

### User Management Properties

Property 1: User initialization
*For any* new user registration, the created user should have level=1, xp=0, total_xp=0, and rank='Bronze'
**Validates: Requirements 1.1**

Property 2: Login timestamp update
*For any* user login event, the user's last_login field should be updated to the current date
**Validates: Requirements 1.2**

Property 3: Profile update persistence
*For any* valid profile update (name, bio, title, avatar), the changes should be persisted and retrievable
**Validates: Requirements 1.3**

Property 4: Profile stats display
*For any* user profile view, the rendered output should contain the user's current level, xp, rank, and total_xp values
**Validates: Requirements 1.4**

Property 5: Profile visibility toggle
*For any* user, toggling profile visibility should update the visibility setting and be reflected in access control
**Validates: Requirements 1.5, 9.4**

Property 6: Badge equip/unequip
*For any* badge owned by a user, equipping it should set is_displayed=true and unequipping should set is_displayed=false in the pivot table
**Validates: Requirements 1.6, 11.2, 11.3**

### Project Management Properties

Property 7: Project creation persistence
*For any* valid project data, creating a project should persist all fields (name, description, url, github_url, language, thumbnail) to the database
**Validates: Requirements 2.1**

Property 8: Project XP award
*For any* project creation, the user's xp and total_xp should increase by the project's xp_reward value
**Validates: Requirements 2.2, 3.1**

Property 9: Skill attachment with proficiency
*For any* project and skill combination, attaching the skill should record the proficiency level (1-5) in the project_skill pivot table
**Validates: Requirements 2.4, 7.2**

Property 10: Project update preserves XP
*For any* project update, the user's xp and total_xp should remain unchanged
**Validates: Requirements 2.5**

Property 11: Project deletion preserves progression
*For any* project deletion, the user's total_xp and unlocked skill nodes should remain unchanged
**Validates: Requirements 2.6**

Property 12: Featured project flag
*For any* project, marking it as featured should set is_featured=true and be reflected in queries
**Validates: Requirements 2.7**

Property 13: Automatic badge awarding
*For any* user action that meets a badge threshold, the badge should be automatically awarded with earned_at timestamp and xp_reward granted
**Validates: Requirements 2.8, 5.1, 5.2, 5.3**

### XP and Leveling Properties

Property 14: Level-up trigger
*For any* user whose current xp reaches or exceeds xpNeededForNextLevel(), the level should increment by 1
**Validates: Requirements 3.2**

Property 15: XP overflow preservation
*For any* level-up event, excess XP beyond the level threshold should be preserved in the current xp field
**Validates: Requirements 3.3**

Property 16: XP formula correctness
*For any* level value, xpNeededForNextLevel() should return exactly 100 * (level ^ 1.5)
**Validates: Requirements 3.4**

Property 17: Rank progression
*For any* user level, the rank should match the correct tier: level 1-9=Bronze, 10-24=Silver, 25-49=Gold, 50-74=Platinum, 75-99=Diamond, 100+=Master
**Validates: Requirements 3.5**

Property 18: XP progress percentage
*For any* user, xpProgress() should return (current xp / xpNeededForNextLevel()) * 100
**Validates: Requirements 3.6**

### Skill Tree Properties

Property 19: Parent-child relationships
*For any* skill node with a parent_node_id, the parent relationship should be queryable and the parent should exist
**Validates: Requirements 4.1**

Property 20: Node state calculation
*For any* skill node and user, the node state should be: 'unlocked' if user has unlocked it, 'available' if all requirements are met, or 'locked' otherwise
**Validates: Requirements 4.2**

Property 21: Task completion marks availability
*For any* skill node with task requirements, when a user completes all tasks, the node should be marked as available for unlock
**Validates: Requirements 4.3, 10.4**

Property 22: Unlock validation
*For any* skill node unlock attempt, the system should verify: required_level is met, parent node is unlocked (if exists), and all task requirements are completed
**Validates: Requirements 4.4, 4.5, 4.6, 10.5**

Property 23: Unlock persistence
*For any* successful skill node unlock, the unlock should be recorded in user_skill_nodes with unlocked_at timestamp
**Validates: Requirements 4.7**

### Badge System Properties

Property 24: Badge eligibility checking
*For any* badge with a threshold, the badge should be awarded when the user's progress (project count or skill-specific project count) reaches the threshold
**Validates: Requirements 5.6, 5.7**

Property 25: Badge equip limit
*For any* user, attempting to equip more than 6 badges should be rejected with an error
**Validates: Requirements 5.8, 11.1, 11.4**

Property 26: Badge collection display
*For any* user viewing their badge collection, both earned badges (with earned_at) and unearned badges should be displayed with progress indicators
**Validates: Requirements 5.9**

Property 27: Badge display ordering
*For any* user with equipped badges, the badges should be displayed in the order they were equipped (by pivot table created_at)
**Validates: Requirements 11.5**

### Resume Builder Properties

Property 28: Keyword extraction
*For any* job description text, the system should extract technology keywords (case-insensitive, removing stop words)
**Validates: Requirements 6.1**

Property 29: Project-keyword matching
*For any* resume generation, projects should be matched to job description keywords based on skill tags, description, and name
**Validates: Requirements 6.2**

Property 30: Match score calculation
*For any* resume generation, the match score should represent the percentage of job description keywords found in the user's profile
**Validates: Requirements 6.3**

Property 31: Project relevance ranking
*For any* resume generation, projects should be ranked by relevance score (keyword matches weighted by location)
**Validates: Requirements 6.4**

Property 32: Top N project selection
*For any* resume generation with N specified, exactly N projects (or fewer if user has fewer) should be selected from the ranked list
**Validates: Requirements 6.5**

Property 33: Resume data persistence
*For any* resume generation, the selected_project_ids and selected_skill_ids should be stored as JSON arrays in the resume record
**Validates: Requirements 6.6**

Property 34: Manual selection updates
*For any* resume, users should be able to update selected_project_ids and selected_skill_ids before finalization
**Validates: Requirements 6.7**

Property 35: PDF generation
*For any* finalized resume, a PDF file should be generated and the pdf_path should be stored in the resume record
**Validates: Requirements 6.8, 6.9**

Property 36: Template selection
*For any* resume generation with a specified template, the PDF should be generated using that template's layout
**Validates: Requirements 6.10**

### Skill Management Properties

Property 37: Auto-create skills
*For any* non-existent skill tag used in project creation, a new skill should be created with default values (category='backend', icon='fa-code', rarity='common')
**Validates: Requirements 7.3**

Property 38: Skill-project relationship
*For any* skill, querying its projects should return all projects that have that skill attached in the project_skill pivot table
**Validates: Requirements 7.6**

Property 39: User skill proficiency calculation
*For any* user and skill, the proficiency should be calculated as the average of proficiency values across all user projects with that skill
**Validates: Requirements 7.7**

### UI and Visual Properties

Property 40: Rarity color mapping
*For any* badge or skill, the color should match its rarity: Common=gray, Uncommon=green, Rare=blue, Epic=purple, Legendary=orange, Mythic=pink
**Validates: Requirements 8.2**

### Public Profile Properties

Property 41: Unique profile URLs
*For any* two different users, their public profile URLs should be unique (based on username or ID)
**Validates: Requirements 9.1**

Property 42: Public profile data display
*For any* public profile view, the response should include stats, equipped badges, and featured projects
**Validates: Requirements 9.2**

Property 43: Sensitive data filtering
*For any* public profile view, sensitive data (email, private projects) should not be present in the response
**Validates: Requirements 9.3**

Property 44: Private profile access control
*For any* private profile, non-authenticated viewers should receive a 403 error response
**Validates: Requirements 9.5**

Property 45: Skill tree progress display
*For any* public profile view, unlocked skill nodes should be visually distinguished from locked nodes
**Validates: Requirements 9.6**

### Task Requirement Properties

Property 46: Requirement display
*For any* locked skill node, the displayed information should include required_level, task requirements, and parent node requirements
**Validates: Requirements 10.1**

Property 47: Task progress tracking
*For any* skill node with task requirements, the system should track current progress toward each requirement (e.g., 2/3 projects with Python)
**Validates: Requirements 10.3**

## Error Handling

### Validation Errors

**User Input Validation**:
- Project creation: Validate required fields (name, description), URL formats, file uploads
- Profile updates: Validate email format, avatar file type/size, bio length
- Skill node unlock: Validate user meets all requirements before processing
- Badge equip: Validate badge ownership and slot availability

**Error Response Format**:
```php
return response()->json([
    'success' => false,
    'message' => 'Validation failed',
    'errors' => [
        'field_name' => ['Error message 1', 'Error message 2']
    ]
], 422);
```

### Business Logic Errors

**Insufficient Requirements**:
- Attempting to unlock skill node without meeting level requirement
- Attempting to unlock child node without parent unlocked
- Attempting to equip more than 6 badges

**Error Handling Strategy**:
- Return user-friendly error messages
- Log errors for debugging
- Maintain data consistency (use database transactions)
- Provide actionable feedback (e.g., "You need level 10 to unlock this node. You are currently level 7.")

### Database Errors

**Constraint Violations**:
- Duplicate email on registration
- Foreign key violations on deletion
- Unique constraint violations on slugs

**Handling**:
- Catch Eloquent exceptions
- Return appropriate HTTP status codes (409 for conflicts)
- Provide clear error messages to users
- Log full error details for developers

### File System Errors

**PDF Generation Failures**:
- Template file not found
- Insufficient disk space
- Permission errors

**Handling**:
- Validate template exists before generation
- Check disk space availability
- Provide fallback error page
- Queue PDF generation for retry on failure

### External Service Errors

**Future AI Integration**:
- API rate limits
- Network timeouts
- Invalid responses

**Handling**:
- Implement retry logic with exponential backoff
- Cache results when possible
- Provide graceful degradation (manual project selection if AI fails)
- Display user-friendly error messages

## Testing Strategy

### Dual Testing Approach

The LvlUp platform requires both unit testing and property-based testing for comprehensive coverage:

**Unit Tests** (Pest PHP):
- Test specific examples and edge cases
- Test integration points between components
- Test error conditions and validation
- Test UI rendering with specific data
- Focus on concrete scenarios

**Property-Based Tests** (Pest PHP with custom generators):
- Test universal properties across randomized inputs
- Verify correctness properties hold for all valid data
- Generate random users, projects, skills, badges
- Run minimum 100 iterations per property test
- Focus on general correctness

### Property-Based Testing Configuration

**Library**: Implement custom property-based testing using Pest PHP's dataset feature with random data generators

**Test Structure**:
```php
it('validates Property N: [property description]', function () {
    // Feature: lvlup-platform, Property N: [property text]
    
    // Generate random test data
    $user = User::factory()->create();
    $project = Project::factory()->make();
    
    // Execute action
    $user->projects()->save($project);
    
    // Assert property holds
    expect($user->fresh()->xp)->toBeGreaterThan(0);
    expect($user->fresh()->total_xp)->toBeGreaterThan(0);
})->repeat(100);
```

**Tagging Convention**:
Each property test must include a comment with the format:
```php
// Feature: lvlup-platform, Property N: [property text from design]
```

### Test Coverage Goals

**Unit Tests**:
- Controller actions: Test each CRUD operation
- Model methods: Test XP calculations, badge checks, unlock validation
- Service classes: Test resume analysis, skill tree building
- Edge cases: First project, level-up boundaries, badge limits
- Error conditions: Invalid input, insufficient permissions, missing data

**Property Tests**:
- All 47 correctness properties from design document
- Each property implemented as a single test
- Minimum 100 iterations per test
- Random data generation for users, projects, skills, badges

**Integration Tests**:
- Full user workflows: Register → Create project → Earn badge → Unlock node
- Resume generation: Input job description → Analyze → Generate PDF
- Public profile: Create profile → Toggle visibility → View as guest

### Test Data Generation

**Factories** (Laravel Factories):
```php
User::factory()->create([
    'level' => fake()->numberBetween(1, 100),
    'xp' => fake()->numberBetween(0, 1000),
    'rank' => fake()->randomElement(['Bronze', 'Silver', 'Gold'])
]);

Project::factory()->create([
    'name' => fake()->sentence(3),
    'description' => fake()->paragraph(),
    'xp_reward' => fake()->numberBetween(50, 200)
]);
```

**Seeders** (Database Seeders):
- Seed skill library with common technologies
- Seed skill tree with career path nodes
- Seed badges with various rarities and thresholds
- Seed sample users with progression for demo

### Testing Commands

```bash
# Run all tests
composer test

# Run specific test file
php artisan test --filter=ProjectTest

# Run property tests only
php artisan test --filter=Property

# Run with coverage
php artisan test --coverage

# Run specific property test
php artisan test --filter="Property 8"
```

### Continuous Integration

**GitHub Actions Workflow**:
- Run tests on every push and pull request
- Test against PHP 8.2 and 8.3
- Test with SQLite and MySQL
- Generate coverage reports
- Fail build if tests fail or coverage drops below 80%

