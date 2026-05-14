<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\SkillNode;
use Illuminate\Database\Seeder;

class SkillTreeSeeder extends Seeder
{
    public function run(): void
    {
        SkillNode::truncate();

        // ─── Skills ───────────────────────────────────────────────────────────
        $skills = [
            ['name' => 'Web Development',    'slug' => 'web-dev',    'icon' => 'fas fa-globe',       'color' => '#3b82f6', 'category' => 'frontend', 'rarity' => 'common'],
            ['name' => 'Backend Engineering','slug' => 'backend',    'icon' => 'fas fa-server',      'color' => '#8b5cf6', 'category' => 'backend',  'rarity' => 'rare'],
            ['name' => 'Database Design',    'slug' => 'database',   'icon' => 'fas fa-database',    'color' => '#10b981', 'category' => 'backend',  'rarity' => 'epic'],
            ['name' => 'DevOps & Cloud',     'slug' => 'devops',     'icon' => 'fas fa-cloud',       'color' => '#f59e0b', 'category' => 'devops',   'rarity' => 'legendary'],
            ['name' => 'Mobile Dev',         'slug' => 'mobile',     'icon' => 'fas fa-mobile-alt',  'color' => '#ec4899', 'category' => 'mobile',   'rarity' => 'rare'],
            ['name' => 'AI & ML',            'slug' => 'ai',         'icon' => 'fas fa-brain',       'color' => '#a78bfa', 'category' => 'ai',       'rarity' => 'legendary'],
            ['name' => 'Full Stack',         'slug' => 'fullstack',  'icon' => 'fas fa-layer-group', 'color' => '#f97316', 'category' => 'fullstack','rarity' => 'epic'],
        ];

        foreach ($skills as $s) {
            Skill::firstOrCreate(['slug' => $s['slug']], $s);
        }

        $web      = Skill::where('slug', 'web-dev')->first()->id;
        $backend  = Skill::where('slug', 'backend')->first()->id;
        $db       = Skill::where('slug', 'database')->first()->id;
        $devops   = Skill::where('slug', 'devops')->first()->id;
        $mobile   = Skill::where('slug', 'mobile')->first()->id;
        $ai       = Skill::where('slug', 'ai')->first()->id;
        $fs       = Skill::where('slug', 'fullstack')->first()->id;

        // ─── Layout ───────────────────────────────────────────────────────────
        // Canvas: 1400×1200px  |  rendered as: left = x*12px, top = y*11px
        //
        // 5 columns (x positions):
        //   Far-Left  (Frontend)  : x = 8
        //   Left      (CSS/Design): x = 24
        //   Center    (Git/API)   : x = 50
        //   Right     (DevOps)    : x = 76
        //   Far-Right (CLI/Shell) : x = 92
        //
        // Tiers (y positions):
        //   core       y =  5
        //   basic      y = 18
        //   advanced   y = 36
        //   master     y = 58
        //   legendary  y = 80 / 90
        //
        // Node IDs (insertion order, 1-based):
        //  1  Hello World        (core,     x=50, y=5)
        //  2  HTML/CSS           (basic,    x=8,  y=18)
        //  3  Git Flow           (basic,    x=50, y=18)
        //  4  CLI Basics         (basic,    x=92, y=18)
        //  5  JS Awakening       (basic,    x=24, y=18)  ← child of 2
        //  6  Branching Strategy (basic,    x=50, y=30)  ← child of 3
        //  7  Shell Scripting    (basic,    x=92, y=30)  ← child of 4
        //  8  Responsive Design  (basic,    x=8,  y=30)  ← child of 2
        //  9  React/Vue          (advanced, x=8,  y=44)  ← child of 5
        // 10  CSS Architecture   (advanced, x=24, y=44)  ← child of 8
        // 11  REST API Builder   (advanced, x=50, y=44)  ← child of 6
        // 12  Linux Admin        (advanced, x=76, y=44)  ← child of 7
        // 13  Docker             (advanced, x=92, y=44)  ← child of 7
        // 14  Auth & Security    (advanced, x=50, y=57)  ← child of 11
        // 15  DB Design          (master,   x=50, y=68)  ← child of 14
        // 16  SQL Mastery        (master,   x=38, y=68)  ← child of 14
        // 17  NoSQL & Caching    (master,   x=62, y=68)  ← child of 14
        // 18  Full Stack Dev     (master,   x=24, y=57)  ← child of 10
        // 19  CI/CD Pipeline     (master,   x=76, y=57)  ← child of 12
        // 20  Mobile Developer   (master,   x=8,  y=57)  ← child of 9
        // 21  Microservices      (legendary,x=50, y=80)  ← child of 15
        // 22  Cloud Engineer     (legendary,x=76, y=80)  ← child of 19
        // 23  AI Integration     (legendary,x=24, y=80)  ← child of 18
        // 24  ML Engineer        (legendary,x=24, y=90)  ← child of 23
        // 25  Full Stack Legend  (legendary,x=50, y=90)  ← child of 21

        $nodes = [

            // ── CORE (root) ──────────────────────────────────────────────────
            [   // ID 1
                'title'       => 'Hello, World!',
                'description' => 'Every developer\'s origin story. Ship your first project — it doesn\'t have to be perfect, it just has to exist.',
                'skill_id'    => $web,
                'parent_node_id' => null,
                'x_position'  => 50, 'y_position' => 5,
                'tier'        => 'core',
                'required_level' => 1,
                'task_requirements' => [
                    ['type' => 'project_count', 'required' => 1, 'description' => 'Ship your first project of any kind'],
                ],
            ],

            // ── BASIC TIER ───────────────────────────────────────────────────
            [   // ID 2
                'title'       => 'HTML & CSS Craftsman',
                'description' => 'Structure and style are the bones and skin of the web. Prove you can build something that looks intentional.',
                'skill_id'    => $web,
                'parent_node_id' => 1,
                'x_position'  => 8, 'y_position' => 18,
                'tier'        => 'basic',
                'required_level' => 1,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'web', 'required' => 1, 'description' => 'Add 1 Web / Frontend project'],
                ],
            ],
            [   // ID 3
                'title'       => 'Git Flow',
                'description' => 'Commit early, commit often. Branching, merging, and not losing your work at 2am.',
                'skill_id'    => $web,
                'parent_node_id' => 1,
                'x_position'  => 50, 'y_position' => 18,
                'tier'        => 'basic',
                'required_level' => 1,
                'task_requirements' => [
                    ['type' => 'project_count', 'required' => 2, 'description' => 'Add 2 projects (link GitHub repos to show version control usage)'],
                ],
            ],
            [   // ID 4
                'title'       => 'CLI Basics',
                'description' => 'The terminal is your superpower. Navigate, create, move, delete — without touching a mouse.',
                'skill_id'    => $backend,
                'parent_node_id' => 1,
                'x_position'  => 92, 'y_position' => 18,
                'tier'        => 'basic',
                'required_level' => 1,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'backend', 'required' => 1, 'description' => 'Add 1 Backend project (shows you\'re comfortable in a server environment)'],
                ],
            ],
            [   // ID 5
                'title'       => 'JavaScript Awakening',
                'description' => 'The language of the web. DOM manipulation, events, and the first taste of making things actually do stuff.',
                'skill_id'    => $web,
                'parent_node_id' => 2,
                'x_position'  => 24, 'y_position' => 18,
                'tier'        => 'basic',
                'required_level' => 2,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'web', 'required' => 2, 'description' => 'Add 2 Web / Frontend projects using JavaScript'],
                ],
            ],
            [   // ID 6
                'title'       => 'Branching Strategy',
                'description' => 'Feature branches, pull requests, code review. The workflow that keeps teams sane.',
                'skill_id'    => $backend,
                'parent_node_id' => 3,
                'x_position'  => 50, 'y_position' => 30,
                'tier'        => 'basic',
                'required_level' => 3,
                'task_requirements' => [
                    ['type' => 'project_count', 'required' => 3, 'description' => 'Add 3 projects with GitHub links showing branch history'],
                ],
            ],
            [   // ID 7
                'title'       => 'Shell Scripting',
                'description' => 'Automate the boring stuff. Bash scripts that save you hours of repetitive work.',
                'skill_id'    => $backend,
                'parent_node_id' => 4,
                'x_position'  => 92, 'y_position' => 30,
                'tier'        => 'basic',
                'required_level' => 3,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'devops', 'required' => 1, 'description' => 'Add 1 DevOps / Automation project'],
                ],
            ],
            [   // ID 8
                'title'       => 'Responsive Design',
                'description' => 'Your app should look good on a phone, a tablet, and a 4K monitor. Mobile-first is not optional anymore.',
                'skill_id'    => $web,
                'parent_node_id' => 2,
                'x_position'  => 8, 'y_position' => 30,
                'tier'        => 'basic',
                'required_level' => 2,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'web', 'required' => 2, 'description' => 'Add 2 Web projects (demonstrate responsive layouts)'],
                ],
            ],

            // ── ADVANCED TIER ────────────────────────────────────────────────
            [   // ID 9
                'title'       => 'React / Vue Practitioner',
                'description' => 'Component-based thinking. State, props, lifecycle — the modern way to build UIs.',
                'skill_id'    => $web,
                'parent_node_id' => 5,
                'x_position'  => 8, 'y_position' => 44,
                'tier'        => 'advanced',
                'required_level' => 4,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'web', 'required' => 3, 'description' => 'Add 3 Web projects using a component framework (React, Vue, etc.)'],
                ],
            ],
            [   // ID 10
                'title'       => 'CSS Architecture',
                'description' => 'BEM, utility-first, CSS modules — writing styles that don\'t become a nightmare at scale.',
                'skill_id'    => $web,
                'parent_node_id' => 8,
                'x_position'  => 24, 'y_position' => 44,
                'tier'        => 'advanced',
                'required_level' => 4,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'web', 'required' => 3, 'description' => 'Add 3 Web projects demonstrating structured CSS (Tailwind, SCSS, etc.)'],
                ],
            ],
            [   // ID 11
                'title'       => 'REST API Builder',
                'description' => 'Design and build APIs that other developers actually want to use. Resources, status codes, and clean contracts.',
                'skill_id'    => $backend,
                'parent_node_id' => 6,
                'x_position'  => 50, 'y_position' => 44,
                'tier'        => 'advanced',
                'required_level' => 4,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'backend', 'required' => 2, 'description' => 'Add 2 Backend / API projects'],
                ],
            ],
            [   // ID 12
                'title'       => 'Linux Administration',
                'description' => 'Servers run Linux. Permissions, processes, networking — the foundation of everything in production.',
                'skill_id'    => $devops,
                'parent_node_id' => 7,
                'x_position'  => 76, 'y_position' => 44,
                'tier'        => 'advanced',
                'required_level' => 4,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'devops', 'required' => 1, 'description' => 'Add 1 DevOps project (server setup, deployment, automation)'],
                ],
            ],
            [   // ID 13
                'title'       => 'Docker & Containers',
                'description' => 'If it works in a container, it works everywhere. Build images, write Compose files, stop saying "works on my machine".',
                'skill_id'    => $devops,
                'parent_node_id' => 7,
                'x_position'  => 92, 'y_position' => 44,
                'tier'        => 'advanced',
                'required_level' => 6,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'devops', 'required' => 2, 'description' => 'Add 2 DevOps projects (one should involve containerization)'],
                ],
            ],

            // ── MASTER TIER ──────────────────────────────────────────────────
            [   // ID 14
                'title'       => 'Auth & Security',
                'description' => 'JWT, OAuth, sessions, hashing. Knowing how to keep users\' data safe is non-negotiable.',
                'skill_id'    => $backend,
                'parent_node_id' => 11,
                'x_position'  => 50, 'y_position' => 57,
                'tier'        => 'master',
                'required_level' => 5,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'backend', 'required' => 3, 'description' => 'Add 3 Backend projects (at least one should implement authentication)'],
                ],
            ],
            [   // ID 15
                'title'       => 'Database Design',
                'description' => 'Normalization, indexes, foreign keys, migrations. A well-designed schema is the backbone of every serious app.',
                'skill_id'    => $db,
                'parent_node_id' => 14,
                'x_position'  => 50, 'y_position' => 68,
                'tier'        => 'master',
                'required_level' => 6,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'backend', 'required' => 3, 'description' => 'Add 3 Backend projects with database usage'],
                    ['type' => 'project_type', 'project_type' => 'fullstack', 'required' => 1, 'description' => 'Add 1 Full Stack project with a real data model'],
                ],
            ],
            [   // ID 16
                'title'       => 'SQL Mastery',
                'description' => 'JOINs, subqueries, window functions, query optimization. SQL is 50 years old and still runs the world.',
                'skill_id'    => $db,
                'parent_node_id' => 14,
                'x_position'  => 38, 'y_position' => 68,
                'tier'        => 'master',
                'required_level' => 7,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'backend', 'required' => 4, 'description' => 'Add 4 Backend projects demonstrating database work'],
                ],
            ],
            [   // ID 17
                'title'       => 'NoSQL & Caching',
                'description' => 'MongoDB, Redis, DynamoDB — knowing when NOT to use a relational DB is just as important as knowing SQL.',
                'skill_id'    => $db,
                'parent_node_id' => 14,
                'x_position'  => 62, 'y_position' => 68,
                'tier'        => 'master',
                'required_level' => 7,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'backend', 'required' => 3, 'description' => 'Add 3 Backend projects (one using a NoSQL or caching layer)'],
                    ['type' => 'project_type', 'project_type' => 'fullstack', 'required' => 1, 'description' => 'Add 1 Full Stack project'],
                ],
            ],
            [   // ID 18
                'title'       => 'Full Stack Developer',
                'description' => 'You own the whole stack — frontend, backend, database. You can take an idea from zero to deployed.',
                'skill_id'    => $fs,
                'parent_node_id' => 10,
                'x_position'  => 24, 'y_position' => 57,
                'tier'        => 'master',
                'required_level' => 7,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'fullstack', 'required' => 2, 'description' => 'Add 2 Full Stack projects (frontend + backend + database)'],
                    ['type' => 'project_type', 'project_type' => 'web', 'required' => 2, 'description' => 'Add 2 Web projects'],
                    ['type' => 'project_type', 'project_type' => 'backend', 'required' => 2, 'description' => 'Add 2 Backend projects'],
                ],
            ],
            [   // ID 19
                'title'       => 'CI/CD Pipeline',
                'description' => 'Automated testing, building, and deploying. Push to main, watch it go live. That\'s the dream.',
                'skill_id'    => $devops,
                'parent_node_id' => 12,
                'x_position'  => 76, 'y_position' => 57,
                'tier'        => 'master',
                'required_level' => 8,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'devops', 'required' => 3, 'description' => 'Add 3 DevOps projects (one should include a CI/CD pipeline)'],
                ],
            ],
            [   // ID 20
                'title'       => 'Mobile Developer',
                'description' => 'React Native, Flutter, or Swift/Kotlin — building for the device in everyone\'s pocket.',
                'skill_id'    => $mobile,
                'parent_node_id' => 9,
                'x_position'  => 8, 'y_position' => 57,
                'tier'        => 'master',
                'required_level' => 6,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'mobile', 'required' => 2, 'description' => 'Add 2 Mobile projects (iOS, Android, or cross-platform)'],
                ],
            ],

            // ── LEGENDARY TIER ───────────────────────────────────────────────
            [   // ID 21
                'title'       => 'Microservices Architect',
                'description' => 'Break the monolith. Independent services, message queues, service discovery — distributed systems thinking.',
                'skill_id'    => $backend,
                'parent_node_id' => 15,
                'x_position'  => 50, 'y_position' => 80,
                'tier'        => 'legendary',
                'required_level' => 10,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'backend', 'required' => 5, 'description' => 'Add 5 Backend projects'],
                    ['type' => 'project_type', 'project_type' => 'devops', 'required' => 2, 'description' => 'Add 2 DevOps projects (deployment infrastructure)'],
                    ['type' => 'project_type', 'project_type' => 'fullstack', 'required' => 2, 'description' => 'Add 2 Full Stack projects'],
                ],
            ],
            [   // ID 22
                'title'       => 'Cloud Engineer',
                'description' => 'AWS, GCP, or Azure — deploying, scaling, and monitoring production systems in the cloud.',
                'skill_id'    => $devops,
                'parent_node_id' => 19,
                'x_position'  => 76, 'y_position' => 80,
                'tier'        => 'legendary',
                'required_level' => 10,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'devops', 'required' => 4, 'description' => 'Add 4 DevOps / Cloud projects'],
                    ['type' => 'project_type', 'project_type' => 'backend', 'required' => 3, 'description' => 'Add 3 Backend projects deployed to cloud'],
                ],
            ],
            [   // ID 23
                'title'       => 'AI Integration Engineer',
                'description' => 'LLMs, embeddings, RAG pipelines — building products that use AI as a feature, not a gimmick.',
                'skill_id'    => $ai,
                'parent_node_id' => 18,
                'x_position'  => 24, 'y_position' => 80,
                'tier'        => 'legendary',
                'required_level' => 9,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'ai', 'required' => 2, 'description' => 'Add 2 AI / ML projects'],
                    ['type' => 'project_type', 'project_type' => 'backend', 'required' => 3, 'description' => 'Add 3 Backend projects (AI needs a backend)'],
                ],
            ],
            [   // ID 24
                'title'       => 'ML Engineer',
                'description' => 'Training models, feature engineering, evaluation metrics, deployment. The full machine learning lifecycle.',
                'skill_id'    => $ai,
                'parent_node_id' => 23,
                'x_position'  => 24, 'y_position' => 90,
                'tier'        => 'legendary',
                'required_level' => 12,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'ai', 'required' => 4, 'description' => 'Add 4 AI / ML projects (show the full ML workflow)'],
                ],
            ],
            [   // ID 25
                'title'       => 'Full Stack Legend',
                'description' => 'You\'ve shipped across every layer of the stack. Frontend, backend, database, DevOps, mobile, AI. You are the whole team.',
                'skill_id'    => $fs,
                'parent_node_id' => 21,
                'x_position'  => 50, 'y_position' => 90,
                'tier'        => 'legendary',
                'required_level' => 15,
                'task_requirements' => [
                    ['type' => 'project_type', 'project_type' => 'fullstack', 'required' => 3, 'description' => 'Add 3 Full Stack projects'],
                    ['type' => 'project_type', 'project_type' => 'backend',   'required' => 5, 'description' => 'Add 5 Backend projects'],
                    ['type' => 'project_type', 'project_type' => 'web',       'required' => 4, 'description' => 'Add 4 Web / Frontend projects'],
                    ['type' => 'project_type', 'project_type' => 'devops',    'required' => 2, 'description' => 'Add 2 DevOps projects'],
                ],
            ],
        ];

        foreach ($nodes as $nodeData) {
            SkillNode::create($nodeData);
        }
    }
}
