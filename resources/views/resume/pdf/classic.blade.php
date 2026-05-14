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
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 11px;
        color: #1a1a2e;
        background: #fff;
        line-height: 1.5;
    }
    
    /* Header */
    .header {
        background-color: #6d28d9;
        color: white;
        padding: 28px 32px;
    }
    .header h1 { font-size: 26px; font-weight: bold; letter-spacing: 0.05em; margin-bottom: 4px; }
    .header .job-title { font-size: 13px; opacity: 0.85; margin-top: 4px; }
    .header-contact-table { margin-top: 10px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
    .header-contact-table div { font-size: 10px; color: rgba(255,255,255,0.8); }

    /* Chrome full height sidebar bg */
    .sidebar-bg {
        position: fixed;
        top: 0; bottom: 0; left: 0;
        width: 30%;
        background-color: #f5f3ff;
        border-right: 3px solid #ede9fe;
        z-index: -1;
    }
    .wrapper { width: 100%; display: block; }
    .wrapper::after { content: ""; clear: both; display: table; }

    /* Body columns */
    .sidebar {
        float: left;
        width: 30%;
        padding: 24px 18px;
    }
    .main {
        float: right;
        width: 70%;
        padding: 24px 24px;
    }

    .section { margin-bottom: 20px; page-break-inside: auto; }
    .section-title {
        font-size: 10px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #7c3aed;
        border-bottom: 2px solid #ede9fe;
        padding-bottom: 4px;
        margin-bottom: 10px;
        page-break-after: avoid;
    }
    .skill-tag {
        display: inline-block;
        background: #ede9fe;
        color: #5b21b6;
        font-size: 9px;
        padding: 4px 8px;
        border-radius: 999px;
        margin-bottom: 4px; margin-right: 4px;
        font-weight: 600;
    }
    .content-text {
        font-size: 10.5px;
        color: #374151;
        white-space: pre-wrap;
        line-height: 1.6;
    }
    .cert-item { margin-bottom: 8px; page-break-inside: avoid; }
    .cert-name { font-weight: bold; font-size: 10.5px; color: #1f2937; }
    .cert-meta { font-size: 9.5px; color: #6b7280; }
    .link-item { font-size: 9.5px; color: #5b21b6; margin-bottom: 4px; }
    
    .avoid-break { page-break-inside: avoid; }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <h1>{{ $user->name }}</h1>
    <div class="job-title">{{ $resume->job_title ?? $user->title ?? '' }}</div>
    <div class="header-contact-table">
        <div>{{ $user->email }}</div>
        @if($resume->phone)<div>{{ $resume->phone }}</div>@endif
        @if($resume->location)<div>{{ $resume->location }}</div>@endif
        @if($resume->linked_in ?? $user->linkedin_url)<div>LinkedIn: {{ str_replace('https://', '', $resume->linked_in ?? $user->linkedin_url) }}</div>@endif
        @if($resume->github_url ?? $user->github_url)<div>GitHub: {{ str_replace('https://', '', $resume->github_url ?? $user->github_url) }}</div>@endif
    </div>
</div>

<div class="sidebar-bg"></div>

{{-- Body --}}
<div class="wrapper">
    {{-- Sidebar --}}
    <div class="sidebar">
        @if(!empty($ai_content['skills']))
        <div class="avoid-break" style="margin-bottom:20px;">
            <div class="section-title">Skills</div>
            @foreach(array_filter(array_map('trim', explode(',', $ai_content['skills']))) as $skill)
            <span class="skill-tag">{{ $skill }}</span>
            @endforeach
        </div>
        @endif

        @if($user->certificates()->count() > 0)
        <div class="avoid-break" style="margin-bottom:20px;">
            <div class="section-title">Certifications</div>
            @foreach($user->certificates as $cert)
            <div class="cert-item">
                <div class="cert-name">{{ $cert->name }}</div>
                @if($cert->issuer)
                <div class="cert-meta">{{ $cert->issuer }}{{ $cert->issued_at ? ' · ' . $cert->issued_at->format('M Y') : '' }}</div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        @if($resume->linked_in ?? $resume->github_url ?? $user->linkedin_url ?? $user->github_url)
        <div class="avoid-break" style="margin-bottom:20px;">
            <div class="section-title">Links</div>
            @if($resume->linked_in ?? $user->linkedin_url)
            <div class="link-item">LinkedIn</div>
            @endif
            @if($resume->github_url ?? $user->github_url)
            <div class="link-item">GitHub</div>
            @endif
        </div>
        @endif

        @if(!empty($ai_content['languages']))
        <div class="avoid-break" style="margin-bottom:20px;">
            <div class="section-title">Languages</div>
            <div class="content-text" style="font-size:10px;">{{ $ai_content['languages'] }}</div>
        </div>
        @endif
    </div>

    {{-- Main content --}}
    <div class="main">
        @if(!empty($ai_content['summary']))
        <div class="section">
            <div class="section-title">Professional Summary</div>
            <div class="content-text">{{ $ai_content['summary'] }}</div>
        </div>
        @endif

        @if(!empty($ai_content['experience']))
        <div class="section">
            <div class="section-title">Work Experience</div>
            <div class="content-text">{{ $ai_content['experience'] }}</div>
        </div>
        @endif

        @if(!empty($ai_content['projects']))
        <div class="section">
            <div class="section-title">Projects</div>
            <div class="content-text">{{ $ai_content['projects'] }}</div>
        </div>
        @endif

        @if(!empty($ai_content['education']))
        <div class="section">
            <div class="section-title">Education</div>
            <div class="content-text">{{ $ai_content['education'] }}</div>
        </div>
        @endif

        @if(!empty($ai_content['certifications']))
        <div class="section">
            <div class="section-title">Certifications</div>
            <div class="content-text">{{ $ai_content['certifications'] }}</div>
        </div>
        @endif

        @if(!empty($ai_content['achievements']))
        <div class="section">
            <div class="section-title">Achievements</div>
            <div class="content-text">{{ $ai_content['achievements'] }}</div>
        </div>
        @endif
    </div>
</div>

</body>
</html>
