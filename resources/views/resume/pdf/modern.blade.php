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
        color: #333333;
        font-size: 11px;
        line-height: 1.4;
    }
    .sidebar-bg {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        width: 35%;
        background-color: #344054;
        z-index: -1;
    }
    .wrapper {
        width: 100%;
        display: block;
    }
    .wrapper::after {
        content: "";
        clear: both;
        display: table;
    }
    .left-col {
        float: left;
        width: 35%;
        padding: 40px 30px;
        color: #ffffff;
    }
    .right-col {
        float: right;
        width: 65%;
        padding: 40px 40px;
    }
    .avatar-container { text-align: center; margin-bottom: 30px; }
    .avatar { width: 120px; height: 120px; border-radius: 60px; border: 4px solid #6366f1; }
    
    .section-title-left {
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 10px;
        margin-top: 25px;
        border-bottom: 1px solid #475467;
        padding-bottom: 5px;
        color: #ffffff;
        page-break-after: avoid;
    }
    .skill-pill {
        display: inline-block;
        background-color: #475467;
        color: #fff;
        padding: 4px 8px;
        border-radius: 4px;
        margin-bottom: 5px;
        margin-right: 4px;
        font-size: 9px;
    }
    .contact-item { margin-bottom: 7px; color: #d0d5dd; font-size: 10.5px; }
    
    .header-name {
        font-size: 34px;
        font-weight: 300;
        color: #101828;
        margin-bottom: 4px;
        letter-spacing: 1px;
    }
    .header-title {
        font-size: 14px;
        color: #0284c7;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 22px;
    }
    .summary-text {
        font-size: 11.5px;
        color: #475467;
        line-height: 1.6;
        margin-bottom: 24px;
    }
    .section-title-right {
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
        color: #101828;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 6px;
        margin-bottom: 14px;
        margin-top: 24px;
        letter-spacing: 1px;
        page-break-after: avoid;
    }
    .content-body {
        margin-bottom: 20px;
        color: #374151;
        line-height: 1.6;
        white-space: pre-wrap;
        font-size: 11px;
    }
    .cert-item { margin-bottom: 8px; page-break-inside: avoid; }
    .cert-name { font-weight: bold; font-size: 10px; color: #e0e7ff; }
    .cert-meta { font-size: 9px; color: #98a2b3; margin-top: 2px; }
    
    .avoid-break { page-break-inside: avoid; }
</style>
</head>
<body>

<div class="sidebar-bg"></div>

<div class="wrapper">
    {{-- Left sidebar --}}
    <div class="left-col">
        <div class="avatar-container">
            @php
                $avatar = $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=6366f1&color=fff&size=200';
            @endphp
            <img src="{{ $avatar }}" class="avatar">
        </div>

        <div class="section-title-left" style="margin-top:0;">Contact</div>
        <div class="avoid-break">
            @if($user->email)<div class="contact-item">Email: {{ $user->email }}</div>@endif
            @if($resume->phone)<div class="contact-item">Phone: {{ $resume->phone }}</div>@endif
            @if($resume->location)<div class="contact-item">Location: {{ $resume->location }}</div>@endif
            @if($resume->linked_in ?? $user->linkedin_url)<div class="contact-item">LinkedIn: {{ str_replace('https://', '', $resume->linked_in ?? $user->linkedin_url) }}</div>@endif
            @if($resume->github_url ?? $user->github_url)<div class="contact-item">GitHub: {{ str_replace('https://', '', $resume->github_url ?? $user->github_url) }}</div>@endif
        </div>

        @if(!empty($ai_content['skills']))
        <div class="avoid-break">
            <div class="section-title-left">Skills</div>
            <div style="line-height: 1.9;">
                @foreach(array_filter(array_map('trim', explode(',', $ai_content['skills']))) as $skill)
                <span class="skill-pill">{{ $skill }}</span>
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($ai_content['certifications']))
        <div class="avoid-break">
            <div class="section-title-left">Certifications</div>
            <div style="white-space: pre-wrap; color: #d0d5dd; font-size: 10.5px; line-height: 1.6;">{{ $ai_content['certifications'] }}</div>
        </div>
        @endif

        @if($user->certificates()->count() > 0)
        <div class="section-title-left">My Certificates</div>
        @foreach($user->certificates as $cert)
        <div class="cert-item">
            <div class="cert-name">{{ $cert->name }}</div>
            @if($cert->issuer)<div class="cert-meta">{{ $cert->issuer }}{{ $cert->issued_at ? ' · ' . $cert->issued_at->format('M Y') : '' }}</div>@endif
        </div>
        @endforeach
        @endif
    </div>

    {{-- Right main --}}
    <div class="right-col">
        <div class="header-name">{{ $user->name }}</div>
        <div class="header-title">{{ $resume->job_title ?? $user->title ?? '' }}</div>

        @if(!empty($ai_content['summary']))
        <div class="summary-text">{{ $ai_content['summary'] }}</div>
        @endif

        @if(!empty($ai_content['experience']))
        <div class="section-title-right">Work Experience</div>
        <div class="content-body">{{ $ai_content['experience'] }}</div>
        @endif

        @if(!empty($ai_content['projects']))
        <div class="section-title-right">Projects</div>
        <div class="content-body">{{ $ai_content['projects'] }}</div>
        @endif

        @if(!empty($ai_content['education']))
        <div class="section-title-right">Education</div>
        <div class="content-body">{{ $ai_content['education'] }}</div>
        @endif

        @if(!empty($ai_content['achievements']))
        <div class="section-title-right">Achievements</div>
        <div class="content-body">{{ $ai_content['achievements'] }}</div>
        @endif
    </div>
</div>

</body>
</html>
