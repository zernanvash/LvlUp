{{--
    pdf.blade.php — entry point for PDF rendering.
    Delegates to the correct template partial based on $resume->template.
    Available templates: modern, classic, minimal, creative, executive, tech
--}}
@php
    $template = in_array($resume->template, ['modern','classic','minimal','creative','executive','tech'])
        ? $resume->template
        : 'modern';
@endphp

@include('resume.templates.' . $template, [
    'resume'     => $resume,
    'resumeData' => $resumeData,
    'user'       => $user,
    'projects'   => $projects,
    'skills'     => $skills,
])
