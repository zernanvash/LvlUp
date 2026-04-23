<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 0px; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 11px;
        color: #1a1a2e;
        background: #fff;
        line-height: 1.5;
    }
    table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    tr { page-break-inside: auto; }
    td { vertical-align: top; page-break-inside: auto; }

    /* Header */
    .header {
        background-color: #6d28d9;
        color: white;
        padding: 28px 32px;
    }
    .header h1 { font-size: 26px; font-weight: bold; letter-spacing: 0.05em; }
    .header .job-title { font-size: 13px; opacity: 0.85; margin-top: 4px; }
    .header-contact-table { margin-top: 10px; }
    .header-contact-table td { font-size: 10px; color: rgba(255,255,255,0.8); padding-right: 16px; padding-bottom: 2px; }

    /* Body columns */
    .sidebar {
        width: 30%;
        background: #f5f3ff;
        padding: 24px 18px;
        border-right: 3px solid #ede9fe;
    }
    .main { width: 70%; padding: 24px; }

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
    .content-text {
        font-size: 10.5px;
        color: #374151;
        white-space: pre-wrap;
        line-height: 1.6;
    }
    .cert-item { margin-bottom: 8px; }
    .cert-name { font-weight: bold; font-size: 10.5px; color: #1f2937; }
    .cert-meta { font-size: 9.5px; color: #6b7280; }
    .link-item { font-size: 9.5px; color: #5b21b6; margin-bottom: 4px; }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <h1>{{ $user->name }}</h1>
    <div class="job-title">{{ $resume->job_title ?? $user->title ?? '' }}</div>
    <table class="header-contact-table" cellspacing="0" cellpadding="0">
        <tr>
            <td>{{ $user->email }}</td>
            @if($resume->phone)<td>{{ $resume->phone }}</td>@endif
            @if($resume->location)<td>{{ $resume->location }}</td>@endif
            @if($resume->linked_in ?? $user->linkedin_url)<td>LinkedIn: {{ str_replace('https://', '', $resume->linked_in ?? $user->linkedin_url) }}</td>@endif
            @if($resume->github_url ?? $user->github_url)<td>GitHub: {{ str_replace('https://', '', $resume->github_url ?? $user->github_url) }}</td>@endif
        </tr>
    </table>
</div>

{{-- Body: sidebar + main via table --}}
<table cellspacing="0" cellpadding="0">
    <tr>
        {{-- Sidebar --}}
        <td class="sidebar">

            @if(!empty($resumeData['skills']))
            <div class="section">
                <div class="section-title">Skills</div>
                @foreach(array_filter(array_map('trim', explode(',', $resumeData['skills']))) as $skill)
                <span class="skill-tag">{{ $skill }}</span>
                @endforeach
            </div>
            @endif

            @if($user->certificates()->count() > 0)
            <div class="section">
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
            <div class="section">
                <div class="section-title">Links</div>
                @if($resume->linked_in ?? $user->linkedin_url)
                <div class="link-item">LinkedIn</div>
                @endif
                @if($resume->github_url ?? $user->github_url)
                <div class="link-item">GitHub</div>
                @endif
            </div>
            @endif

            @if(!empty($resumeData['languages']))
            <div class="section">
                <div class="section-title">Languages</div>
                <div class="content-text" style="font-size:10px;">{{ $resumeData['languages'] }}</div>
            </div>
            @endif

        </td>

        {{-- Main content --}}
        <td class="main">

            @if(!empty($resumeData['summary']))
            <div class="section">
                <div class="section-title">Professional Summary</div>
                <div class="content-text">{{ $resumeData['summary'] }}</div>
            </div>
            @endif

            @if(!empty($resumeData['experience']))
            <div class="section">
                <div class="section-title">Work Experience</div>
                <div class="content-text">{{ $resumeData['experience'] }}</div>
            </div>
            @endif

            @if(!empty($resumeData['projects']))
            <div class="section">
                <div class="section-title">Projects</div>
                <div class="content-text">{{ $resumeData['projects'] }}</div>
            </div>
            @endif

            @if(!empty($resumeData['education']))
            <div class="section">
                <div class="section-title">Education</div>
                <div class="content-text">{{ $resumeData['education'] }}</div>
            </div>
            @endif

            @if(!empty($resumeData['certifications']))
            <div class="section">
                <div class="section-title">Certifications</div>
                <div class="content-text">{{ $resumeData['certifications'] }}</div>
            </div>
            @endif

            @if(!empty($resumeData['achievements']))
            <div class="section">
                <div class="section-title">Achievements</div>
                <div class="content-text">{{ $resumeData['achievements'] }}</div>
            </div>
            @endif

        </td>
    </tr>
</table>

</body>
</html>
