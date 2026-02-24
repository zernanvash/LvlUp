# Implementation Plan: LvlUp Platform

## Overview

This implementation plan breaks down the LvlUp platform development into discrete, incremental tasks. Each task builds on previous work, with testing integrated throughout. The plan follows Laravel conventions and integrates with the existing codebase (models, controllers, views already partially implemented).

The implementation focuses on enhancing existing functionality, adding missing features (skill tree task requirements, resume builder, public profiles), and ensuring correctness through comprehensive testing.

## Tasks

- [x] 1. Database schema updates and migrations
  - Review existing migrations and identify missing fields/tables
  - Create migration for task_requirements JSON column in skill_nodes table
  - Create migration to remove gacha_currency and streak_days from users table
  - Create migration to remove skill_point_cost from skill_nodes table
  - Create migration to remove gacha_currency_reward from badges table
  - Run migrations and verify schema matches design
  - _Requirements: 1.1, 4.6, 10.6_

- [x] 2. Update User model for simplified gamification
  - [x] 2.1 Remove skill points from level-up logic
    - Update addXP() method to remove skill point awarding
    - Remove skill_points field references
    - Update xpNeededForNextLevel() to ensure formula is correct
    - _Requirements: 3.1, 3.2, 3.3, 3.4_

  - [ ]* 2.2 Write property tests for User XP system
    - **Property 8: Project XP award** - For any project creation, user's xp and total_xp should increase by xp_reward
    - **Property 14: Level-up trigger** - For any user whose xp reaches xpNeededForNextLevel(), level should increment
    - **Property 15: XP overflow preservation** - For any level-up, excess XP should be preserved
    - **Property 16: XP formula correctness** - For any level, xpNeededForNextLevel() should return 100 * (level ^ 1.5)
    - **Property 17: Rank progression** - For any user level, rank should match correct tier
    - **Property 18: XP progress percentage** - For any user, xpProgress() should return correct percentage
    - **Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5, 3.6**

  - [x] 2.3 Remove daily reward and streak logic
    - Remove checkDailyLogin() method
    - Remove canClaimDailyReward() method
    - Remove claimDailyReward() method
    - Remove dailyRewards() relationship
    - _Requirements: 1.1, 1.2_

  - [ ]* 2.4 Write property tests for User initialization and profile
    - **Property 1: User initialization** - For any new user, should have level=1, xp=0, rank='Bronze'
    - **Property 2: Login timestamp update** - For any login, last_login should be updated
    - **Property 3: Profile update persistence** - For any profile update, changes should persist
    - **Validates: Requirements 1.1, 1.2, 1.3**

- [x] 3. Enhance Project model with badge checking
  - [x] 3.1 Update checkBadges() method
    - Remove gacha_currency reward logic
    - Ensure XP rewards are granted correctly
    - Test with skill-based and project-count badges
    - _Requirements: 2.8, 5.1, 5.2, 5.3_

  - [ ]* 3.2 Write property tests for Project management
    - **Property 7: Project creation persistence** - For any project data, all fields should persist
    - **Property 9: Skill attachment with proficiency** - For any project-skill, proficiency should be recorded
    - **Property 10: Project update preserves XP** - For any project update, XP should remain unchanged
    - **Property 11: Project deletion preserves progression** - For any deletion, total_xp and nodes should remain
    - **Property 12: Featured project flag** - For any project, is_featured should be settable
    - **Property 13: Automatic badge awarding** - For any threshold met, badge should be awarded
    - **Validates: Requirements 2.1, 2.4, 2.5, 2.6, 2.7, 2.8, 5.1, 5.2, 5.3**

- [x] 4. Implement skill tree task requirements system
  - [x] 4.1 Add task requirement methods to SkillNode model
    - Implement checkTaskRequirements(User $user): array method
    - Implement calculateTaskProgress(User $user): array method
    - Update canBeUnlockedBy() to check task requirements
    - Support task types: project_count, skill_projects, badge_count, level_requirement
    - _Requirements: 4.3, 4.6, 10.3, 10.4_

  - [x] 4.2 Update SkillNode unlock logic
    - Remove skill point cost checking
    - Add task requirement validation
    - Ensure parent node checking still works
    - Ensure level requirement checking still works
    - _Requirements: 4.4, 4.5, 4.6, 10.5_

  - [ ]* 4.3 Write property tests for skill tree system
    - **Property 19: Parent-child relationships** - For any node with parent, relationship should be queryable
    - **Property 20: Node state calculation** - For any node and user, state should be correct (locked/available/unlocked)
    - **Property 21: Task completion marks availability** - For any node with tasks, completion should mark available
    - **Property 22: Unlock validation** - For any unlock attempt, all requirements should be verified
    - **Property 23: Unlock persistence** - For any unlock, should be recorded with timestamp
    - **Property 47: Task progress tracking** - For any node with tasks, progress should be tracked
    - **Validates: Requirements 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7, 10.3**

  - [x] 4.3 Create seeder for skill tree with task requirements
    - Seed root node: "Taking the First Steps" (unlocked by first project)
    - Seed career path branches with task requirements
    - Seed example nodes with various task types
    - _Requirements: 4.8, 4.11_

