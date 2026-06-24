<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'message',
        'type',
        'is_read',
        'reference_id',
        'reference_type',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the notification's type label in Indonesian.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'service_request' => 'Permintaan Layanan',
            'report' => 'Laporan Warga',
            'system' => 'Sistem',
            default => $this->type
        };
    }

    /**
     * Get the notification's status label in Indonesian.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_read ? 'Sudah Dibaca' : 'Belum Dibaca';
    }
}