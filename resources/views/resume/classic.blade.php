<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 11px;
        color: #1a1a2e;
        background: #fff;
        line-height: 1.5;
    }
    .header {
        background: linear-gradient(135deg, #6d28d9, #db2777);
        color: white;
        padding: 28px 32px;
    }
    .header h1 {
        font-size: 26px;
        font-weight: bold;
        letter-spacing: 0.05em;
    }
    .header .title { font-size: 13px; opacity: 0.85; margin-top: 4px; }
    .header .contact { font-size: 10px; opacity: 0.8; margin-top: 8px; display: flex; gap: 16px; flex-wrap: wrap; }
    .body { display: flex; }
    .sidebar {
        width: 30%;
        background: #f5f3ff;
        padding: 24px 18px;
        border-right: 3px solid #ede9fe;
        min-height: 100%;
    }
    .main { width: 70%; padding: 24px 24px; }
    .section { margin-bottom: 20px; }
    .section-title {
        font-size: 10px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #7c3aed;
        border-bottom: 2px solid #ede9fe;
        padding-bottom: 4px;
        margin-bottom: 10px;
    }
    .skill-tag {
        display: inline-block;
        background: #ede9fe;
        color: #5b21b6;
        font-size: 9px;
        padding: 2px 8px;
        border-radius: 999px;
        margin: 2px;
        font-weight: 600;
    }
    .content-block { margin-bottom: 10px; }
    .content-text {
        font-size: 10.5px;
        color: #374151;
        white-space: pre-wrap;
        line-height: 1.6;
    }
    .cert-item { margin-bottom: 6px; }
    .cert-title { font-weight: bold; font-size: 10.5px; color: #1f2937; }
    .cert-meta { font-size: 9.5px; color: #6b7280; }
    .cert-summary { font-size: 9.5px; color: #374151; font-style: italic; }
    .divider { height: 1px; background: #e5e7eb; margin: 10px 0; }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <h1>{{ $user->name }}</h1>
    <div class="title">{{ $resume->job_title ?? $user->resume_job_title ?? $user->title ?? '' }}</div>
    <div class="contact">
        <span>{{ $user->email }}</span>
        @if($user->phone_number)<span>{{ $user->phone_number }}</span>@endif
        @if($user->city || $user->country)<span>{{ collect([$user->city, $user->country])->filter()->join(', ') }}</span>@endif
        @if($user->linkedin_url)<span>LinkedIn: {{ $user->linkedin_url }}</span>@endif
        @if($user->github_url)<span>GitHub: {{ $user->github_url }}</span>@endif
        @if($user->website_url)<span>{{ $user->website_url }}</span>@endif
    </div>
</div>

<div class="body">

    {{-- Sidebar --}}
    <div class="sidebar">

        {{-- Skills --}}
        @if(!empty($ai_content['skills']))
        <div class="section">
            <div class="section-title">Skills</div>
            @foreach(array_filter(array_map('trim', explode(',', $ai_content['skills']))) as $skill)
            <span class="skill-tag">{{ $skill }}</span>
            @endforeach
        </div>
        @endif

        {{-- Certifications --}}
        @if($user->certificates->count() > 0)
        <div class="section">
            <div class="section-title">Certifications</div>
            @foreach($user->certificates as $cert)
            <div class="cert-item">
                <div class="cert-title">{{ $cert->title }}</div>
                @if($cert->issuer)
                <div class="cert-meta">{{ $cert->issuer }}{{ $cert->issued_date ? ' · ' . $cert->issued_date->format('M Y') : '' }}</div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        {{-- Links --}}
        @if($user->linkedin_url || $user->github_url || $user->website_url)
        <div class="section">
            <div class="section-title">Links</div>
            @if($user->linkedin_url)<p style="font-size:9.5px;color:#5b21b6;margin-bottom:3px;">LinkedIn</p>@endif
            @if($user->github_url)<p style="font-size:9.5px;color:#5b21b6;margin-bottom:3px;">GitHub</p>@endif
            @if($user->website_url)<p style="font-size:9.5px;color:#5b21b6;margin-bottom:3px;">Portfolio</p>@endif
        </div>
        @endif

    </div>{{-- end sidebar --}}

    {{-- Main content --}}
    <div class="main">

        {{-- Summary --}}
        @if(!empty($ai_content['summary']))
        <div class="section">
            <div class="section-title">Professional Summary</div>
            <div class="content-text">{{ $ai_content['summary'] }}</div>
        </div>
        @endif

        {{-- Experience --}}
        @if(!empty($ai_content['experience']))
        <div class="section">
            <div class="section-title">Work Experience</div>
            <div class="content-text">{{ $ai_content['experience'] }}</div>
        </div>
        @endif

        {{-- Projects --}}
        @if(!empty($ai_content['projects']))
        <div class="section">
            <div class="section-title">Projects</div>
            <div class="content-text">{{ $ai_content['projects'] }}</div>
        </div>
        @endif

        {{-- Education --}}
        @if(!empty($ai_content['education']))
        <div class="section">
            <div class="section-title">Education</div>
            <div class="content-text">{{ $ai_content['education'] }}</div>
        </div>
        @endif

    </div>{{-- end main --}}
</div>

</body>
</html>
