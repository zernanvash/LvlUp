# Requirements Document: LvlUp Platform

## Introduction

LvlUp is a gamified knowledge management system that transforms skill acquisition and portfolio building into an RPG-like experience. The platform enables developers and students to track their coding projects, visualize skill progression through an interactive skill tree, earn achievements through badges, and generate AI-powered resumes tailored to specific job descriptions. The system combines traditional portfolio management with game mechanics (XP, levels, ranks, daily rewards) to maintain user engagement and motivation.

## Glossary

- **User**: A registered developer or student using the LvlUp platform
- **Project**: A coding project uploaded by a User with associated skills, description, and links
- **XP (Experience Points)**: Points earned through activities that contribute to leveling up
- **Level**: A numeric representation of User progression, increased by accumulating XP
- **Rank**: A tier classification (Bronze, Silver, Gold, Platinum, Diamond, Master) based on Level
- **Task_Requirement**: A specific action or milestone that must be completed to unlock a Skill_Node (e.g., "Upload 3 projects with Python tag")
- **Skill**: A technology or programming language tag (e.g., Python, React, Laravel)
- **Skill_Node**: A node in the skill tree representing a career milestone or specialization
- **Skill_Tree**: A visual graph of interconnected Skill_Nodes with parent-child dependencies
- **Badge**: An achievement award with rarity tiers earned by completing milestones
- **Rarity**: A classification system for Badges and Skills (Common, Uncommon, Rare, Epic, Legendary, Mythic)

- **Resume**: An AI-generated document matching User projects to job requirements
- **Job_Description**: Text input describing a target job position for resume generation
- **Match_Score**: A percentage indicating how well a User's profile matches a Job_Description
- **Root_Node**: The first Skill_Node in the Skill_Tree, unlocked by uploading the first Project
- **Tier**: A progression level within the Skill_Tree (1-5) indicating advancement depth
- **Profile**: A User's public or private page displaying stats, badges, and projects
- **Equipped_Badge**: A Badge selected by the User to display on their Profile
- **Threshold**: A numeric requirement for earning a Badge (e.g., "10 projects")
- **Proficiency**: A numeric level (1-5) indicating expertise with a Skill on a Project

## Requirements

### Requirement 1: User Authentication and Profile Management

**User Story:** As a developer, I want to register and manage my profile, so that I can track my progress and showcase my work.

#### Acceptance Criteria

1. WHEN a new user registers, THE System SHALL create a User account with initial values (level=1, xp=0, rank=Bronze)
2. WHEN a User logs in, THE System SHALL update the last_login timestamp
3. THE System SHALL allow Users to update profile fields (name, bio, title, avatar)
4. WHEN a User views their profile, THE System SHALL display current stats (level, xp, rank, total_xp)
5. THE System SHALL support both public and private profile visibility settings
6. WHEN a User equips a Badge, THE System SHALL mark it as displayed on the Profile

### Requirement 2: Project Management

**User Story:** As a developer, I want to add and manage my coding projects, so that I can build my portfolio and earn progression rewards.

#### Acceptance Criteria

1. WHEN a User creates a Project, THE System SHALL store the project details (name, description, url, github_url, language, thumbnail)
2. WHEN a User creates a Project, THE System SHALL award XP to the User based on the xp_reward value
3. WHEN a User creates their first Project, THE System SHALL unlock the Root_Node in the Skill_Tree
4. THE System SHALL allow Users to attach Skills to Projects with proficiency levels (1-5)
5. WHEN a User edits a Project, THE System SHALL update the project details without awarding additional XP
6. WHEN a User deletes a Project, THE System SHALL remove the Project but preserve earned XP and unlocked Skill_Nodes
7. THE System SHALL allow Users to mark Projects as featured for prominent display
8. WHEN a User uploads a Project, THE System SHALL check for Badge eligibility and award earned Badges

### Requirement 3: Experience and Leveling System

**User Story:** As a user, I want to earn XP and level up, so that I can unlock new features and feel progression.

#### Acceptance Criteria

