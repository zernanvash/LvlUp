<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resume - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 50px 70px;
            color: #333;
            font-size: 11pt;
            line-height: 1.5;
        }
        h1 {
            font-size: 32px;
            font-weight: 300;
            margin-bottom: 5px;
            color: #000;
        }
        h2 {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 30px;
            margin-bottom: 12px;
            color: #000;
        }
        .header {
            margin-bottom: 40px;
        }
        .contact-info {
            font-size: 10pt;
            color: #666;
            margin-top: 3px;
        }
        .job-title {
            font-size: 14px;
            color: #666;
            font-weight: 300;
            margin-top: 3px;
        }
        .section {
            margin-bottom: 30px;
        }
        .project {
            margin-bottom: 20px;
            padding-left: 0;
        }
        .project-title {
            font-weight: 500;
            font-size: 12pt;
            margin-bottom: 4px;
            color: #000;
        }
        .project-description {
            color: #555;
            font-size: 10pt;
            line-height: 1.6;
        }
        .skills {
            margin-top: 8px;
        }
        .skill-tag {
            display: inline-block;
            color: #666;
            font-size: 9pt;
            margin-right: 12px;
            margin-bottom: 4px;
        }
        .skill-tag:before {
            content: "•";
            margin-right: 6px;
            color: #999;
        }
        .about {
            color: #555;
            font-size: 10pt;
            line-height: 1.7;
        }
        .experience-level {
            font-size: 10pt;
            color: #666;
        }
        .divider {
            height: 1px;
            background: #e0e0e0;
            margin: 25px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $user->name }}</h1>
        @if($user->title)
            <div class="job-title">{{ $user->title }}</div>
        @endif
        <div class="contact-info">{{ $user->email }}</div>
    </div>

    @if($user->bio)
        <div class="section">
            <h2>About</h2>
            <div class="about">{{ $user->bio }}</div>
        </div>
        <div class="divider"></div>
    @endif

    @if($projects->count() > 0)
        <div class="section">
            <h2>Projects</h2>
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
        <div class="divider"></div>
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
        <div class="divider"></div>
    @endif

    <div class="section">
        <h2>Experience</h2>
        <div class="experience-level">
            Level {{ $user->level }} • {{ $user->rank }} Rank • {{ number_format($user->total_xp) }} XP
        </div>
    </div>
</body>
</html>
