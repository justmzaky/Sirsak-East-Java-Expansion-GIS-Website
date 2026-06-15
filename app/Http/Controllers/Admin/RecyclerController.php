<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recycler;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecyclerController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->input('year');
        $month = $request->input('month');

        $recyclers = Recycler::with('shipments')
            ->where('is_active', true)
            ->withSum(['shipments as total_received' => function ($q) use ($year, $month) {
                $q->where('status', 'received');
                if ($year)  $q->whereYear('dispatched_at', $year);
                if ($month) $q->whereMonth('dispatched_at', $month);
            }], 'received_weight_kg')
            ->withCount('shipments')
            ->get();

        $summary = Shipment::where('status', 'received')
            ->when($year,  fn($q) => $q->whereYear('dispatched_at', $year))
            ->when($month, fn($q) => $q->whereMonth('dispatched_at', $month))
            ->select('material_type', DB::raw('SUM(received_weight_kg) as total_kg'))
            ->groupBy('material_type')->get();

        $years = Shipment::selectRaw('YEAR(dispatched_at) as y')->distinct()->orderBy('y', 'desc')->pluck('y');

        return view('recycler.index', compact('recyclers', 'summary', 'years', 'year', 'month'));
    }

    public function show(Recycler $recycler, Request $request)
    {
        $year  = $request->input('year');
        $month = $request->input('month');

        $shipmentsQuery = $recycler->shipments()->with('aggregator');
        if ($year)  $shipmentsQuery->whereYear('dispatched_at', $year);
        if ($month) $shipmentsQuery->whereMonth('dispatched_at', $month);

        $shipments = (clone $shipmentsQuery)->orderByDesc('dispatched_at')->paginate(20)->withQueryString();

        $materialBreakdown = (clone $shipmentsQuery)
            ->select('material_type', DB::raw('SUM(received_weight_kg) as total_kg'))
            ->where('status', 'received')
            ->groupBy('material_type')->get();

        return view('recycler.show', compact('recycler', 'shipments', 'materialBreakdown', 'year', 'month'));
    }
}
