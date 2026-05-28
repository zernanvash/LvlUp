# LvlUp — File Map

Quick reference for which files to edit when changing a feature or its styles.

> **General rule:** view = `resources/views/` · logic = `app/Http/Controllers/` · data rules = `app/Models/`

---

## Global / Shared

| What to change | File(s) |
|---|---|
| Sidebar, header, navigation | `resources/views/layouts/app.blade.php` |
| Toast notifications, level-up overlay | `resources/views/layouts/app.blade.php` |
| Global CSS (animations, glow effects, scrollbar, fonts) | `resources/views/layouts/app.blade.php` → `<style>` block |
| Auth pages layout (login/register wrapper) | `resources/views/layouts/guest.blade.php` |
| All routes | `routes/web.php`, `routes/auth.php` |

---

## Dashboard

| What to change | File(s) |
|---|---|
| Page layout, XP bar, stats cards, streak banner | `resources/views/dashboard.blade.php` |
| Data logic (streak, XP milestone, bonus multiplier) | `app/Http/Controllers/DashboardController.php` |
| XP / level / rank calculations | `app/Models/User.php` |

---

## Skill Tree

| What to change | File(s) |
|---|---|
| Canvas, node visuals, connection lines, modal UI | `resources/views/skill-tree/index.blade.php` |
| Node unlock logic, AJAX responses | `app/Http/Controllers/SkillTreeController.php` |
| Node requirements, `canBeUnlockedBy()` | `app/Models/SkillNode.php` |
| Node definitions, positions, tiers | `database/seeders/SkillTreeSeeder.php` |

---

## Achievements / Badges

| What to change | File(s) |
|---|---|
| Achievements page, equipped panel, badge cards, equip buttons | `resources/views/achievements/index.blade.php` |
| Equip / unequip / toggle endpoints | `app/Http/Controllers/BadgeController.php` |
| Badge eligibility logic, `checkEligibility()` | `app/Models/Badge.php` |
| Badge definitions (title, icon, rarity, XP reward) | `database/seeders/BadgeSeeder.php` |
| Auto-award badges when a project is created | `app/Models/Project.php` → `checkBadges()` |

---

## Projects

| What to change | File(s) |
|---|---|
| Projects list page | `resources/views/projects/index.blade.php` |
| Create project form | `resources/views/projects/create.blade.php` |
| Edit project form | `resources/views/projects/edit.blade.php` |
| Project detail page | `resources/views/projects/show.blade.php` |
| CRUD logic, XP award, badge/node checks | `app/Http/Controllers/ProjectController.php` |
| Project model, skill attachment | `app/Models/Project.php` |

---

## Resume Builder

| What to change | File(s) |
|---|---|
| Resume list page | `resources/views/resumes/index.blade.php` |
| Create resume form | `resources/views/resumes/create.blade.php` |
| Resume detail / preview | `resources/views/resumes/show.blade.php` |
| Template: Classic | `resources/views/resumes/templates/classic.blade.php` |
| Template: Modern | `resources/views/resumes/templates/modern.blade.php` |
| Template: Minimal | `resources/views/resumes/templates/minimal.blade.php` |
| Template: Creative | `resources/views/resumes/templates/creative.blade.php` |
| Resume CRUD, AI generation | `app/Http/Controllers/ResumeController.php` |
| AI keyword analysis logic | `app/Services/ResumeAnalyzer.php` |
| AI writing / content generation | `app/Services/AiResumeWriter.php` |

---

## Profile

| What to change | File(s) |
|---|---|
| Profile edit page (tabs container) | `resources/views/profile/edit.blade.php` |
| Overview tab | `resources/views/profile/partials/overview.blade.php` |
| Update profile info form | `resources/views/profile/partials/update-profile-information-form.blade.php` |
| Resume details form | `resources/views/profile/partials/resume-details-form.blade.php` |
| Change password form | `resources/views/profile/partials/update-password-form.blade.php` |
| Public visibility toggles | `resources/views/profile/partials/update-profile-visibility.blade.php` |
| Delete account form | `resources/views/profile/partials/delete-user-form.blade.php` |
| Public profile page | `resources/views/profile/public.blade.php` |
| Profile update logic | `app/Http/Controllers/ProfileController.php` |

---

## Auth (Login / Register)

| What to change | File(s) |
|---|---|
| Login page | `resources/views/auth/login.blade.php` |
| Register page | `resources/views/auth/register.blade.php` |
| Auth logic (login, register, password reset) | `app/Http/Controllers/Auth/` (all files) |

---

## Database / Data

| What to change | File(s) |
|---|---|
| Add or change a database column | Create a new file in `database/migrations/` |
| Skill node definitions and positions | `database/seeders/SkillTreeSeeder.php` |
| Badge definitions | `database/seeders/BadgeSeeder.php` |
| Initial users / projects | `database/seeders/DatabaseSeeder.php` |
| Migrate SQLite → MySQL | `app/Console/Commands/MigrateSqliteToMysql.php` |

---

## Models (business logic / relationships)

| Model | File |
|---|---|
| User (XP, level, rank, streak, badges) | `app/Models/User.php` |
| Project (skills, XP reward, badge checks) | `app/Models/Project.php` |
| Badge (eligibility, rarity) | `app/Models/Badge.php` |
| Skill Node (requirements, unlock checks) | `app/Models/SkillNode.php` |
| Skill | `app/Models/Skill.php` |
| Resume | `app/Models/Resume.php` |
| Daily Reward | `app/Models/DailyReward.php` |
