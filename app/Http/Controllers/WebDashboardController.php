<?php

namespace App\Http\Controllers;

use App\Services\QueueService;
use Illuminate\Http\Request;

class WebDashboardController extends Controller
{
    protected $queueService;

    public function __construct(QueueService $queueService)
    {
        $this->queueService = $queueService;
    }

    public function index()
    {
        return view('dashboard.index');
    }

    public function cc()
    {
        return view('cc.index');
    }

    public function getDashboardData(Request $request)
    {
        $data = $this->queueService->getDashboardData($request->user());
        return response()->json($data);
    }
}
