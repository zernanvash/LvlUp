<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resume - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 40px 60px;
            color: #000;
            line-height: 1.6;
        }
        h1 {
            font-size: 28px;
            text-align: center;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        h2 {
            font-size: 16px;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-top: 25px;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
        }
        .contact-info {
            text-align: center;
            font-size: 12px;
            margin-top: 8px;
        }
        .job-title {
            font-size: 14px;
            font-style: italic;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 25px;
        }
        .project {
            margin-bottom: 18px;
            page-break-inside: avoid;
        }
        .project-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .project-description {
            margin-left: 20px;
            text-align: justify;
        }
        .skills {
            margin-left: 20px;
            margin-top: 8px;
        }
        .skill-item {
            display: inline;
            margin-right: 15px;
            font-size: 13px;
        }
        .skill-item:after {
            content: " •";
            margin-left: 15px;
        }
        .skill-item:last-child:after {
            content: "";
        }
        .about {
            text-align: justify;
            margin-left: 20px;
            font-style: italic;
        }
        .experience-level {
            margin-left: 20px;
            font-size: 13px;
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
            <h2>Professional Summary</h2>
            <div class="about">{{ $user->bio }}</div>
        </div>
    @endif

    @if($projects->count() > 0)
        <div class="section">
            <h2>Projects & Portfolio</h2>
            @foreach($projects as $project)
                <div class="project">
                    <div class="project-title">{{ $project->name }}</div>
                    <div class="project-description">{{ $project->description }}</div>
                    @if($project->skills->count() > 0)
                        <div class="skills">
                            @foreach($project->skills as $skill)
                                <span class="skill-item">{{ $skill->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if($skills->count() > 0)
        <div class="section">
            <h2>Technical Skills</h2>
            <div class="skills">
                @foreach($skills as $skill)
                    <span class="skill-item">{{ $skill->name }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <div class="section">
        <h2>Professional Development</h2>
        <div class="experience-level">
            <strong>Level {{ $user->level }}</strong> - {{ $user->rank }} Rank Developer<br>
            Total Experience Points: {{ number_format($user->total_xp) }}
        </div>
    </div>
</body>
</html>
