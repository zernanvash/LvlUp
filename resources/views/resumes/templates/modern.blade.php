<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resume - {{ $user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            color: #333;
        }
        h1 {
            color: #2563eb;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
        }
        h2 {
            color: #1e40af;
            margin-top: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .contact-info {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 25px;
        }
        .project {
            margin-bottom: 20px;
            padding: 10px;
            border-left: 3px solid #2563eb;
            background: #f8fafc;
        }
        .project-title {
            font-weight: bold;
            font-size: 16px;
            color: #1e40af;
        }
        .project-description {
            margin-top: 5px;
            line-height: 1.6;
        }
        .skills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .skill-tag {
            background: #2563eb;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
        }
        .job-title {
            font-size: 18px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $user->name }}</h1>
        @if($user->title)
            <div class="job-title">{{ $user->title }}</div>
        @endif
        <div class="contact-info">
            {{ $user->email }}
        </div>
    </div>

    @if($user->bio)
        <div class="section">
            <h2>About</h2>
            <p>{{ $user->bio }}</p>
        </div>
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
        <p>Level {{ $user->level }} - {{ $user->rank }} Rank</p>
        <p>Total XP: {{ number_format($user->total_xp) }}</p>
    </div>
</body>
</html>
