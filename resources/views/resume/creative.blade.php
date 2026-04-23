<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 0px; }
    body {
        margin: 0px;
        padding: 0px;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: #111827;
        font-size: 11px;
        line-height: 1.5;
    }
    table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    tr { page-break-inside: auto; }
    td { vertical-align: top; page-break-inside: auto; }
    
    .name-banner {
        padding: 40px 40px 20px 40px;
        text-align: left;
    }
    .name-text {
        font-size: 42px;
        font-weight: 800;
        letter-spacing: 6px;
        text-transform: uppercase;
        color: #000;
    }
    .title-banner {
        background-color: #dbeafe;
        text-align: center;
        padding: 12px 0;
        margin: 0 40px;
        font-size: 14px;
        font-weight: bold;
        letter-spacing: 4px;
        text-transform: uppercase;
        color: #1f2937;
    }
    
    .body-table { margin-top: 20px; }
    .left-col {
        width: 35%;
        background-color: #dbeafe;
        padding: 30px 40px;
    }
    .right-col {
        width: 65%;
        background-color: #ffffff;
        padding: 30px 40px;
    }
    
    .section-title {
        font-size: 14px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 12px;
        margin-top: 20px;
        color: #000;
    }
    
    .contact-item { margin-bottom: 8px; font-weight: 500; font-size: 10px; }
    .contact-item .icon { font-weight: bold; margin-right: 5px; }
    
    ul.skills-list {
        margin: 0; padding-left: 15px; margin-bottom: 20px;
    }
    ul.skills-list li { margin-bottom: 4px; font-weight: 500; font-size: 11px; }
    
    .summary-text {
        font-size: 11px;
        color: #374151;
        line-height: 1.6;
        margin-bottom: 25px;
    }
    .content-body {
        margin-bottom: 25px;
        color: #374151;
        line-height: 1.6;
        white-space: pre-wrap;
    }
</style>
</head>
<body>

<div class="name-banner">
    <div class="name-text">{{ $user->name }}</div>
</div>
<div class="title-banner">
    {{ $resume->job_title ?? $user->resume_job_title ?? $user->title ?? 'Professional' }}
</div>

<table class="body-table" cellspacing="0" cellpadding="0">
    <tr>
        <td class="left-col">
            <div class="section-title" style="margin-top: 0;">Contact</div>
            @if($user->phone_number)<div class="contact-item"><span class="icon">&phone;</span> {{ $user->phone_number }}</div>@endif
            @if($user->email)<div class="contact-item"><span class="icon">&#9993;</span> {{ $user->email }}</div>@endif
            @if($user->city || $user->country)<div class="contact-item"><span class="icon">&#127968;</span> {{ collect([$user->city, $user->country])->filter()->join(', ') }}</div>@endif
            @if($user->linkedin_url)<div class="contact-item"><span class="icon">&#128279;</span> {{ str_replace('https://', '', $user->linkedin_url) }}</div>@endif
            
            @if(!empty($ai_content['skills']))
            <div class="section-title">Technical Skills</div>
            <ul class="skills-list">
                @foreach(array_filter(array_map('trim', explode(',', $ai_content['skills']))) as $skill)
                <li>{{ $skill }}</li>
                @endforeach
            </ul>
            @endif

            @if($user->certificates->count() > 0)
            <div class="section-title">Achievements</div>
            @foreach($user->certificates as $cert)
            <div style="margin-bottom: 10px;">
                <div style="font-weight: 800; font-size: 10px;">&bull; {{ $cert->title }}</div>
                @if($cert->issuer)<div style="font-size: 9px; margin-top:2px; color: #4b5563;">{{ $cert->issuer }} / {{ $cert->issued_date ? $cert->issued_date->format('Y') : '' }}</div>@endif
            </div>
            @endforeach
            @endif
        </td>

        <td class="right-col">
            @if(!empty($ai_content['summary']))
            <div class="section-title" style="margin-top: 0;">Summary</div>
            <div class="summary-text">{{ $ai_content['summary'] }}</div>
            @endif

            @if(!empty($ai_content['education']))
            <div class="section-title">Education</div>
            <div class="content-body">{{ $ai_content['education'] }}</div>
            @endif

            @if(!empty($ai_content['experience']))
            <div class="section-title">Experience</div>
            <div class="content-body">{{ $ai_content['experience'] }}</div>
            @endif

            @if(!empty($ai_content['projects']))
            <div class="section-title">Projects</div>
            <div class="content-body">{{ $ai_content['projects'] }}</div>
            @endif
        </td>
    </tr>
</table>

</body>
</html>
