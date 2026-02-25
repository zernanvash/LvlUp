<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume - {{ $resume->job_title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            line-height: 1.5;
            background: #ffffff;
        }

        /* ── Layout ── */
        .page {
            padding: 36px 44px;
        }

        /* ── Header ── */
        .header {
            border-bottom: 2px solid #7c3aed;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .header h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a2e;
            letter-spacing: 0.5px;
        }

        .header .role {
            font-size: 13px;
            color: #7c3aed;
            font-weight: 600;
            margin-top: 2px;
        }

        .header .contact {
            font-size: 10px;
            color: #6b7280;
            margin-top: 4px;
        }

        /* ── Section ── */
        .section {
            margin-bottom: 16px;
        }

        .section-title {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #7c3aed;
            border-bottom: 1px solid #e9d5ff;
            padding-bottom: 3px;
            margin-bottom: 8px;
        }

        /* ── Summary ── */
        .summary p {
            color: #374151;
            font-size: 11px;
            line-height: 1.6;
        }

        /* ── Skills ── */
        .skills-grid {
            display: table;
            width: 100%;
        }

        .skill-tag {
            display: inline-block;
            background: #f3e8ff;
            color: #6d28d9;
            border: 1px solid #ddd6fe;
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 10px;
            margin: 2px 3px 2px 0;
        }

        /* ── Projects ── */
        .project {
            margin-bottom: 10px;
        }

        .project-title {
            font-weight: 700;
            font-size: 11px;
            color: #1a1a2e;
        }

        .project-body {
            color: #374151;
            font-size: 10.5px;
            margin-top: 2px;
            line-height: 1.55;
        }

        /* ── Experience / Education ── */
        .text-block {
            color: #374151;
            font-size: 10.5px;
            line-height: 1.6;
        }

        /* ── Footer ── */
        .footer {
            position: fixed;
            bottom: 20px;
            left: 44px;
            right: 44px;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
            padding-top: 6px;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <h1>{{ $user->name }}</h1>
        <div class="role">{{ $resume->job_title }}</div>
        <div class="contact">{{ $user->email }}</div>
    </div>

    {{-- Professional Summary --}}
    @if(!empty($resumeData['summary']))
    <div class="section summary">
        <div class="section-title">Professional Summary</div>
        <p>{{ $resumeData['summary'] }}</p>
    </div>
    @endif

    {{-- Skills --}}
    @if(!empty($resumeData['skills']))
    <div class="section">
        <div class="section-title">Skills</div>
        @foreach(explode(',', $resumeData['skills']) as $skill)
            @if(trim($skill))
                <span class="skill-tag">{{ trim($skill) }}</span>
            @endif
        @endforeach
    </div>
    @endif

    {{-- Projects --}}
    @if(!empty($resumeData['projects']))
    <div class="section">
        <div class="section-title">Projects</div>
        @foreach(explode("\n\n", $resumeData['projects']) as $block)
            @if(trim($block))
            <div class="project">
                @php
                    $lines = explode("\n", trim($block));
                    $title = ltrim(array_shift($lines), '*# ');
                    $body  = implode("\n", $lines);
                @endphp
                <div class="project-title">{{ $title }}</div>
                <div class="project-body">{{ $body }}</div>
            </div>
            @endif
        @endforeach
    </div>
    @endif

    {{-- Experience --}}
    @if(!empty($resumeData['experience']))
    <div class="section">
        <div class="section-title">Experience</div>
        <div class="text-block">{{ $resumeData['experience'] }}</div>
    </div>
    @endif

    {{-- Education --}}
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
