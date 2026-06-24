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
        // Push offline users to the end of the queue on every poll
        $this->queueService->moveOfflineUsersToEnd();
        $data = $this->queueService->getDashboardData($request->user());
        return response()->json($data);
    }

    public function startBreak(Request $request)
    {
        $position = $this->queueService->startBreak($request->user());
        return response()->json(['queue_position' => $position]);
    }

    public function endBreak(Request $request)
    {
        $position = $this->queueService->endBreak($request->user());
        return response()->json(['queue_position' => $position]);
    }

    public function adminScreen()
    {
        return view('admin.screen');
    }

    public function getAdminScreenData()
    {
        // Keep queue sorted with offline users at the end
        $this->queueService->moveOfflineUsersToEnd();
        $data = $this->queueService->getScreenData();
        return response()->json($data);
    }
}
