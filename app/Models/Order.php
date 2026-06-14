<?php

namespace App\Models;

use App\Models\OrderType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'order_type_id',
        'queue_before',
        'queue_after',
        'status',
        'void_reason',
    ];

    protected $casts = [
        'queue_before' => 'array',
        'queue_after' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderType()
    {
        return $this->belongsTo(OrderType::class);
    }
}
