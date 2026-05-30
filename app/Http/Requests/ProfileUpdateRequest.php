<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            // Private contact
            'phone_number' => ['nullable', 'string', 'max:50'],
            'home_address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            // Skills
            'technical_skills' => ['nullable', 'string', 'max:2000'],
            // Resume details
            'resume_job_title' => ['nullable', 'string', 'max:255'],
            'resume_summary' => ['nullable', 'string', 'max:3000'],
            'work_experience' => ['nullable', 'string', 'max:5000'],
            'education' => ['nullable', 'string', 'max:3000'],
            'certifications' => ['nullable', 'string', 'max:3000'],
            'languages' => ['nullable', 'string', 'max:500'],
            // Visibility toggles
            'visibility_settings' => ['nullable', 'array'],
            'visibility_settings.*' => ['boolean'],
        ];
    }
}
