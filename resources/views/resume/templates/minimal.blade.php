<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resume – {{ $user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            background: #fff;
            line-height: 1.55;
        }
        .page { padding: 48px 70px; }

        /* Header */
        .header { margin-bottom: 36px; }
        .header h1 { font-size: 28px; font-weight: 300; color: #000; letter-spacing: -0.5px; }
        .header .role { font-size: 12px; color: #666; font-weight: 300; margin-top: 3px; }
        .header .contact { font-size: 10px; color: #999; margin-top: 3px; }

        /* Sections */
        .section { margin-bottom: 28px; }
        .divider { height: 1px; background: #e0e0e0; margin-bottom: 14px; }
        .section-title {
            font-size: 9px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1.5px;
            color: #000; margin-bottom: 10px;
        }

        /* Skills */
        .skill-item {
            display: inline-block;
            font-size: 10px; color: #555;
            margin-right: 14px; margin-bottom: 3px;
        }
        .skill-item::before { content: "— "; color: #bbb; }

        /* Projects */
        .project { margin-bottom: 14px; }
        .project-title { font-weight: 500; font-size: 11px; color: #000; margin-bottom: 2px; }
        .project-body { color: #555; font-size: 10px; line-height: 1.6; white-space: pre-wrap; }

        /* Text blocks */
        .text-block { color: #555; font-size: 10.5px; line-height: 1.7; white-space: pre-wrap; }

        /* Footer */
        .footer {
            position: fixed; bottom: 20px; left: 70px; right: 70px;
            text-align: center; font-size: 8px; color: #bbb;
            border-top: 1px solid #eee; padding-top: 5px;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <h1>{{ $user->name }}</h1>
        <div class="role">{{ $resume->job_title }}</div>
        <div class="contact">{{ $user->email }}</div>
    </div>

    @if(!empty($resumeData['summary']))
    <div class="section">
        <div class="divider"></div>
        <div class="section-title">About</div>
        <div class="text-block">{{ $resumeData['summary'] }}</div>
    </div>
    @endif

    @if(!empty($resumeData['skills']))
    <div class="section">
        <div class="divider"></div>
        <div class="section-title">Skills</div>
        @foreach(explode(',', $resumeData['skills']) as $skill)
            @if(trim($skill))<span class="skill-item">{{ trim($skill) }}</span>@endif
        @endforeach
    </div>
    @endif

    @if(!empty($resumeData['projects']))
    <div class="section">
        <div class="divider"></div>
        <div class="section-title">Projects</div>
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
        <div class="divider"></div>
        <div class="section-title">Experience</div>
        <div class="text-block">{{ $resumeData['experience'] }}</div>
    </div>
    @endif

    @if(!empty($resumeData['education']))
    <div class="section">
        <div class="divider"></div>
        <div class="section-title">Education</div>
        <div class="text-block">{{ $resumeData['education'] }}</div>
    </div>
    @endif
</div>
<div class="footer">
    {{ now()->format('F j, Y') }} &middot; {{ $resume->job_title }}
    @if($resume->match_score) &middot; {{ $resume->match_score }}% match @endif
</div>
</body>
</html>
