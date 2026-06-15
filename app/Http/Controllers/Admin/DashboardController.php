<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aggregator;
use App\Models\Collection;
use App\Models\Recycler;
use App\Models\Shipment;
use App\Models\WasteUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->input('year');
        $month = $request->input('month');

        // KPI per material
        $materialsQuery = Collection::query();
        if ($year)  $materialsQuery->whereYear('collected_at', $year);
        if ($month) $materialsQuery->whereMonth('collected_at', $month);

        $materials = $materialsQuery
            ->select('material_type', DB::raw('SUM(net_weight_kg) as total_kg'), DB::raw('SUM(total_value) as total_value'))
            ->groupBy('material_type')
            ->get()
            ->keyBy('material_type');

        // Monthly trend (last 6 months)
        $trend = Collection::select(
                DB::raw('YEAR(collected_at) as year'),
                DB::raw('MONTH(collected_at) as month'),
                'material_type',
                DB::raw('SUM(net_weight_kg) as total_kg')
            )
            ->where('collected_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('year', 'month', 'material_type')
            ->orderBy('year')->orderBy('month')
            ->get();

        // Entity counts
        $counts = [
            'bsu'       => WasteUnit::where('is_active', true)->count(),
            'agregator' => Aggregator::where('is_active', true)->count(),
            'recycler'  => Recycler::where('is_active', true)->count(),
        ];

        // Recent activities (last 5)
        $recentCollections = Collection::with(['wasteUnit', 'aggregator'])
            ->orderByDesc('created_at')->limit(5)->get();

        $recentShipments = Shipment::with(['aggregator', 'recycler'])
            ->orderByDesc('created_at')->limit(5)->get();

        // Top BSU by weight
        $topBsu = WasteUnit::withSum(['collections as total_kg' => function ($q) use ($year, $month) {
                if ($year)  $q->whereYear('collected_at', $year);
                if ($month) $q->whereMonth('collected_at', $month);
            }], 'net_weight_kg')
            ->where('is_active', true)
            ->orderByDesc('total_kg')
            ->limit(5)
            ->get();

        // Top Aggregator
        $topAgg = Aggregator::withSum(['collections as total_kg' => function ($q) use ($year, $month) {
                if ($year)  $q->whereYear('collected_at', $year);
                if ($month) $q->whereMonth('collected_at', $month);
            }], 'net_weight_kg')
            ->where('is_active', true)
            ->orderByDesc('total_kg')
            ->limit(5)
            ->get();

        $years = Collection::selectRaw('YEAR(collected_at) as y')->distinct()->orderBy('y', 'desc')->pluck('y');

        return view('dashboard.index', compact(
            'materials', 'trend', 'counts',
            'recentCollections', 'recentShipments',
            'topBsu', 'topAgg', 'years', 'year', 'month'
        ));
    }
}
