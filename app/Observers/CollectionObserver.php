<?php

namespace App\Observers;

use App\Models\AggregatorStock;
use App\Models\Collection;
use Illuminate\Support\Facades\DB;

class CollectionObserver
{
    public function created(Collection $collection): void
    {
        $this->updateStock($collection->aggregator_id, $collection->material_type, $collection->net_weight_kg);
    }

    public function deleted(Collection $collection): void
    {
        $this->updateStock($collection->aggregator_id, $collection->material_type, -$collection->net_weight_kg);
    }

    private function updateStock(string $aggregatorId, string $materialType, float $delta): void
    {
        // Use upsert via raw query on the stock_kg column to avoid
        // DB::raw() conflicting with the float cast on the model.
        $existing = AggregatorStock::where('aggregator_id', $aggregatorId)
            ->where('material_type', $materialType)
            ->first();

        if ($existing) {
            $newStock = max(0, $existing->stock_kg + $delta);
            $existing->update([
                'stock_kg'        => $newStock,
                'last_updated_at' => now(),
            ]);
        } else {
            AggregatorStock::create([
                'aggregator_id'   => $aggregatorId,
                'material_type'   => $materialType,
                'stock_kg'        => max(0, $delta),
                'last_updated_at' => now(),
            ]);
        }
    }
}
