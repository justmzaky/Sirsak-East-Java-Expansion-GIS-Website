<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aggregator;
use App\Models\AggregatorStock;
use App\Models\Recycler;
use App\Models\WasteUnit;
use Illuminate\Http\JsonResponse;

class GisController extends Controller
{
    public function index()
    {
        return view('gis.index');
    }

    public function mapData(): JsonResponse
    {
        $bsu = WasteUnit::with(['aggregator', 'collections' => fn($q) => $q->latest('collected_at')])
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($b) {
                $mats = $b->collections->groupBy('material_type')->map->sum('net_weight_kg')->toArray();
                $history = $b->collections->groupBy('transaction_code')->map(function ($items) {
                    $first = $items->first();
                    return [
                        'code' => $first->transaction_code,
                        'date' => $first->collected_at->format('d F Y'),
                        'timestamp' => $first->collected_at->timestamp,
                        'year' => $first->collected_at->format('Y'),
                        'month' => $first->collected_at->format('F'),
                        'type' => 'Setor Sampah',
                        'total_weight' => $items->sum('net_weight_kg'),
                        'total_value' => $items->sum('total_value'),
                        'details' => $items->map(fn($i) => [
                            'material' => $i->material_type,
                            'weight' => $i->net_weight_kg,
                            'price' => $i->price_per_kg,
                            'total' => $i->total_value
                        ])->values()->toArray()
                    ];
                })->values()->toArray();

                return [
                    'type'     => 'bsu',
                    'id'       => $b->id,
                    'code'     => $b->code,
                    'name'     => $b->name,
                    'address'  => "{$b->village}, {$b->district}, {$b->regency}",
                    'lat'      => $b->latitude,
                    'lng'      => $b->longitude,
                    'aggregator' => $b->aggregator?->name,
                    'total_kg' => $b->collections()->sum('net_weight_kg'),
                    'materials'=> $mats,
                    'history'  => $history
                ];
            });

        $aggregators = Aggregator::with([
            'stocks', 
            'collections.wasteUnit', 
            'shipments.recycler'
        ])
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($a) {
                $histIn = $a->collections->sortByDesc('collected_at')->groupBy('transaction_code')->map(function ($items) {
                    $first = $items->first();
                    return [
                        'code' => $first->transaction_code,
                        'date' => $first->collected_at->format('d F Y'),
                        'timestamp' => $first->collected_at->timestamp,
                        'year' => $first->collected_at->format('Y'),
                        'month' => $first->collected_at->format('F'),
                        'type' => 'Penerimaan',
                        'from_to' => $first->wasteUnit->name ?? 'BSU',
                        'total_weight' => $items->sum('net_weight_kg'),
                        'total_value' => $items->sum('total_value'),
                        'details' => $items->map(fn($i) => [
                            'material' => $i->material_type,
                            'weight' => $i->net_weight_kg,
                            'price' => $i->price_per_kg,
                            'total' => $i->total_value
                        ])->values()->toArray()
                    ];
                });
                $histOut = $a->shipments->sortByDesc('dispatched_at')->groupBy('shipment_code')->map(function ($items) {
                    $first = $items->first();
                    return [
                        'code' => $first->shipment_code,
                        'date' => $first->dispatched_at->format('d F Y'),
                        'timestamp' => $first->dispatched_at->timestamp,
                        'year' => $first->dispatched_at->format('Y'),
                        'month' => $first->dispatched_at->format('F'),
                        'type' => 'Pengiriman',
                        'from_to' => $first->recycler->name ?? 'Recycler',
                        'total_weight' => $items->sum('shipped_weight_kg'),
                        'total_value' => 0,
                        'details' => $items->map(fn($i) => [
                            'material' => $i->material_type,
                            'weight' => $i->shipped_weight_kg,
                            'price' => 0,
                            'total' => 0
                        ])->values()->toArray()
                    ];
                });
                $history = $histIn->concat($histOut)->sortByDesc('timestamp')->values()->toArray();

                return [
                    'type'    => 'aggregator',
                    'id'      => $a->id,
                    'code'    => $a->code,
                    'name'    => $a->name,
                    'address' => "{$a->village}, {$a->district}, {$a->regency}",
                    'lat'     => $a->latitude,
                    'lng'     => $a->longitude,
                    'stocks'  => $a->stocks->map(fn ($s) => ['material' => $s->material_type, 'stock' => $s->stock_kg])->toArray(),
                    'bsu_count' => $a->wasteUnits()->count(),
                    'history' => $history
                ];
            });

        $recyclers = Recycler::with(['shipments' => fn($q) => $q->where('status', 'received')->latest('received_at')->with('aggregator')])
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($r) {
                $mats = $r->shipments->groupBy('material_type')->map->sum('received_weight_kg')->toArray();
                $history = $r->shipments->groupBy('shipment_code')->map(function ($items) {
                    $first = $items->first();
                    return [
                        'code' => $first->shipment_code,
                        'date' => $first->received_at->format('d F Y'),
                        'timestamp' => $first->received_at->timestamp,
                        'year' => $first->received_at->format('Y'),
                        'month' => $first->received_at->format('F'),
                        'type' => 'Penerimaan',
                        'from_to' => $first->aggregator->name ?? 'Agregator',
                        'total_weight' => $items->sum('received_weight_kg'),
                        'total_value' => 0,
                        'details' => $items->map(fn($i) => [
                            'material' => $i->material_type,
                            'weight' => $i->received_weight_kg,
                            'price' => 0,
                            'total' => 0
                        ])->values()->toArray()
                    ];
                })->sortByDesc('timestamp')->values()->toArray();

                return [
                    'type'       => 'recycler',
                    'id'         => $r->id,
                    'code'       => $r->code,
                    'name'       => $r->name,
                    'address'    => $r->address . ', ' . $r->regency,
                    'lat'        => $r->latitude,
                    'lng'        => $r->longitude,
                    'total_received' => $r->shipments()->sum('received_weight_kg'),
                    'materials'  => $mats,
                    'history'    => $history
                ];
            });

        return response()->json([
            'bsu'        => $bsu,
            'aggregators' => $aggregators,
            'recyclers'  => $recyclers,
        ]);
    }
}
