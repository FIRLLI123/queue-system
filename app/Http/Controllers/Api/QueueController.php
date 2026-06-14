<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\QueueService;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    protected $queueService;

    public function __construct(QueueService $queueService)
    {
        $this->queueService = $queueService;
    }

    public function index(Request $request)
    {
        return response()->json($this->queueService->getDashboardData($request->user()));
    }
}
