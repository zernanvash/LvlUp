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
        'match_score',
    ];

    protected $casts = [
        'selected_project_ids' => 'array',
        'selected_skill_ids' => 'array',
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
}
