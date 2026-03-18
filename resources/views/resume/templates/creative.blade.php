<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resume – {{ $user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            color: #2d3748;
            background: #fff;
            line-height: 1.55;
        }

        /* Two-column layout */
        .container { display: table; width: 100%; min-height: 100%; }
        .sidebar {
            display: table-cell;
            width: 34%;
            background: linear-gradient(160deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 36px 24px;
            vertical-align: top;
        }
        .main {
            display: table-cell;
            width: 66%;
            padding: 36px 32px;
            vertical-align: top;
        }

        /* Sidebar */
        .sidebar h1 { font-size: 20px; font-weight: 700; line-height: 1.2; margin-bottom: 4px; }
        .sidebar .role { font-size: 11px; opacity: 0.85; margin-bottom: 4px; }
        .sidebar .contact { font-size: 10px; opacity: 0.75; margin-bottom: 22px; }
        .sidebar .section-title {
            font-size: 8px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 2px; opacity: 0.75;
            border-bottom: 1px solid rgba(255,255,255,0.25);
            padding-bottom: 4px; margin-bottom: 9px; margin-top: 20px;
        }
        .sidebar .text-block { font-size: 10.5px; opacity: 0.92; line-height: 1.6; white-space: pre-wrap; }
        .sidebar .skill-tag {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            color: #fff; border-radius: 20px;
            padding: 2px 9px; font-size: 10px;
            margin: 2px 3px 2px 0;
        }

        /* Main */
        .section { margin-bottom: 20px; }
        .section-title {
            font-size: 8px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 2px;
            color: #667eea;
            border-bottom: 1px solid #e9d8fd;
            padding-bottom: 3px; margin-bottom: 9px;
        }

        .project { margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #f0e6ff; }
        .project:last-child { border-bottom: none; }
        .project-title { font-weight: 600; font-size: 11px; color: #44337a; margin-bottom: 2px; }
        .project-body { color: #4a5568; font-size: 10.5px; line-height: 1.55; white-space: pre-wrap; }

        .text-block { color: #4a5568; font-size: 10.5px; line-height: 1.65; white-space: pre-wrap; }

        .footer {
            position: fixed; bottom: 14px; left: 0; right: 0;
            text-align: center; font-size: 8px; color: #a0aec0;
        }
    </style>
</head>
<body>
<div class="container">
    {{-- Sidebar --}}
    <div class="sidebar">
        <h1>{{ $user->name }}</h1>
        <div class="role">{{ $resume->job_title }}</div>
        <div class="contact">{{ $user->email }}</div>

        @if(!empty($resumeData['summary']))
        <div class="section-title">About Me</div>
        <div class="text-block">{{ $resumeData['summary'] }}</div>
        @endif

        @if(!empty($resumeData['skills']))
        <div class="section-title">Skills</div>
        @foreach(explode(',', $resumeData['skills']) as $skill)
            @if(trim($skill))<span class="skill-tag">{{ trim($skill) }}</span>@endif
        @endforeach
        @endif

        @if(!empty($resumeData['education']))
        <div class="section-title">Education</div>
        <div class="text-block">{{ $resumeData['education'] }}</div>
        @endif
    </div>

    {{-- Main --}}
    <div class="main">
        @if(!empty($resumeData['projects']))
        <div class="section">
            <div class="section-title">Featured Projects</div>
            @foreach(explode("\n\n", $resumeData['projects']) as $block)
                @if(trim($block))
                @php $lines = explode("\n", trim($block)); $title = ltrim(array_shift($lines), '*# '); $body = implode("\n", $lines); @endphp
                <div class="project">
                    <div class="project-title">{{ $title }}</div>
                    <div class="project-body">{{ $body }}</div>
                </div>
                @endif
            @endforeach
        </div>
        @endif

        @if(!empty($resumeData['experience']))
        <div class="section">
            <div class="section-title">Experience</div>
            <div class="text-block">{{ $resumeData['experience'] }}</div>
        </div>
        @endif
    </div>
</div>
<div class="footer">
    Generated {{ now()->format('F j, Y') }} &middot; {{ $resume->job_title }}
    @if($resume->match_score) &middot; {{ $resume->match_score }}% match @endif
</div>
</body>
</html>
