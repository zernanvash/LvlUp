<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'issuer',
        'issued_date',
        'file_path',
        'file_public_id',
        'file_type',
        'ai_summary',
    ];

    protected $casts = [
        'issued_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determine if the file is a PDF.
     */
    public function isPdf(): bool
    {
        return $this->file_type === 'pdf';
    }

    /**
     * Get the PDF version of the file via Cloudinary format transformation.
     */
    public function getPdfUrlAttribute()
    {
        if ($this->isPdf()) return $this->file_path;
        return preg_replace('/\.[^.]+$/', '.pdf', $this->file_path);
    }

    /**
     * Get the PNG version of the file.
     */
    public function getPngUrlAttribute()
    {
        if ($this->isPdf()) return $this->file_path;
        return preg_replace('/\.[^.]+$/', '.png', $this->file_path);
    }
}