1. WHEN a User earns XP, THE System SHALL add the XP to both current xp and total_xp fields
2. WHEN a User's current xp reaches or exceeds the XP needed for next level, THE System SHALL increment the level by 1
3. WHEN a User levels up, THE System SHALL subtract the required XP from current xp
4. THE System SHALL calculate XP needed for next level using the formula: 100 * (level ^ 1.5)
5. WHEN a User levels up, THE System SHALL update the Rank based on level thresholds (1=Bronze, 10=Silver, 25=Gold, 50=Platinum, 75=Diamond, 100=Master)
6. THE System SHALL display XP progress as a percentage toward the next level

### Requirement 4: Skill Tree System

**User Story:** As a user, I want to explore and unlock nodes in a skill tree, so that I can visualize my career path and specialization.

#### Acceptance Criteria

1. THE System SHALL maintain a Skill_Tree with Skill_Nodes connected by parent-child relationships
2. WHEN a User views the Skill_Tree, THE System SHALL display all nodes with visual indicators for locked, unlocked, and available states
3. WHEN a User completes a Task_Requirement for a Skill_Node, THE System SHALL mark the node as available for unlock
4. WHEN a User attempts to unlock a Skill_Node, THE System SHALL verify the User meets the required_level requirement
5. WHEN a User attempts to unlock a Skill_Node with a parent, THE System SHALL verify the parent node is already unlocked
6. WHEN a User attempts to unlock a Skill_Node, THE System SHALL verify all Task_Requirements are completed
7. WHEN a User unlocks a Skill_Node, THE System SHALL record the unlock timestamp in the user_skill_nodes pivot table
8. THE System SHALL organize Skill_Nodes into career path branches (Web Developer, Cybersecurity, Software Engineer, Full Stack, Mobile Dev, DevOps)
9. THE System SHALL assign each Skill_Node a tier value (1-5) indicating progression depth
10. THE System SHALL position Skill_Nodes with x_position and y_position coordinates for visual rendering
11. WHEN a User uploads their first Project, THE System SHALL automatically unlock the Root_Node

### Requirement 5: Badge and Achievement System

**User Story:** As a user, I want to earn badges for completing milestones, so that I can showcase my achievements and feel rewarded.

#### Acceptance Criteria

1. WHEN a User completes a milestone, THE System SHALL check for eligible Badges and award them automatically
2. WHEN a Badge is awarded, THE System SHALL record the earned_at timestamp in the user_badges pivot table
3. WHEN a Badge is awarded, THE System SHALL grant the xp_reward to the User
4. THE System SHALL classify Badges by rarity (Common, Uncommon, Rare, Epic, Legendary, Mythic)
5. THE System SHALL classify Badges by category (project, skill, level, special)
6. WHEN a User earns a skill-based Badge, THE System SHALL verify the User has completed the threshold number of Projects with the required_skill_id
7. WHEN a User earns a project-count Badge, THE System SHALL verify the User has created at least threshold Projects
8. THE System SHALL allow Users to equip up to 6 Badges for display on their Profile
9. WHEN a User views their Badge collection, THE System SHALL display earned and unearned Badges with progress indicators

### Requirement 6: AI Resume Builder

**User Story:** As a user, I want to generate a tailored resume for a job description, so that I can highlight relevant projects and skills.

#### Acceptance Criteria

1. WHEN a User inputs a Job_Description, THE System SHALL analyze the text for technology keywords and requirements
2. WHEN generating a Resume, THE System SHALL match the User's Projects and Skills to the Job_Description keywords
3. WHEN generating a Resume, THE System SHALL calculate a Match_Score representing the percentage of keywords matched
4. WHEN generating a Resume, THE System SHALL rank Projects by relevance to the Job_Description
5. WHEN generating a Resume, THE System SHALL select the top N most relevant Projects (configurable, default 5)
6. WHEN generating a Resume, THE System SHALL store the selected_project_ids and selected_skill_ids in the Resume record
7. THE System SHALL allow Users to manually adjust the selected Projects and Skills before finalizing
8. WHEN a Resume is finalized, THE System SHALL generate a PDF document using a template
9. THE System SHALL store the pdf_path for later retrieval and download
10. THE System SHALL support multiple Resume templates (Modern, Classic, Minimal, Creative)