- [x] 5. Checkpoint - Ensure all tests pass
  - Run all unit and property tests
  - Verify database schema is correct
  - Test XP system, project creation, skill tree unlocking manually
  - Ask the user if questions arise

- [x] 6. Implement badge system enhancements
  - [x] 6.1 Update Badge model
    - Remove gacha_currency_reward field references
    - Implement checkEligibility(User $user): bool method
    - Ensure getRarityColorAttribute() includes all rarity tiers
    - _Requirements: 5.4, 5.6, 5.7_

  - [x] 6.2 Implement badge equipping system
    - Add equipBadge($badgeId) method to User model
    - Add unequipBadge($badgeId) method to User model
    - Enforce 6 badge limit
    - Track equip order via pivot table created_at
    - _Requirements: 5.8, 11.1, 11.2, 11.3, 11.4, 11.5_

  - [ ]* 6.3 Write property tests for badge system
    - **Property 6: Badge equip/unequip** - For any badge, equipping/unequipping should set is_displayed correctly
    - **Property 24: Badge eligibility checking** - For any badge with threshold, should be awarded at threshold
    - **Property 25: Badge equip limit** - For any user, equipping more than 6 should be rejected
    - **Property 26: Badge collection display** - For any user, both earned and unearned badges should display
    - **Property 27: Badge display ordering** - For any user, badges should display in equip order
    - **Validates: Requirements 1.6, 5.6, 5.7, 5.8, 5.9, 11.1, 11.2, 11.3, 11.4, 11.5**

- [x] 7. Create BadgeController
  - [x] 7.1 Implement badge viewing and management
    - index() - Display all badges with earned status and progress
    - show($badgeId) - Display badge details
    - equip($badgeId) - Equip badge to profile (validate limit)
    - unequip($badgeId) - Remove badge from profile
    - Add routes for badge actions
    - _Requirements: 5.9, 11.1, 11.2, 11.3, 11.4_

  - [ ]* 7.2 Write unit tests for BadgeController
    - Test index displays all badges
    - Test equip enforces 6 badge limit
    - Test unequip removes badge
    - Test equip requires badge ownership
    - _Requirements: 5.9, 11.1, 11.4_

- [x] 8. Implement ResumeAnalyzer service
  - [x] 8.1 Create ResumeAnalyzer service class
    - Implement extractKeywords(string $jobDescription): array
    - Implement scoreProject(Project $project, array $keywords): float
    - Implement rankProjects(Collection $projects, array $keywords): Collection
    - Implement calculateMatchScore(User $user, array $keywords): float
    - Use weighted scoring: skill tags (3x), description (2x), name (1x)
    - _Requirements: 6.1, 6.2, 6.3, 6.4_

  - [ ]* 8.2 Write property tests for ResumeAnalyzer
    - **Property 28: Keyword extraction** - For any job description, keywords should be extracted
    - **Property 29: Project-keyword matching** - For any resume, projects should match keywords
    - **Property 30: Match score calculation** - For any resume, match score should be percentage of keywords
    - **Property 31: Project relevance ranking** - For any resume, projects should be ranked by relevance
    - **Validates: Requirements 6.1, 6.2, 6.3, 6.4**

