<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueuePosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'queue_number',
        'status',
    ];

    protected $casts = [
        'queue_number' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function scopeBreak($query)
    {
        return $query->where('status', 'BREAK');
    }
}
