<?php

namespace App\Observers;

use App\Models\AggregatorStock;
use App\Models\Shipment;

class ShipmentObserver
{
    public function created(Shipment $shipment): void
    {
        // Deduct stock when shipment is created — read current value first,
        // then write a plain float to avoid DB::raw() conflicting with float cast.
        $stock = AggregatorStock::where('aggregator_id', $shipment->aggregator_id)
            ->where('material_type', $shipment->material_type)
            ->first();

        if ($stock) {
            $stock->update([
                'stock_kg'        => max(0, $stock->stock_kg - $shipment->shipped_weight_kg),
                'last_updated_at' => now(),
            ]);
        }
    }

    public function updated(Shipment $shipment): void
    {
        // If cancelled, restore stock
        if ($shipment->isDirty('status') && $shipment->status === 'cancelled') {
            $stock = AggregatorStock::where('aggregator_id', $shipment->aggregator_id)
                ->where('material_type', $shipment->material_type)
                ->first();

            if ($stock) {
                $stock->update([
                    'stock_kg'        => $stock->stock_kg + $shipment->shipped_weight_kg,
                    'last_updated_at' => now(),
                ]);
            } else {
                AggregatorStock::create([
                    'aggregator_id'   => $shipment->aggregator_id,
                    'material_type'   => $shipment->material_type,
                    'stock_kg'        => $shipment->shipped_weight_kg,
                    'last_updated_at' => now(),
                ]);
            }
        }
    }
}
