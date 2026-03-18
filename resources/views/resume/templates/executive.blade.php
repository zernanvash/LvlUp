<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resume – {{ $user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 11px;
            color: #1a1a1a;
            background: #fff;
            line-height: 1.6;
        }
        .page { padding: 0 0 40px 0; }

        /* Dark premium header */
        .header {
            background: #1a1a2e;
            color: #fff;
            padding: 30px 50px;
            margin-bottom: 28px;
        }
        .header h1 { font-size: 26px; font-weight: 400; letter-spacing: 1.5px; text-transform: uppercase; }
        .header .role { font-size: 12px; color: #c4a882; font-style: italic; margin-top: 4px; letter-spacing: 0.5px; }
        .header .contact { font-size: 10px; color: #8892a4; margin-top: 5px; }
        .header .score-badge {
            display: inline-block;
            margin-top: 8px;
            background: rgba(196,168,130,0.2);
            color: #c4a882;
            border: 1px solid rgba(196,168,130,0.4);
            padding: 2px 10px; border-radius: 20px;
            font-size: 9.5px; font-family: Arial, sans-serif;
        }

        /* Body content */
        .body { padding: 0 50px; }

        /* Sections */
        .section { margin-bottom: 22px; }
        .section-title {
            font-size: 10px; font-family: Arial, sans-serif;
            font-weight: 700; text-transform: uppercase;
            letter-spacing: 2.5px; color: #1a1a2e;
            border-bottom: 1px solid #c4a882;
            padding-bottom: 4px; margin-bottom: 11px;
        }

        /* Summary */
        .summary { font-style: italic; color: #333; line-height: 1.7; }

        /* Skills */
        .skill-tag {
            display: inline-block;
            border: 1px solid #c4a882;
            color: #5c4a2a;
            border-radius: 2px;
            padding: 2px 9px; font-size: 9.5px;
            font-family: Arial, sans-serif;
            margin: 2px 4px 2px 0;
        }

        /* Projects */
        .project { margin-bottom: 13px; }
        .project-title { font-weight: bold; font-size: 11.5px; color: #1a1a2e; margin-bottom: 2px; }
        .project-body { color: #444; font-size: 10.5px; line-height: 1.6; white-space: pre-wrap; }

        /* Text blocks */
        .text-block { color: #444; font-size: 10.5px; line-height: 1.7; white-space: pre-wrap; }

        /* Accent line between sections */
        .accent { height: 1px; background: linear-gradient(to right, #c4a882, transparent); margin-bottom: 22px; }

        .footer {
            position: fixed; bottom: 16px; left: 50px; right: 50px;
            text-align: center; font-size: 8px; color: #999;
            font-family: Arial, sans-serif;
            border-top: 1px solid #e8e0d8; padding-top: 5px;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <h1>{{ $user->name }}</h1>
        <div class="role">{{ $resume->job_title }}</div>
        <div class="contact">{{ $user->email }}</div>
        @if($resume->match_score)
        <div><span class="score-badge">★ {{ $resume->match_score }}% keyword match</span></div>
        @endif
    </div>

    <div class="body">
        @if(!empty($resumeData['summary']))
        <div class="section">
            <div class="section-title">Executive Summary</div>
            <div class="summary">{{ $resumeData['summary'] }}</div>
        </div>
        @endif

        @if(!empty($resumeData['skills']))
        <div class="section">
            <div class="section-title">Core Competencies</div>
            @foreach(explode(',', $resumeData['skills']) as $skill)
                @if(trim($skill))<span class="skill-tag">{{ trim($skill) }}</span>@endif
            @endforeach
        </div>
        @endif

        @if(!empty($resumeData['experience']))
        <div class="section">
            <div class="section-title">Professional Experience</div>
            <div class="text-block">{{ $resumeData['experience'] }}</div>
        </div>
        @endif

        @if(!empty($resumeData['projects']))
        <div class="section">
            <div class="section-title">Key Projects</div>
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

        @if(!empty($resumeData['education']))
        <div class="section">
            <div class="section-title">Education</div>
            <div class="text-block">{{ $resumeData['education'] }}</div>
        </div>
        @endif
    </div>
</div>
<div class="footer">
    Generated {{ now()->format('F j, Y') }} &middot; {{ $resume->job_title }}
</div>
</body>
</html>