- [x] 9. Implement resume generation system
  - [x] 9.1 Update Resume model
    - Implement getSelectedProjects(): Collection (already exists, verify)
    - Implement getSelectedSkills(): Collection (already exists, verify)
    - Implement generatePDF(string $template): string method
    - Use Laravel PDF library (e.g., barryvdh/laravel-dompdf)
    - _Requirements: 6.8, 6.9, 6.10_

  - [x] 9.2 Create ResumeController
    - index() - List user's resumes
    - create() - Show resume builder form
    - analyze() - Analyze job description, return ranked projects (AJAX)
    - store() - Save resume configuration
    - update($resumeId) - Update selected projects/skills
    - generate($resumeId) - Generate PDF
    - download($resumeId) - Serve PDF file
    - Add routes for resume actions
    - _Requirements: 6.5, 6.6, 6.7, 6.8, 6.9_

  - [ ]* 9.3 Write property tests for resume system
    - **Property 32: Top N project selection** - For any resume with N, exactly N projects should be selected
    - **Property 33: Resume data persistence** - For any resume, selected IDs should be stored as JSON
    - **Property 34: Manual selection updates** - For any resume, users should be able to update selections
    - **Property 35: PDF generation** - For any finalized resume, PDF should be generated and path stored
    - **Property 36: Template selection** - For any resume with template, PDF should use that template
    - **Validates: Requirements 6.5, 6.6, 6.7, 6.8, 6.9, 6.10**

- [x] 10. Checkpoint - Ensure all tests pass
  - Run all unit and property tests
  - Test badge equipping and limits
  - Test resume generation workflow manually
  - Ask the user if questions arise

- [x] 11. Implement public profile system
  - [x] 11.1 Add profile visibility to User model
    - Add is_public boolean field via migration
    - Add toggleVisibility() method
    - Add getPublicUrl() method
    - _Requirements: 1.5, 9.1, 9.4_

  - [x] 11.2 Update ProfileController
    - show($username) - Display public profile (check visibility)
    - Implement access control for private profiles (403 error)
    - Filter sensitive data (email, private projects)
    - Display stats, equipped badges, featured projects
    - Display skill tree progress with unlocked nodes
    - _Requirements: 9.2, 9.3, 9.4, 9.5, 9.6_

  - [ ]* 11.3 Write property tests for public profiles
    - **Property 5: Profile visibility toggle** - For any user, toggling should update setting
    - **Property 41: Unique profile URLs** - For any two users, URLs should be unique
    - **Property 42: Public profile data display** - For any public profile, should include stats, badges, projects
    - **Property 43: Sensitive data filtering** - For any public profile, sensitive data should not be present
    - **Property 44: Private profile access control** - For any private profile, non-auth viewers should get 403
    - **Property 45: Skill tree progress display** - For any public profile, unlocked nodes should be distinguished
    - **Validates: Requirements 1.5, 9.1, 9.2, 9.3, 9.4, 9.5, 9.6**

- [x] 12. Implement skill management features
  - [x] 12.1 Update Skill model
    - Ensure auto-creation works in Project::attachSkillsFromTags()
    - Implement user proficiency calculation method
    - Add method to get all projects for a skill
    - _Requirements: 7.3, 7.6, 7.7_

  - [ ]* 12.2 Write property tests for skill management
    - **Property 37: Auto-create skills** - For any non-existent skill tag, should be created with defaults
    - **Property 38: Skill-project relationship** - For any skill, should return all projects with that skill
    - **Property 39: User skill proficiency calculation** - For any user and skill, proficiency should be average
    - **Validates: Requirements 7.3, 7.6, 7.7**

- [x] 13. Create SkillTreeController
  - [x] 13.1 Implement skill tree viewing and interaction
    - index() - Display skill tree with user's progress
    - show($nodeId) - Show node details, requirements, and progress
    - unlock($nodeId) - Attempt to unlock node (validate all requirements)
    - progress() - Return JSON of user's unlock progress and task progress
    - Add routes for skill tree actions
    - _Requirements: 4.2, 4.3, 10.1, 10.3_

  - [ ]* 13.2 Write unit tests for SkillTreeController
    - Test index displays all nodes with correct states
    - Test unlock validates all requirements
    - Test unlock rejects insufficient level
    - Test unlock rejects missing parent
    - Test unlock rejects incomplete tasks
    - Test progress returns correct task progress
    - _Requirements: 4.2, 4.4, 4.5, 4.6, 10.1, 10.3_

- [x] 14. Create skill tree visualization views
  - [x] 14.1 Build interactive skill tree UI
    - Create skill-tree/index.blade.php with node graph
    - Use Alpine.js for node interactions (hover, click)
    - Display node states with visual indicators (locked/available/unlocked)
    - Show connecting lines between parent and child nodes
    - Implement tooltip with node details on hover
    - Display task requirements and progress
    - _Requirements: 4.2, 8.3, 8.4, 10.1_

  - [x] 14.2 Style skill tree with gamification theme
    - Apply rarity-based colors to nodes
    - Add glow effects for available nodes
    - Implement smooth transitions and animations
    - Ensure responsive design for mobile
    - _Requirements: 8.2, 8.6, 8.8_

