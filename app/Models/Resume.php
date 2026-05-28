<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_title',
        'target_keywords',
        'job_description',
        'selected_project_ids',
        'selected_skill_ids',
        'pdf_path',
        'pdf_public_id',
        'pdf_template',
        'pdf_generated_at',
        'match_score',
        'template',
        'ai_content',
    ];

    protected $casts = [
        'selected_project_ids' => 'array',
        'selected_skill_ids' => 'array',
        'pdf_generated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSelectedProjects()
    {
        if (!$this->selected_project_ids) {
            return collect();
        }
        
        return Project::whereIn('id', $this->selected_project_ids)->get();
    }

    public function getSelectedSkills()
    {
        if (!$this->selected_skill_ids) {
            return collect();
        }
        
        return Skill::whereIn('id', $this->selected_skill_ids)->get();
    }

    /**
     * Generate a PDF resume using the specified template
     *
     * @param string|null $template Template name (modern, classic, minimal, creative). If null, uses stored template.
     * @return string Path to the generated PDF file
     */
    public function generatePDF(?string $template = null): string
    {
        // Use provided template or fall back to stored template or default
        $templateName = $template ?? $this->template ?? 'modern';
        
        $user = $this->user;
        $projects = $this->getSelectedProjects();
        $skills = $this->getSelectedSkills();

        // Generate PDF using dompdf
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView("resumes.templates.{$templateName}", [
            'user' => $user,
            'resume' => $this,
            'projects' => $projects,
            'skills' => $skills,
        ]);

        // Generate filename
        $filename = 'resume_' . $user->id . '_' . time() . '.pdf';
        $path = 'resumes/' . $filename;

        // Save PDF to storage
        \Storage::put($path, $pdf->output());

        // Update resume record with PDF path and template used
        $this->update([
            'pdf_path' => $path,
            'template' => $templateName,
        ]);

        return $path;
    }
}
