<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 40px; }
    body {
        margin: 0;
        padding: 0;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: #333333;
        font-size: 11px;
        line-height: 1.5;
    }
    table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    tr { page-break-inside: auto; }
    td { vertical-align: top; page-break-inside: auto; }
    .header-table { margin-bottom: 25px; }
    .header-name {
        font-size: 34px;
        font-weight: 300;
        letter-spacing: 4px;
        text-transform: uppercase;
        color: #1f2937;
        margin-bottom: 8px;
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
        border-radius: 50%;
    }
    .horizontal-divider {
        height: 1px;
        background-color: #d1d5db;
        margin-bottom: 25px;
    }
    .left-col {
        width: 38%;
        padding-right: 25px;
    }
    .right-col {
        width: 62%;
        padding-left: 25px;
        border-left: 1px solid #d1d5db;
    }
    .section-title {
        font-size: 14px;
        font-weight: bold;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: #374151;
        margin-bottom: 12px;
        margin-top: 20px;
    }
    .contact-table { margin-bottom: 20px; }
    .contact-table td { padding-bottom: 6px; font-size: 10px; color: #4b5563; }
    .contact-table .icon { width: 20px; font-weight: bold; color: #6b7280; }
    ul { margin: 0; padding-left: 15px; margin-bottom: 20px; color: #4b5563; line-height: 1.8; font-size: 10.5px; }
    .content-body {
        margin-bottom: 20px;
        color: #4b5563;
        line-height: 1.6;
        white-space: pre-wrap;
    }
    .summary-text {
        font-size: 11px;
        color: #4b5563;
        line-height: 1.6;
        margin-bottom: 25px;
    }
</style>
</head>
<body>

<table class="header-table">
    <tr>
        <td style="vertical-align: middle;">
            <div class="header-name">{{ $user->name }}</div>
            <div class="header-title">{{ $resume->job_title ?? $user->resume_job_title ?? $user->title ?? '' }}</div>
        </td>
        <td style="width: 110px; text-align: right; vertical-align: middle;">
            @php
                $avatar = $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=f3f4f6&color=374151&size=200';
            @endphp
            <img src="{{ $avatar }}" class="avatar">
        </td>
    </tr>
</table>

<div class="horizontal-divider"></div>

<table>
    <tr>
        <td class="left-col">
            <table class="contact-table">
                @if($user->phone_number)
                <tr><td class="icon">&phone;</td><td>{{ $user->phone_number }}</td></tr>
                @endif
                @if($user->email)
                <tr><td class="icon">&#9993;</td><td>{{ $user->email }}</td></tr>
                @endif
                @if($user->city || $user->country)
                <tr><td class="icon">&#128142;</td><td>{{ collect([$user->city, $user->country])->filter()->join(', ') }}</td></tr>
                @endif
                @if($user->linkedin_url)
                <tr><td class="icon">&#128279;</td><td>{{ str_replace('https://', '', $user->linkedin_url) }}</td></tr>
                @endif
            </table>

            @if(!empty($ai_content['skills']))
            <div class="section-title">Skills</div>
            <ul>
                @foreach(array_filter(array_map('trim', explode(',', $ai_content['skills']))) as $skill)
                <li>{{ $skill }}</li>
                @endforeach
            </ul>
            @endif

            @if(!empty($ai_content['education']))
            <div class="section-title" style="margin-top: 30px;">Education</div>
            <div class="content-body" style="font-size: 10.5px;">{{ $ai_content['education'] }}</div>
            @endif
        </td>

        <td class="right-col">
            @if(!empty($ai_content['summary']))
            <div class="section-title" style="margin-top: 0;">Profile</div>
            <div class="summary-text">{{ $ai_content['summary'] }}</div>
            @endif

            @if(!empty($ai_content['experience']))
            <div class="section-title">Experience</div>
            <div class="content-body">{{ $ai_content['experience'] }}</div>
            @endif

            @if(!empty($ai_content['projects']))
            <div class="section-title">Projects</div>
            <div class="content-body">{{ $ai_content['projects'] }}</div>
            @endif

            @if(!empty($ai_content['certifications']))
            <div class="section-title">Certifications</div>
            <div class="content-body" style="font-size: 10.5px;">{{ $ai_content['certifications'] }}</div>
            @endif
        </td>
    </tr>
</table>

</body>
</html>
