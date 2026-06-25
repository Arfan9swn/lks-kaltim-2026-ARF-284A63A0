<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'category',
        'title',
        'description',
        'location',
        'image_url',
        'status',
    ];

    /**
     * Get the user that owns the report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category label in Indonesian.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'infrastructure' => 'Infrastruktur',
            'environment' => 'Lingkungan',
            'social' => 'Sosial',
            'other' => 'Lainnya',
            default => $this->category
        };
    }

    /**
     * Get the status label in Indonesian.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open' => 'Terbuka',
            'in_progress' => 'Sedang Diproses',
            'resolved' => 'Selesai',
            default => $this->status
        };
    }
}