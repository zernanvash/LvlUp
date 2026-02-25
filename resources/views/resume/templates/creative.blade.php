<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resume - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: #2d3748;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 35%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
        }
        .main-content {
            width: 65%;
            padding: 40px 50px;
        }
        h1 {
            font-size: 36px;
            margin-bottom: 10px;
            font-weight: 700;
        }
        h2 {
            font-size: 18px;
            margin-top: 30px;
            margin-bottom: 15px;
            font-weight: 600;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .sidebar h2 {
            color: white;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 8px;
        }
        .job-title {
            font-size: 16px;
            margin-bottom: 20px;
            opacity: 0.9;
        }
        .contact-info {
            font-size: 13px;
            margin-bottom: 30px;
            opacity: 0.95;
            line-height: 1.8;
        }
        .section {
            margin-bottom: 30px;
        }
        .project {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        .project:last-child {
            border-bottom: none;
        }
        .project-title {
            font-weight: 600;
            font-size: 16px;
            color: #1a202c;
            margin-bottom: 8px;
        }
        .project-description {
            line-height: 1.7;
            color: #4a5568;
            font-size: 14px;
        }
        .skills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }
        .skill-tag {
            background: #edf2f7;
            color: #4a5568;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .sidebar .skill-tag {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        .about {
            line-height: 1.8;
            color: #4a5568;
            font-size: 14px;
        }
        .sidebar .about {
            color: white;
            opacity: 0.95;
        }
        .experience-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
        }
        .experience-level {
            font-size: 13px;
            line-height: 1.8;
        }
        .level-number {
            font-size: 32px;
            font-weight: 700;
            display: block;
            margin-bottom: 5px;
        }
        .rank-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.3);
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h1>{{ $user->name }}</h1>
            @if($user->title)
                <div class="job-title">{{ $user->title }}</div>
            @endif
            <div class="contact-info">
                {{ $user->email }}
            </div>

            @if($user->bio)
                <div class="section">
                    <h2>About Me</h2>
                    <div class="about">{{ $user->bio }}</div>
                </div>
            @endif

            @if($skills->count() > 0)
                <div class="section">
                    <h2>Skills</h2>
                    <div class="skills">
                        @foreach($skills as $skill)
                            <span class="skill-tag">{{ $skill->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="section">
                <h2>Experience Level</h2>
                <div class="experience-badge">
                    <span class="level-number">{{ $user->level }}</span>
                    <div class="experience-level">
                        <span class="rank-badge">{{ $user->rank }} Rank</span><br>
                        {{ number_format($user->total_xp) }} Total XP
                    </div>
                </div>
            </div>
        </div>

        <div class="main-content">
            @if($projects->count() > 0)
                <div class="section">
                    <h2>Featured Projects</h2>
                    @foreach($projects as $project)
                        <div class="project">
                            <div class="project-title">{{ $project->name }}</div>
                            <div class="project-description">{{ $project->description }}</div>
                            @if($project->skills->count() > 0)
                                <div class="skills">
                                    @foreach($project->skills as $skill)
                                        <span class="skill-tag">{{ $skill->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</body>
</html>
