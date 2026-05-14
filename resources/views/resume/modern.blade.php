<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    @page {
        margin: 0px;
    }
    body {
        margin: 0px;
        padding: 0px;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: #333333;
        font-size: 11px;
        line-height: 1.4;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }
    tr {
        page-break-inside: auto;
    }
    td {
        vertical-align: top;
        page-break-inside: auto;
    }
    .left-col {
        width: 35%;
        background-color: #344054;
        color: #ffffff;
        padding: 40px 30px;
    }
    .right-col {
        width: 65%;
        background-color: #ffffff;
        padding: 40px 40px;
    }
    .avatar-container {
        text-align: center;
        margin-bottom: 30px;
    }
    .avatar {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        border: 4px solid #6366f1;
    }
    .section-title-left {
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 12px;
        margin-top: 30px;
        border-bottom: 1px solid #475467;
        padding-bottom: 5px;
    }
    .skill-pill {
        display: inline-block;
        background-color: #475467;
        color: #fff;
        padding: 4px 8px;
        border-radius: 4px;
        margin-bottom: 5px;
        margin-right: 2px;
        font-size: 10px;
    }
    .cert-title {
        font-weight: bold;
        font-size: 10px;
        color: #e0e7ff;
    }
    .cert-meta {
        font-size: 9px;
        color: #98a2b3;
    }
    .contact-item {
        margin-bottom: 8px;
        color: #d0d5dd;
        font-size: 10px;
    }
    .header-name {
        font-size: 32px;
        font-weight: 300;
        color: #101828;
        margin-bottom: 2px;
        letter-spacing: 1px;
    }
    .header-title {
        font-size: 14px;
        color: #0284c7;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 20px;
    }
    .summary-text {
        font-size: 11px;
        color: #475467;
        line-height: 1.6;
        margin-bottom: 25px;
    }
    .section-title-right {
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
        color: #101828;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 5px;
        margin-bottom: 15px;
        letter-spacing: 1px;
    }
    .content-body {
        margin-bottom: 20px;
        color: #374151;
        line-height: 1.5;
        white-space: pre-wrap;
    }
</style>
</head>
<body>

<table>
    <tr>
        <td class="left-col">
            <div class="avatar-container">
                @php
                    $avatar = $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=6366f1&color=fff&size=200';
                @endphp
                <img src="{{ $avatar }}" class="avatar">
            </div>

            <div class="section-title-left">Contact</div>
            @if($user->email)<div class="contact-item">Email: {{ $user->email }}</div>@endif
            @if($user->phone_number)<div class="contact-item">Phone: {{ $user->phone_number }}</div>@endif
            @if($user->city || $user->country)<div class="contact-item">Location: {{ collect([$user->city, $user->country])->filter()->join(', ') }}</div>@endif
            @if($user->linkedin_url)<div class="contact-item">LinkedIn: {{ str_replace('https://', '', $user->linkedin_url) }}</div>@endif
            @if($user->github_url)<div class="contact-item">GitHub: {{ str_replace('https://', '', $user->github_url) }}</div>@endif
            @if($user->website_url)<div class="contact-item">Web: {{ str_replace('https://', '', $user->website_url) }}</div>@endif

            @if(!empty($ai_content['skills']))
            <div class="section-title-left">Skills</div>
            <div style="line-height: 1.8;">
                @foreach(array_filter(array_map('trim', explode(',', $ai_content['skills']))) as $skill)
                <span class="skill-pill">{{ $skill }}</span>
                @endforeach
            </div>
            @endif

            @if(!empty($ai_content['certifications']))
            <div class="section-title-left">Certifications</div>
            <div style="white-space: pre-wrap; color: #d0d5dd; font-size: 10px;">{{ $ai_content['certifications'] }}</div>
            @endif
        </td>
        
        <td class="right-col">
            <div class="header-name">{{ $user->name }}</div>
            <div class="header-title">{{ $resume->job_title ?? $user->resume_job_title ?? $user->title ?? '' }}</div>

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
        </td>
    </tr>
</table>

</body>
</html>
