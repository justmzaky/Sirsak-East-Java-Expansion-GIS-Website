<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Aggregator;
use App\Models\AggregatorStock;
use App\Models\Recycler;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengirimanController extends Controller
{
    public function index(Request $request)
    {
        $year        = $request->input('year');
        $month       = $request->input('month');
        $aggregatorId = $request->input('aggregator_id');
        $status      = $request->input('status');

        $query = Shipment::with(['aggregator', 'recycler', 'dispatcher'])
            ->orderByDesc('dispatched_at');

        if ($year)         $query->whereYear('dispatched_at', $year);
        if ($month)        $query->whereMonth('dispatched_at', $month);
        if ($aggregatorId) $query->where('aggregator_id', $aggregatorId);
        if ($status)       $query->where('status', $status);

        $shipments   = $query->paginate(20)->withQueryString();
        $aggregators = Aggregator::where('is_active', true)->orderBy('code')->get();
        $recyclers   = Recycler::where('is_active', true)->orderBy('code')->get();
        $years       = Shipment::selectRaw('YEAR(dispatched_at) as y')->distinct()->orderBy('y', 'desc')->pluck('y');
        $nextCode    = Shipment::generateShipmentCode();

        return view('superadmin.pengiriman.index', compact(
            'shipments', 'aggregators', 'recyclers', 'years',
            'year', 'month', 'aggregatorId', 'status', 'nextCode'
        ));
    }

    public function getAggregatorStock(Aggregator $aggregator)
    {
        $stocks = AggregatorStock::where('aggregator_id', $aggregator->id)
            ->where('stock_kg', '>', 0)
            ->get(['material_type', 'stock_kg']);

        return response()->json($stocks);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'aggregator_id'    => 'required|ulid|exists:aggregators,id',
            'recycler_id'      => 'required|ulid|exists:recyclers,id',
            'material_type'    => 'required|in:PET,MLP,Kardus,Metal,HDPE,Campuran',
            'shipped_weight_kg' => 'required|numeric|min:0.01',
            'vehicle_info'     => 'nullable|string|max:100',
            'notes'            => 'nullable|string|max:500',
        ]);

        // Validate stock is sufficient
        $stock = AggregatorStock::where('aggregator_id', $data['aggregator_id'])
            ->where('material_type', $data['material_type'])
            ->value('stock_kg') ?? 0;

        if ($data['shipped_weight_kg'] > $stock) {
            return back()->withErrors(['shipped_weight_kg' => "Stok {$data['material_type']} tidak mencukupi. Stok tersedia: {$stock} kg."])->withInput();
        }

        DB::transaction(function () use ($data) {
            $shipment = Shipment::create(array_merge($data, [
                'shipment_code' => Shipment::generateShipmentCode(),
                'dispatched_by' => auth()->id(),
                'dispatched_at' => now(),
                'status'        => 'dispatched',
            ]));

            // Deduct stock
            $stock = AggregatorStock::where('aggregator_id', $data['aggregator_id'])
                ->where('material_type', $data['material_type'])
                ->first();
            
            if ($stock) {
                $stock->stock_kg = max(0, $stock->stock_kg - $data['shipped_weight_kg']);
                $stock->last_updated_at = now();
                $stock->save();
            }

            ActivityLog::record('created', "Pengiriman {$shipment->shipment_code} dibuat", $shipment);
        });

        return redirect()->route('superadmin.pengiriman.index')
            ->with('success', 'Pengiriman berhasil dicatat. Stok agregator telah dikurangi.');
    }

    public function updateStatus(Request $request, Shipment $shipment)
    {
        $data = $request->validate([
            'status'              => 'required|in:in_transit,received,cancelled',
            'received_weight_kg'  => 'nullable|numeric|min:0',
        ]);

        if ($data['status'] === 'received' && empty($data['received_weight_kg'])) {
            return back()->withErrors(['received_weight_kg' => 'Berat diterima wajib diisi jika status diterima.'])->withInput();
        }

        DB::transaction(function () use ($data, $shipment) {
            if ($data['status'] === 'received') {
                $data['received_at']  = now();
                $data['confirmed_by'] = auth()->id();
            }

            $old = $shipment->status;
            
            if ($data['status'] === 'cancelled' && $old !== 'cancelled') {
                // Restore stock
                $stock = AggregatorStock::where('aggregator_id', $shipment->aggregator_id)
                    ->where('material_type', $shipment->material_type)
                    ->first();
                if ($stock) {
                    $stock->stock_kg += $shipment->shipped_weight_kg;
                    $stock->last_updated_at = now();
                    $stock->save();
                }
            } elseif ($old === 'cancelled' && $data['status'] !== 'cancelled') {
                // Deduct stock again if un-cancelling
                $stock = AggregatorStock::where('aggregator_id', $shipment->aggregator_id)
                    ->where('material_type', $shipment->material_type)
                    ->first();
                if ($stock) {
                    $stock->stock_kg = max(0, $stock->stock_kg - $shipment->shipped_weight_kg);
                    $stock->last_updated_at = now();
                    $stock->save();
                }
            }

            $shipment->update($data);

            ActivityLog::record('updated', "Status pengiriman {$shipment->shipment_code} diubah dari {$old} ke {$data['status']}", $shipment);
        });

        return back()->with('success', "Status pengiriman berhasil diperbarui.");
    }
}
