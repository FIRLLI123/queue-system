<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderType;
use App\Services\QueueService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $queueService;

    public function __construct(QueueService $queueService)
    {
        $this->queueService = $queueService;
    }

    public function accept(Request $request)
    {
        $request->validate([
            'order_type_id' => 'required|exists:order_types,id',
        ]);

        $orderType = OrderType::active()->findOrFail($request->input('order_type_id'));

        $order = $this->queueService->acceptOrder($request->user(), $orderType);

        return response()->json(['order' => $order]);
    }

    public function void(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $order = $this->queueService->voidLastOrder($request->user(), $request->input('reason'));

        return response()->json(['order' => $order]);
    }
}
