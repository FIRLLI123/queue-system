<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminOrderTypeController extends Controller
{
    public function index()
    {
        $types = OrderType::orderBy('name')->get();
        return response()->json(['order_types' => $types]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:order_types,name',
            'status' => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],
        ]);

        $orderType = OrderType::create([
            'name' => $request->input('name'),
            'status' => $request->input('status'),
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'CREATE_ORDER_TYPE',
            'description' => sprintf('Admin created order type %s.', $orderType->name),
        ]);

        return response()->json(['order_type' => $orderType], 201);
    }

    public function show($id)
    {
        $orderType = OrderType::findOrFail($id);
        return response()->json(['order_type' => $orderType]);
    }

    public function update(Request $request, $id)
    {
        $orderType = OrderType::findOrFail($id);

        $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('order_types', 'name')->ignore($orderType->id)],
            'status' => ['sometimes', 'required', Rule::in(['ACTIVE', 'INACTIVE'])],
        ]);

        $orderType->update($request->only(['name', 'status']));

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'UPDATE_ORDER_TYPE',
            'description' => sprintf('Admin updated order type %s.', $orderType->name),
        ]);

        return response()->json(['order_type' => $orderType]);
    }

    public function destroy(Request $request, $id)
    {
        $orderType = OrderType::findOrFail($id);

        try {
            $orderType->delete();

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'DELETE_ORDER_TYPE',
                'description' => sprintf('Admin deleted order type %s.', $orderType->name),
            ]);

            return response()->json(['message' => 'Order type deleted successfully.']);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'message' => 'Cannot delete this order type because it is referenced by existing orders. You can set its status to INACTIVE instead.'
            ], 400);
        }
    }
}
