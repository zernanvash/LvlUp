<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 40px; size: A4 portrait; }
    * { box-sizing: border-box; }
    body {
        margin: 0;
        padding: 0;
        font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: #333333;
        font-size: 11px;
        line-height: 1.5;
    }

    .header { width: 100%; display: flex; align-items: center; justify-content: space-between; }
    .header-text { flex-grow: 1; }
    .header-name {
        font-size: 36px;
        font-weight: 300;
        letter-spacing: 4px;
        text-transform: uppercase;
        color: #1f2937;
        margin-bottom: 6px;
    }
    .header-title {
        font-size: 14px;
        font-weight: bold;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: #4b5563;
    }
    .avatar {
        width: 100px;
        height: 100px;
        border-radius: 50px;
        float: right;
    }
    .horizontal-divider {
        height: 1px;
        background-color: #d1d5db;
        margin: 20px 0 25px 0;
        clear: both;
    }
    
    .wrapper { width: 100%; display: block; }
    .wrapper::after { content: ""; clear: both; display: table; }
    
    .left-col { 
        float: left; 
        width: 38%; 
        padding-right: 25px; 
    }
    .right-col {
        float: right;
        width: 62%;
        padding-left: 25px;
        border-left: 1px solid #d1d5db;
        /* Minimal's vertical line cannot stretch perfectly down infinite pages with floats unless we use a pseudo element on wrapper. Let's do that! */
    }
    
    /* Chrome magic line spanning full height */
    .wrapper { position: relative; }
    .wrapper::before {
        content: "";
        position: absolute;
        top: 0; left: 38%; bottom: 0;
        width: 1px;
        background-color: #d1d5db;
        z-index: -1;
    }
    .right-col { border-left: none; } /* removed border since absolute line handles it */

    .section-title {
        font-size: 11px;
        font-weight: bold;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: #374151;
        margin-bottom: 10px;
        margin-top: 20px;
        page-break-after: avoid;
    }
    .contact-row { display: flex; align-items: flex-start; margin-bottom: 6px; font-size: 10.5px; color: #4b5563; }
    .contact-icon { width: 20px; font-weight: bold; color: #6b7280; flex-shrink: 0; }
    ul { margin: 0; padding-left: 15px; margin-bottom: 18px; color: #4b5563; line-height: 1.8; font-size: 10.5px; }
    .content-body {
        margin-bottom: 18px;
        color: #4b5563;
        line-height: 1.6;
        white-space: pre-wrap;
        font-size: 11px;
    }
    .summary-text {
        font-size: 11.5px;
        color: #4b5563;
        line-height: 1.6;
        margin-bottom: 24px;
    }
    .cert-item { margin-bottom: 8px; page-break-inside: avoid; }
    .cert-name { font-weight: bold; font-size: 10px; color: #1f2937; }
    .cert-meta { font-size: 9px; color: #6b7280; margin-top: 2px; }
    
    .avoid-break { page-break-inside: avoid; }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <div class="header-text">
        <div class="header-name">{{ $user->name }}</div>
        <div class="header-title">{{ $resume->job_title ?? $user->title ?? '' }}</div>
    </div>
    @php
        $avatar = $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=f3f4f6&color=374151&size=200';
    @endphp
    <img src="{{ $avatar }}" class="avatar">
</div>

<div class="horizontal-divider"></div>

{{-- Body --}}
<div class="wrapper">
    <div class="left-col">
        {{-- Contact --}}
        <div class="section-title" style="margin-top:0;">Contact</div>
        <div class="avoid-break" style="margin-bottom:18px;">
            @if($resume->phone)
            <div class="contact-row"><div class="contact-icon">&phone;</div><div>{{ $resume->phone }}</div></div>
            @endif
            @if($user->email)
            <div class="contact-row"><div class="contact-icon">&#9993;</div><div>{{ $user->email }}</div></div>
            @endif
            @if($resume->location)
            <div class="contact-row"><div class="contact-icon">&#128205;</div><div>{{ $resume->location }}</div></div>
            @endif
            @if($resume->linked_in ?? $user->linkedin_url)
            <div class="contact-row"><div class="contact-icon">&#128279;</div><div>{{ str_replace('https://', '', $resume->linked_in ?? $user->linkedin_url) }}</div></div>
            @endif
            @if($resume->github_url ?? $user->github_url)
            <div class="contact-row"><div class="contact-icon">&#128736;</div><div>{{ str_replace('https://', '', $resume->github_url ?? $user->github_url) }}</div></div>
            @endif
        </div>

        @if(!empty($resumeData['skills']))
        <div class="avoid-break">
            <div class="section-title">Skills</div>
            <ul>
                @foreach(array_filter(array_map('trim', explode(',', $resumeData['skills']))) as $skill)
                <li>{{ $skill }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($user->certificates()->count() > 0)
        <div class="avoid-break">
            <div class="section-title">Certifications</div>
            @foreach($user->certificates as $cert)
            <div class="cert-item">
                <div class="cert-name">{{ $cert->name }}</div>
                @if($cert->issuer)<div class="cert-meta">{{ $cert->issuer }}{{ $cert->issued_at ? ' · ' . $cert->issued_at->format('M Y') : '' }}</div>@endif
            </div>
            @endforeach
        </div>
        @endif

        @if(!empty($resumeData['education']))
        <div class="avoid-break">
            <div class="section-title" style="margin-top:20px;">Education</div>
            <div class="content-body">{{ $resumeData['education'] }}</div>
        </div>
        @endif

        @if(!empty($resumeData['languages']))
        <div class="avoid-break">
            <div class="section-title">Languages</div>
            <div class="content-body" style="font-size:10px;">{{ $resumeData['languages'] }}</div>
        </div>
        @endif
    </div>

    <div class="right-col">
        @if(!empty($resumeData['summary']))
        <div class="section-title" style="margin-top:0;">Profile</div>
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
