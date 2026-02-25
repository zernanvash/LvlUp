<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resume – {{ $user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
            line-height: 1.55;
        }
        .page { padding: 36px 44px; }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        .header h1 { font-size: 24px; color: #1e40af; letter-spacing: 0.5px; }
        .header .role { font-size: 13px; color: #2563eb; font-weight: 600; margin-top: 3px; }
        .header .contact { font-size: 10px; color: #64748b; margin-top: 4px; }

        /* Sections */
        .section { margin-bottom: 18px; }
        .section-title {
            font-size: 8px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 2px;
            color: #1e40af;
            border-bottom: 1px solid #bfdbfe;
            padding-bottom: 3px; margin-bottom: 9px;
        }

        /* Skills */
        .skill-tag {
            display: inline-block;
            background: #2563eb; color: #fff;
            border-radius: 4px;
            padding: 2px 8px; font-size: 10px;
            margin: 2px 3px 2px 0;
        }

        /* Projects */
        .project { margin-bottom: 11px; padding-left: 10px; border-left: 3px solid #2563eb; background: #f8fafc; padding: 7px 10px; border-radius: 3px; }
        .project-title { font-weight: 700; font-size: 11px; color: #1e40af; }
        .project-body { color: #475569; font-size: 10.5px; margin-top: 2px; line-height: 1.5; }

        /* Text blocks */
        .text-block { color: #475569; font-size: 10.5px; line-height: 1.6; white-space: pre-wrap; }

        /* Footer */
        .footer {
            position: fixed; bottom: 20px; left: 44px; right: 44px;
            text-align: center; font-size: 8px; color: #94a3b8;
            border-top: 1px solid #f1f5f9; padding-top: 5px;
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
        <div class="section-title">Professional Summary</div>
        <div class="text-block">{{ $resumeData['summary'] }}</div>
    </div>
    @endif

    @if(!empty($resumeData['skills']))
    <div class="section">
        <div class="section-title">Skills</div>
        @foreach(explode(',', $resumeData['skills']) as $skill)
            @if(trim($skill))<span class="skill-tag">{{ trim($skill) }}</span>@endif
        @endforeach
    </div>
    @endif

    @if(!empty($resumeData['projects']))
    <div class="section">
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
        <div class="section-title">Experience</div>
        <div class="text-block">{{ $resumeData['experience'] }}</div>
    </div>
    @endif

    @if(!empty($resumeData['education']))
    <div class="section">
        <div class="section-title">Education</div>
        <div class="text-block">{{ $resumeData['education'] }}</div>
    </div>
    @endif
</div>
<div class="footer">
    Generated {{ now()->format('F j, Y') }} &middot; {{ $resume->job_title }}
    @if($resume->match_score) &middot; Keyword Match: {{ $resume->match_score }}% @endif
</div>
</body>
</html>