- [x] 15. Create resume builder views
  - [x] 15.1 Build resume creation workflow
    - Create resumes/create.blade.php with job description input
    - Create resumes/analyze.blade.php with project selection
    - Use Alpine.js for dynamic project selection
    - Display match scores and relevance indicators
    - Allow manual project reordering
    - _Requirements: 6.1, 6.2, 6.4, 6.7_

  - [x] 15.2 Create resume templates
    - Create PDF templates: Modern, Classic, Minimal, Creative
    - Use Blade components for reusable template sections
    - Ensure templates display projects, skills, and user info
    - _Requirements: 6.10_

- [x] 16. Create badge collection views
  - [x] 16.1 Build badge display and management UI
    - Create achievements/index.blade.php with badge grid
    - Display earned and unearned badges with progress
    - Show rarity colors and glow effects
    - Implement badge equip/unequip with Alpine.js
    - Display equipped badges on profile
    - Enforce 6 badge limit in UI
    - _Requirements: 5.9, 8.2, 8.7, 11.1, 11.4_

- [x] 17. Create public profile views
  - [x] 17.1 Build public profile display
    - Create profile/public.blade.php
    - Display user stats (level, xp, rank)
    - Display equipped badges
    - Display featured projects
    - Display skill tree progress
    - Hide sensitive information
    - _Requirements: 9.2, 9.3, 9.6_

  - [x] 17.2 Add profile visibility toggle
    - Add toggle to profile/edit.blade.php
    - Display public URL when profile is public
    - Show privacy indicator on profile
    - _Requirements: 1.5, 9.1, 9.4_

- [x] 18. Enhance dashboard with gamification elements
  - [x] 18.1 Update dashboard.blade.php
    - Display XP progress bar with level and rank
    - Show next level requirements
    - Display equipped badges
    - Show recent achievements
    - Display skill tree progress summary
    - Add quick links to skill tree, badges, projects
    - _Requirements: 1.4, 3.6_

  - [x] 18.2 Apply gamification styling
    - Use animated gradient backgrounds
    - Add particle effects (optional, can use CSS or JS library)
    - Apply custom fonts (Orbitron, Rajdhani)
    - Implement smooth transitions
    - _Requirements: 8.1, 8.5, 8.6_

- [ ] 19. Implement UI property tests
  - [ ]* 19.1 Write property tests for UI rendering
    - **Property 4: Profile stats display** - For any profile view, should contain level, xp, rank, total_xp
    - **Property 40: Rarity color mapping** - For any badge/skill, color should match rarity
    - **Property 46: Requirement display** - For any locked node, should display all requirements
    - **Validates: Requirements 1.4, 8.2, 10.1**

- [ ] 20. Create comprehensive seeders
  - [ ] 20.1 Create DatabaseSeeder with sample data
    - Seed 5 sample users with varying progression
    - Seed 20+ projects across users
    - Seed skill library with 30+ technologies
    - Seed skill tree with 50+ nodes across career paths
    - Seed badges with all rarity tiers
    - Seed some users with equipped badges
    - Seed some users with unlocked skill nodes
    - _Requirements: All_

- [ ] 21. Final checkpoint - Comprehensive testing
  - Run full test suite (unit + property tests)
  - Test all user workflows end-to-end
  - Verify gamification mechanics work correctly
  - Test resume generation with real job descriptions
  - Test skill tree unlocking with various requirements
  - Test badge awarding and equipping
  - Test public profile access control
  - Ask the user if questions arise

- [ ] 22. Documentation and polish
  - [ ] 22.1 Update README with feature documentation
    - Document gamification system
    - Document skill tree mechanics
    - Document resume builder usage
    - Document badge system
    - Add screenshots of key features

  - [ ] 22.2 Code cleanup and optimization
    - Run Laravel Pint for code formatting
    - Remove unused code and comments
    - Optimize database queries (eager loading)
    - Add missing docblocks
    - Clear all caches

## Notes

- Tasks marked with `*` are optional property/unit tests and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- Property tests validate universal correctness properties (minimum 100 iterations each)
- Unit tests validate specific examples and edge cases
- The implementation builds on existing models and controllers, enhancing rather than replacing
- Focus on Laravel best practices: thin controllers, fat models, service classes for complex logic
- Use Eloquent relationships and query builder for all database operations
- Use Blade components for reusable UI elements
- Use Alpine.js for client-side interactivity without heavy JavaScript frameworks

