<?php

namespace App\Http\Controllers;

use App\Models\TitipanOrder;
use App\Models\TitipanRequirement;
use App\Models\ActivityLog;
use App\Services\QueueService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WebTitipanOrderController extends Controller
{
    protected $queueService;

    public function __construct(QueueService $queueService)
    {
        $this->queueService = $queueService;
    }

    public function index()
    {
        // Programmatic migration run helper:
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('titipan_orders')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            }
        } catch (\Exception $e) {
            // Ignore
        }

        return view('admin.titipan-orders.index');
    }

    public function list()
    {
        $orders = TitipanOrder::with(['takenBy', 'creator'])->orderBy('created_at', 'desc')->get();
        return response()->json(['titipan_orders' => $orders]);
    }

    public function store(Request $request)
    {
        $validRequirements = TitipanRequirement::pluck('name')->toArray();
        $request->validate([
            'booking_date' => 'required|date',
            'booking_time' => 'required|string',
            'requirement'  => ['required', Rule::in($validRequirements)],
            'description'  => 'nullable|string|max:1000',
        ]);

        $order = TitipanOrder::create([
            'booking_date' => $request->input('booking_date'),
            'booking_time' => $request->input('booking_time'),
            'requirement' => $request->input('requirement'),
            'description' => $request->input('description'),
            'status' => 'CREATE',
            'created_by_user_id' => $request->user()->id,
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'CREATE_TITIPAN',
            'description' => sprintf('%s created titipan order: %s at %s %s.', $request->user()->username, $order->requirement, $order->booking_date->toDateString(), $order->booking_time),
        ]);

        return response()->json(['titipan_order' => $order], 201);
    }

    public function show($id)
    {
        $order = TitipanOrder::findOrFail($id);
        return response()->json(['titipan_order' => $order]);
    }

    public function update(Request $request, $id)
    {
        $order = TitipanOrder::findOrFail($id);

        $validRequirements = TitipanRequirement::pluck('name')->toArray();
        $request->validate([
            'booking_date' => 'required|date',
            'booking_time' => 'required|string',
            'requirement'  => ['required', Rule::in($validRequirements)],
            'description'  => 'nullable|string|max:1000',
            'status'       => ['sometimes', 'required', Rule::in(['CREATE', 'COMPLETED'])],
        ]);

        $order->update($request->only(['booking_date', 'booking_time', 'requirement', 'description', 'status']));

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'UPDATE_TITIPAN',
            'description' => sprintf('%s updated titipan order #%s.', $request->user()->username, $order->id),
        ]);

        return response()->json(['titipan_order' => $order]);
    }

    public function destroy(Request $request, $id)
    {
        $order = TitipanOrder::findOrFail($id);

        // Validation for order status: cannot delete if status is COMPLETED
        if ($order->status === 'COMPLETED') {
            return response()->json(['message' => 'Tidak dapat menghapus order titipan yang sudah complete.'], 403);
        }

        // Validation for user role:
        // CC: only delete own orders
        if ($request->user()->role === 'CC' && $order->created_by_user_id !== $request->user()->id) {
            return response()->json(['message' => 'Anda hanya diperbolehkan menghapus order titipan buatan Anda sendiri.'], 403);
        }

        $order->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'DELETE_TITIPAN',
            'description' => sprintf('%s deleted titipan order #%s.', $request->user()->username, $id),
        ]);

        return response()->json(['message' => 'Titipan order deleted successfully.']);
    }

    public function accept(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:titipan_orders,id',
        ]);

        $titipan = TitipanOrder::where('status', 'CREATE')->findOrFail($request->input('id'));

        $order = $this->queueService->acceptTitipan($request->user(), $titipan);

        return response()->json(['order' => $order]);
    }
}
