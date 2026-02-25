<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resume – {{ $user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11.5px;
            color: #000;
            background: #fff;
            line-height: 1.6;
        }
        .page { padding: 40px 60px; }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 14px;
            margin-bottom: 22px;
        }
        .header h1 { font-size: 26px; text-transform: uppercase; letter-spacing: 3px; }
        .header .role { font-size: 13px; font-style: italic; margin-top: 4px; color: #333; }
        .header .contact { font-size: 10.5px; color: #555; margin-top: 4px; }

        /* Sections */
        .section { margin-bottom: 22px; }
        .section-title {
            font-size: 12px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 2px;
            border-bottom: 2px solid #000;
            padding-bottom: 4px; margin-bottom: 10px;
        }

        /* Skills */
        .skill-item { display: inline; font-size: 11px; }
        .skill-item + .skill-item::before { content: " • "; margin: 0 4px; }

        /* Projects */
        .project { margin-bottom: 13px; padding-left: 18px; }
        .project-title { font-weight: bold; font-size: 12px; margin-bottom: 3px; }
        .project-body { color: #333; font-size: 11px; line-height: 1.6; text-align: justify; white-space: pre-wrap; }

        /* Text blocks */
        .text-block { color: #333; font-size: 11px; line-height: 1.7; padding-left: 18px; white-space: pre-wrap; }

        /* Footer */
        .footer {
            position: fixed; bottom: 20px; left: 60px; right: 60px;
            text-align: center; font-size: 8px; color: #999;
            border-top: 1px solid #ccc; padding-top: 5px;
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
        <div class="text-block" style="font-style:italic;">{{ $resumeData['summary'] }}</div>
    </div>
    @endif

    @if(!empty($resumeData['skills']))
    <div class="section">
        <div class="section-title">Technical Skills</div>
        <div style="padding-left:18px;">
            @foreach(explode(',', $resumeData['skills']) as $skill)
                @if(trim($skill))<span class="skill-item">{{ trim($skill) }}</span>@endif
            @endforeach
        </div>
    </div>
    @endif

    @if(!empty($resumeData['projects']))
    <div class="section">
        <div class="section-title">Projects &amp; Portfolio</div>
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