### Requirement 7: Skill Management and Tagging

**User Story:** As a user, I want to tag my projects with skills, so that the system can track my expertise and suggest relevant opportunities.

#### Acceptance Criteria

1. THE System SHALL maintain a library of Skills with properties (name, slug, icon, color, category, rarity, required_level)
2. WHEN a User attaches a Skill to a Project, THE System SHALL record the proficiency level (1-5) in the project_skill pivot table
3. WHEN a User creates a Skill tag that doesn't exist, THE System SHALL create a new Skill with default values
4. THE System SHALL categorize Skills (frontend, backend, database, devops, mobile, security, data_science, other)
5. THE System SHALL assign rarity levels to Skills based on usage frequency or manual classification
6. WHEN a User views a Skill, THE System SHALL display all Projects tagged with that Skill
7. THE System SHALL calculate a User's proficiency with a Skill based on the number of Projects and average proficiency level

### Requirement 8: Gamification UI and Visual Design

**User Story:** As a user, I want an engaging and visually appealing interface, so that using the platform feels rewarding and enjoyable.

#### Acceptance Criteria

1. THE System SHALL display animated gradient backgrounds with particle effects on key pages
2. THE System SHALL apply rarity-based color schemes to Badges and Skills (Common=gray, Rare=blue, Epic=purple, Legendary=orange, Mythic=pink)
3. THE System SHALL render the Skill_Tree as an interactive node graph with connecting lines between parent and child nodes
4. WHEN a User hovers over a Skill_Node, THE System SHALL display a tooltip with title, description, requirements, and cost
5. THE System SHALL use custom fonts (Orbitron for headings, Rajdhani for body text)
6. THE System SHALL implement smooth transitions and hover effects for interactive elements
7. THE System SHALL display star-field backdrops and glow effects for high-rarity items
8. THE System SHALL be responsive and functional on mobile and desktop devices

### Requirement 9: Public Profile and Sharing

**User Story:** As a user, I want to share my public profile, so that I can showcase my portfolio to potential employers or collaborators.

#### Acceptance Criteria

1. THE System SHALL generate a unique public URL for each User's Profile
2. WHEN a public Profile is viewed, THE System SHALL display the User's stats, equipped Badges, and featured Projects
3. WHEN a public Profile is viewed, THE System SHALL hide sensitive information (email, private projects)
4. THE System SHALL allow Users to toggle profile visibility between public and private
5. WHEN a Profile is private, THE System SHALL return a 403 error for non-authenticated viewers
6. THE System SHALL display the Skill_Tree progress on public Profiles with unlocked nodes highlighted

### Requirement 10: Skill Tree Node Unlock Requirements

**User Story:** As a user, I want to see clear requirements for unlocking skill tree nodes, so that I know what actions to take to progress.

#### Acceptance Criteria

1. WHEN a User views a locked Skill_Node, THE System SHALL display the required_level, Task_Requirements, and parent node requirements
2. THE System SHALL support task-based unlock requirements (e.g., "Upload 3 projects with Python tag", "Reach level 10", "Earn 5 badges")
3. WHEN a Skill_Node has a task requirement, THE System SHALL track progress toward that requirement
4. WHEN a User completes all Task_Requirements for a Skill_Node, THE System SHALL mark the Skill_Node as available for unlock
5. THE System SHALL prevent unlocking a Skill_Node if any requirement is not met
6. THE System SHALL store Task_Requirements as structured data (type, target, current_progress, required_progress)

### Requirement 11: Badge Display and Customization

**User Story:** As a user, I want to customize which badges are displayed on my profile, so that I can highlight my most impressive achievements.

#### Acceptance Criteria

1. THE System SHALL allow Users to select up to 6 Badges to display on their Profile
2. WHEN a User equips a Badge, THE System SHALL set is_displayed=true in the user_badges pivot table
3. WHEN a User unequips a Badge, THE System SHALL set is_displayed=false
4. WHEN a User attempts to equip more than 6 Badges, THE System SHALL prevent the action and display an error message
5. THE System SHALL display equipped Badges in the order they were equipped

