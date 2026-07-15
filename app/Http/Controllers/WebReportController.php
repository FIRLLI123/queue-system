<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebReportController extends Controller
{
    /**
     * Show the report page.
     */
    public function index()
    {
        return view('admin.report');
    }

    /**
     * Return JSON data for the report with flexible filters.
     * Query params: date_from, date_to, user_id, order_type
     */
    public function getData(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->input('date_to',   now()->toDateString());
        $userId   = $request->input('user_id');    // optional
        $typeFilter = $request->input('order_type'); // optional: CRM|CMS|OTHER

        // ── Order Types ────────────────────────────────────────────────────
        $orderTypes = OrderType::pluck('name', 'id')->toArray(); // [id => name]

        // ── Per-User Per-Day breakdown ─────────────────────────────────────
        $query = Order::with(['user', 'orderType'])
            ->select(
                'user_id',
                'order_type_id',
                DB::raw('DATE(created_at) as order_date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('status', 'COMPLETED')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->groupBy('user_id', 'order_type_id', 'order_date')
            ->orderBy('order_date', 'asc');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($typeFilter) {
            $typeId = OrderType::where('name', $typeFilter)->value('id');
            if ($typeId) {
                $query->where('order_type_id', $typeId);
            }
        }

        $rows = $query->get();

        // Build user list (CC only, for filter dropdown)
        $users = User::where('role', 'CC')
            ->orderBy('name')
            ->get(['id', 'name', 'username']);

        // ── Aggregate: per-user totals ─────────────────────────────────────
        $userTotals = [];
        foreach ($rows as $row) {
            $uid  = $row->user_id;
            $type = $orderTypes[$row->order_type_id] ?? 'OTHER';
            if (!isset($userTotals[$uid])) {
                $userTotals[$uid] = [
                    'user_id'  => $uid,
                    'name'     => optional($row->user)->name ?? '–',
                    'username' => optional($row->user)->username ?? '–',
                    'CRM'      => 0,
                    'CMS'      => 0,
                    'OTHER'    => 0,
                    'TITIPAN'  => 0,
                    'TOTAL'    => 0,
                ];
            }
            $userTotals[$uid][$type]  += $row->total;
            $userTotals[$uid]['TOTAL'] += $row->total;
        }
        usort($userTotals, fn($a, $b) => $b['TOTAL'] <=> $a['TOTAL']);

        // ── Aggregate: per-date totals (for line chart) ────────────────────
        $dateTotals = [];
        foreach ($rows as $row) {
            $d    = $row->order_date;
            $type = $orderTypes[$row->order_type_id] ?? 'OTHER';
            if (!isset($dateTotals[$d])) {
                $dateTotals[$d] = ['date' => $d, 'CRM' => 0, 'CMS' => 0, 'OTHER' => 0, 'TITIPAN' => 0, 'TOTAL' => 0];
            }
            $dateTotals[$d][$type]  += $row->total;
            $dateTotals[$d]['TOTAL'] += $row->total;
        }
        ksort($dateTotals);

        // ── Per-user per-date table (for the detailed table) ───────────────
        $tableRows = [];
        foreach ($rows as $row) {
            $uid  = $row->user_id;
            $d    = $row->order_date;
            $type = $orderTypes[$row->order_type_id] ?? 'OTHER';
            $key  = "{$uid}_{$d}";
            if (!isset($tableRows[$key])) {
                $tableRows[$key] = [
                    'user_id'    => $uid,
                    'name'       => optional($row->user)->name ?? '–',
                    'username'   => optional($row->user)->username ?? '–',
                    'order_date' => $d,
                    'CRM'        => 0,
                    'CMS'        => 0,
                    'OTHER'      => 0,
                    'TITIPAN'    => 0,
                    'TOTAL'      => 0,
                ];
            }
            $tableRows[$key][$type]  += $row->total;
            $tableRows[$key]['TOTAL'] += $row->total;
        }
        usort($tableRows, fn($a, $b) => strcmp($a['order_date'], $b['order_date']) ?: strcmp($a['name'], $b['name']));

        return response()->json([
            'users'        => $users,
            'user_totals'  => array_values($userTotals),
            'date_totals'  => array_values($dateTotals),
            'table_rows'   => array_values($tableRows),
            'filter'       => [
                'date_from'  => $dateFrom,
                'date_to'    => $dateTo,
                'user_id'    => $userId,
                'order_type' => $typeFilter,
            ],
        ]);
    }

    /**
     * Export report as an Excel-compatible CSV file.
     */
    public function export(Request $request)
    {
        $dateFrom   = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo     = $request->input('date_to',   now()->toDateString());
        $userId     = $request->input('user_id');
        $typeFilter = $request->input('order_type');

        $orderTypes = OrderType::pluck('name', 'id')->toArray();

        $query = Order::with(['user', 'orderType'])
            ->select(
                'user_id',
                'order_type_id',
                DB::raw('DATE(created_at) as order_date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('status', 'COMPLETED')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->groupBy('user_id', 'order_type_id', 'order_date')
            ->orderBy('order_date', 'asc');

        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($typeFilter) {
            $typeId = OrderType::where('name', $typeFilter)->value('id');
            if ($typeId) $query->where('order_type_id', $typeId);
        }

        $rows = $query->get();

        // Build per-user-per-date structure
        $tableRows = [];
        foreach ($rows as $row) {
            $uid  = $row->user_id;
            $d    = $row->order_date;
            $type = $orderTypes[$row->order_type_id] ?? 'OTHER';
            $key  = "{$uid}_{$d}";
            if (!isset($tableRows[$key])) {
                $tableRows[$key] = [
                    'Tanggal' => $d,
                    'Nama'    => optional($row->user)->name ?? '–',
                    'Username'=> optional($row->user)->username ?? '–',
                    'CRM'     => 0,
                    'CMS'     => 0,
                    'OTHER'   => 0,
                    'TITIPAN' => 0,
                    'TOTAL'   => 0,
                ];
            }
            $tableRows[$key][$type]  += $row->total;
            $tableRows[$key]['TOTAL'] += $row->total;
        }
        usort($tableRows, fn($a, $b) => strcmp($a['Tanggal'], $b['Tanggal']) ?: strcmp($a['Nama'], $b['Nama']));

        $filename = "report_order_{$dateFrom}_to_{$dateTo}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($tableRows) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            // Header row
            fputcsv($handle, ['Tanggal', 'Nama', 'Username', 'CRM', 'CMS', 'OTHER', 'TITIPAN', 'TOTAL']);
            foreach ($tableRows as $row) {
                fputcsv($handle, [
                    $row['Tanggal'],
                    $row['Nama'],
                    $row['Username'],
                    $row['CRM'],
                    $row['CMS'],
                    $row['OTHER'],
                    $row['TITIPAN'],
                    $row['TOTAL'],
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
