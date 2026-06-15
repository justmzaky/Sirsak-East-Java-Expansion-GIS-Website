<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Aggregator;
use App\Models\Collection;
use App\Models\WasteUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenimbanganController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->input('year');
        $month = $request->input('month');
        $bsuId = $request->input('bsu_id');

        $query = Collection::with(['wasteUnit', 'aggregator', 'recorder'])
            ->orderByDesc('collected_at')->orderByDesc('created_at');

        if ($year)  $query->whereYear('collected_at', $year);
        if ($month) $query->whereMonth('collected_at', $month);
        if ($bsuId) $query->where('waste_unit_id', $bsuId);

        $collections = $query->paginate(20)->withQueryString();

        $bsuList     = WasteUnit::where('is_active', true)->orderBy('code')->get();
        $aggregators = Aggregator::where('is_active', true)->orderBy('code')->get();
        $years       = Collection::selectRaw('YEAR(collected_at) as y')->distinct()->orderBy('y', 'desc')->pluck('y');
        $nextCode    = Collection::generateTransactionCode();

        return view('superadmin.penimbangan.index', compact(
            'collections', 'bsuList', 'aggregators', 'years',
            'year', 'month', 'bsuId', 'nextCode'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'waste_unit_id'      => 'required|ulid|exists:waste_units,id',
            'aggregator_id'      => 'required|ulid|exists:aggregators,id',
            'material_type'      => 'required|in:PET,MLP,Kardus,Metal,HDPE,Campuran',
            'material_condition' => 'required|in:Bersih & Kering,Kotor / Campuran,Basah',
            'gross_weight_kg'    => 'required|numeric|min:0.01',
            'tare_weight_kg'     => 'required|numeric|min:0',
            'price_per_kg'       => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string|max:500',
            'collected_at'       => 'required|date',
        ], [
            'waste_unit_id.required' => 'BSU wajib dipilih.',
            'aggregator_id.required' => 'Agregator tujuan wajib dipilih.',
            'material_type.required' => 'Jenis material wajib dipilih.',
        ]);

        $net   = max(0, $data['gross_weight_kg'] - $data['tare_weight_kg']);
        $price = $data['price_per_kg'] ?? 0;

        DB::transaction(function () use ($data, $net, $price) {
            $collection = Collection::create(array_merge($data, [
                'transaction_code' => Collection::generateTransactionCode(),
                'recorded_by'      => auth()->id(),
                'net_weight_kg'    => $net,
                'total_value'      => $net * $price,
            ]));

            // Add stock to aggregator
            $stock = \App\Models\AggregatorStock::firstOrCreate(
                ['aggregator_id' => $data['aggregator_id'], 'material_type' => $data['material_type']],
                ['stock_kg' => 0]
            );
            $stock->stock_kg += $net;
            $stock->last_updated_at = now();
            $stock->save();

            ActivityLog::record('created', "Penimbangan {$collection->transaction_code} dicatat", $collection);
        });

        return redirect()->route('superadmin.penimbangan.index')
            ->with('success', 'Data penimbangan berhasil disimpan dan inventori agregator diperbarui.');
    }

    public function destroy(Collection $collection)
    {
        DB::transaction(function () use ($collection) {
            // Deduct stock before deleting
            $stock = \App\Models\AggregatorStock::where('aggregator_id', $collection->aggregator_id)
                ->where('material_type', $collection->material_type)
                ->first();
            
            if ($stock) {
                $stock->stock_kg = max(0, $stock->stock_kg - $collection->net_weight_kg);
                $stock->last_updated_at = now();
                $stock->save();
            }

            ActivityLog::record('deleted', "Penimbangan {$collection->transaction_code} dihapus", $collection);
            $collection->delete();
        });

        return back()->with('success', 'Data penimbangan berhasil dihapus.');
    }
}
