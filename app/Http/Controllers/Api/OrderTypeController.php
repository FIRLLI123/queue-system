<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderType;

class OrderTypeController extends Controller
{
    public function index()
    {
        $types = OrderType::active()->orderBy('name')->get();

        return response()->json(['order_types' => $types]);
    }
}
