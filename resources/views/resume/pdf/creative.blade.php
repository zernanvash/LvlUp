<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 0px; size: A4 portrait; }
    * { box-sizing: border-box; }
    body {
        margin: 0px;
        padding: 0px;
        font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: #111827;
        font-size: 11px;
        line-height: 1.5;
    }
    
    .name-banner {
        padding: 36px 40px 16px 40px;
        text-align: left;
        background: #ffffff;
    }
    .name-text {
        font-size: 36px;
        font-weight: 800;
        letter-spacing: 6px;
        text-transform: uppercase;
        color: #000;
    }
    .title-banner {
        background-color: #dbeafe;
        padding: 12px 40px;
        font-size: 13px;
        font-weight: bold;
        letter-spacing: 4px;
        text-transform: uppercase;
        color: #1f2937;
        text-align: center;
    }

    .sidebar-bg {
        position: fixed;
        top: 0; bottom: 0; left: 0;
        width: 35%;
        background-color: #dbeafe;
        z-index: -1;
    }
    .wrapper { width: 100%; display: block; }
    .wrapper::after { content: ""; clear: both; display: table; }

    .left-col {
        float: left;
        width: 35%;
        padding: 28px 30px;
    }
    .right-col {
        float: right;
        width: 65%;
        padding: 28px 36px;
    }

    .section-title {
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
        margin-top: 18px;
        color: #000;
        page-break-after: avoid;
    }

    .contact-item { margin-bottom: 7px; font-weight: 500; font-size: 10px; color: #1f2937; }

    ul.skills-list { margin: 0; padding-left: 15px; margin-bottom: 18px; }
    ul.skills-list li { margin-bottom: 4px; font-weight: 500; font-size: 10.5px; }

    .summary-text {
        font-size: 11px;
        color: #374151;
        line-height: 1.6;
        margin-bottom: 22px;
    }
    .content-body {
        margin-bottom: 22px;
        color: #374151;
        line-height: 1.6;
        white-space: pre-wrap;
        font-size: 10.5px;
    }
    .cert-item { margin-bottom: 10px; page-break-inside: avoid; }
    .cert-name { font-weight: 800; font-size: 10px; color: #1f2937; }
    .cert-meta { font-size: 9px; margin-top: 2px; color: #4b5563; }
    
    .avoid-break { page-break-inside: avoid; }
</style>
</head>
<body>

{{-- Name banner --}}
<div class="name-banner">
    <div class="name-text">{{ $user->name }}</div>
</div>
<div class="title-banner">
    {{ $resume->job_title ?? $user->title ?? 'Professional' }}
</div>

<div class="sidebar-bg"></div>

{{-- Body --}}
<div class="wrapper">
    {{-- Left column --}}
    <div class="left-col">
        <div class="section-title" style="margin-top:0;">Contact</div>
        <div class="avoid-break" style="margin-bottom:18px;">
            @if($resume->phone)
            <div class="contact-item">&#9990; {{ $resume->phone }}</div>
            @endif
            @if($user->email)
            <div class="contact-item">&#9993; {{ $user->email }}</div>
            @endif
            @if($resume->location)
            <div class="contact-item">&#128205; {{ $resume->location }}</div>
            @endif
            @if($resume->linked_in ?? $user->linkedin_url)
            <div class="contact-item">&#128279; {{ str_replace('https://', '', $resume->linked_in ?? $user->linkedin_url) }}</div>
            @endif
            @if($resume->github_url ?? $user->github_url)
            <div class="contact-item">&#128736; {{ str_replace('https://', '', $resume->github_url ?? $user->github_url) }}</div>
            @endif
        </div>

        @if(!empty($resumeData['skills']))
        <div class="avoid-break">
            <div class="section-title">Technical Skills</div>
            <ul class="skills-list">
                @foreach(array_filter(array_map('trim', explode(',', $resumeData['skills']))) as $skill)
                <li>{{ $skill }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(!empty($resumeData['languages']))
        <div class="avoid-break">
            <div class="section-title">Languages</div>
            <div style="font-size:10px;color:#1f2937;line-height:1.7;margin-bottom:18px;">{{ $resumeData['languages'] }}</div>
        </div>
        @endif

        @if($user->certificates()->count() > 0)
        <div class="avoid-break">
            <div class="section-title">Certificates</div>
            @foreach($user->certificates as $cert)
            <div class="cert-item">
                <div class="cert-name">&bull; {{ $cert->name }}</div>
                @if($cert->issuer)<div class="cert-meta">{{ $cert->issuer }}{{ $cert->issued_at ? ' / ' . $cert->issued_at->format('Y') : '' }}</div>@endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Right column --}}
    <div class="right-col">
        @if(!empty($resumeData['summary']))
        <div class="section-title" style="margin-top:0;">Summary</div>
        <div class="summary-text">{{ $resumeData['summary'] }}</div>
        @endif

        @if(!empty($resumeData['experience']))
        <div class="section-title">Experience</div>
        <div class="content-body">{{ $resumeData['experience'] }}</div>
        @endif

        @if(!empty($resumeData['projects']))
        <div class="section-title">Projects</div>
        <div class="content-body">{{ $resumeData['projects'] }}</div>
        @endif

        @if(!empty($resumeData['education']))
        <div class="section-title">Education</div>
        <div class="content-body">{{ $resumeData['education'] }}</div>
        @endif

        @if(!empty($resumeData['certifications']))
        <div class="section-title">Certifications</div>
        <div class="content-body">{{ $resumeData['certifications'] }}</div>
        @endif

        @if(!empty($resumeData['achievements']))
        <div class="section-title">Achievements</div>
        <div class="content-body">{{ $resumeData['achievements'] }}</div>
        @endif
    </div>
</div>

</body>
</html>
