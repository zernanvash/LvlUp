<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resume – {{ $user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10.5px;
            color: #e2e8f0;
            background: #0f172a;
            line-height: 1.6;
        }
        .page { padding: 36px 48px; }

        /* Header — terminal style */
        .header { margin-bottom: 28px; }
        .prompt { color: #22d3ee; font-size: 10px; margin-bottom: 4px; }
        .header h1 { font-size: 22px; color: #f8fafc; font-weight: 700; letter-spacing: 1px; }
        .header .role { font-size: 12px; color: #818cf8; margin-top: 3px; }
        .header .contact { font-size: 10px; color: #64748b; margin-top: 3px; }

        /* Terminal divider */
        .divider { color: #334155; font-size: 10px; margin: 14px 0; letter-spacing: 1px; }

        /* Sections */
        .section { margin-bottom: 20px; }
        .section-title {
            font-size: 10px; color: #22d3ee;
            margin-bottom: 9px;
        }
        .section-title::before { content: "# "; color: #475569; }

        /* Summary */
        .summary { color: #94a3b8; font-size: 10.5px; line-height: 1.65; font-family: Arial, sans-serif; }

        /* Skills — code tag style */
        .skill-tag {
            display: inline-block;
            background: #1e293b;
            color: #7dd3fc;
            border: 1px solid #334155;
            border-radius: 3px;
            padding: 1px 7px; font-size: 10px;
            margin: 2px 3px 2px 0;
        }

        /* Projects */
        .project { margin-bottom: 14px; padding: 10px 14px; background: #1e293b; border-left: 3px solid #818cf8; border-radius: 2px; }
        .project-title { font-weight: 700; font-size: 11px; color: #c084fc; margin-bottom: 4px; }
        .project-body { color: #94a3b8; font-size: 10px; line-height: 1.55; font-family: Arial, sans-serif; white-space: pre-wrap; }

        /* Text blocks */
        .text-block { color: #94a3b8; font-size: 10.5px; line-height: 1.65; font-family: Arial, sans-serif; white-space: pre-wrap; }

        /* Score */
        .match-badge {
            display: inline-block;
            background: #134e4a;
            color: #6ee7b7;
            border: 1px solid #047857;
            border-radius: 3px;
            padding: 1px 9px; font-size: 9.5px;
            margin-top: 6px;
        }

        .footer {
            position: fixed; bottom: 16px; left: 48px; right: 48px;
            text-align: center; font-size: 8px; color: #334155;
            border-top: 1px solid #1e293b; padding-top: 5px;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="prompt">~/resume $ cat profile.json</div>
        <h1>{{ $user->name }}</h1>
        <div class="role">{{ $resume->job_title }}</div>
        <div class="contact">{{ $user->email }}</div>
        @if($resume->match_score)
        <div><span class="match-badge">✓ {{ $resume->match_score }}% keyword match</span></div>
        @endif
    </div>

    <div class="divider">─────────────────────────────────────────────────────</div>

    @if(!empty($resumeData['summary']))
    <div class="section">
        <div class="section-title">summary</div>
        <div class="summary">{{ $resumeData['summary'] }}</div>
    </div>
    @endif

    @if(!empty($resumeData['skills']))
    <div class="section">
        <div class="section-title">stack</div>
        @foreach(explode(',', $resumeData['skills']) as $skill)
            @if(trim($skill))<span class="skill-tag">{{ trim($skill) }}</span>@endif
        @endforeach
    </div>
    @endif

    @if(!empty($resumeData['projects']))
    <div class="section">
        <div class="section-title">projects</div>
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
        <div class="section-title">experience</div>
        <div class="text-block">{{ $resumeData['experience'] }}</div>
    </div>
    @endif

    @if(!empty($resumeData['education']))
    <div class="section">
        <div class="section-title">education</div>
        <div class="text-block">{{ $resumeData['education'] }}</div>
    </div>
    @endif

    <div class="divider">─────────────────────────────────────────────────────</div>
</div>
<div class="footer">
    generated {{ now()->format('Y-m-d') }} // {{ $resume->job_title }}
</div>
</body>
</html>
