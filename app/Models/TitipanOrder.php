<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TitipanOrder extends Model
{
    use HasFactory;

    protected $table = 'titipan_orders';

    protected $fillable = [
        'booking_date',
        'booking_time',
        'requirement',
        'description',
        'status', // 'CREATE' or 'COMPLETED'
        'taken_by_user_id',
        'taken_at',
        'created_by_user_id',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'taken_at' => 'datetime',
    ];

    public function takenBy()
    {
        return $this->belongsTo(User::class, 'taken_by_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}

