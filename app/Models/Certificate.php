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
}
