<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aggregator;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgregatorController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->input('year');
        $month = $request->input('month');

        $aggregators = Aggregator::with(['stocks', 'wasteUnits'])
            ->where('is_active', true)
            ->withSum(['collections as total_kg' => function ($q) use ($year, $month) {
                if ($year)  $q->whereYear('collected_at', $year);
                if ($month) $q->whereMonth('collected_at', $month);
            }], 'net_weight_kg')
            ->withCount('wasteUnits')
            ->get();

        $materialSummary = Collection::query()
            ->when($year,  fn($q) => $q->whereYear('collected_at', $year))
            ->when($month, fn($q) => $q->whereMonth('collected_at', $month))
            ->select('material_type', DB::raw('SUM(net_weight_kg) as total_kg'))
            ->groupBy('material_type')
            ->get();

        $years = Collection::selectRaw('YEAR(collected_at) as y')->distinct()->orderBy('y', 'desc')->pluck('y');

        return view('agregator.index', compact('aggregators', 'materialSummary', 'years', 'year', 'month'));
    }

    public function show(Aggregator $aggregator, Request $request)
    {
        $year  = $request->input('year');
        $month = $request->input('month');

        $aggregator->load(['stocks', 'wasteUnits']);

        $collectionsQuery = $aggregator->collections()->with('wasteUnit');
        if ($year)  $collectionsQuery->whereYear('collected_at', $year);
        if ($month) $collectionsQuery->whereMonth('collected_at', $month);

        $collections = (clone $collectionsQuery)->orderByDesc('collected_at')->paginate(15)->withQueryString();

        $shipmentsQuery = $aggregator->shipments()->with('recycler');
        $shipments = (clone $shipmentsQuery)->orderByDesc('dispatched_at')->paginate(10, ['*'], 'spage')->withQueryString();

        $materialBreakdown = (clone $collectionsQuery)
            ->select('material_type', DB::raw('SUM(net_weight_kg) as total_kg'))
            ->groupBy('material_type')->get();

        return view('agregator.show', compact('aggregator', 'collections', 'shipments', 'materialBreakdown', 'year', 'month'));
    }
}
