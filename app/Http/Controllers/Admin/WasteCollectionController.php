<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\WasteUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WasteCollectionController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->input('year');
        $month = $request->input('month');
        $bsuId = $request->input('bsu_id');

        $query = Collection::with(['wasteUnit', 'aggregator', 'recorder'])
            ->orderByDesc('collected_at');

        if ($year)  $query->whereYear('collected_at', $year);
        if ($month) $query->whereMonth('collected_at', $month);
        if ($bsuId) $query->where('waste_unit_id', $bsuId);

        $collections = $query->paginate(20)->withQueryString();

        // Stats
        $statsQ = Collection::query();
        if ($year)  $statsQ->whereYear('collected_at', $year);
        if ($month) $statsQ->whereMonth('collected_at', $month);
        if ($bsuId) $statsQ->where('waste_unit_id', $bsuId);

        $statsByMaterial = $statsQ->clone()
            ->select('material_type', DB::raw('SUM(net_weight_kg) as total_kg'), DB::raw('COUNT(*) as total_trx'))
            ->groupBy('material_type')->get();

        $totalKg    = $statsByMaterial->sum('total_kg');
        $totalTrx   = $statsByMaterial->sum('total_trx');
        $totalValue = $statsQ->clone()->sum('total_value');

        $bsuList = WasteUnit::where('is_active', true)->orderBy('name')->get();
        $years   = Collection::selectRaw('YEAR(collected_at) as y')->distinct()->orderBy('y', 'desc')->pluck('y');

        return view('waste-collection.index', compact(
            'collections', 'statsByMaterial', 'totalKg', 'totalTrx',
            'totalValue', 'bsuList', 'years', 'year', 'month', 'bsuId'
        ));
    }
}
