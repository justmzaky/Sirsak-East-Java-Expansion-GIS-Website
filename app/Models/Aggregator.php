<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aggregator extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'pic_name', 'village', 'district',
        'regency', 'phone', 'latitude', 'longitude', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'latitude'  => 'float',
            'longitude' => 'float',
        ];
    }

    public function wasteUnits()
    {
        return $this->hasMany(WasteUnit::class);
    }

    public function stocks()
    {
        return $this->hasMany(AggregatorStock::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function getStockByMaterial(string $material): float
    {
        return $this->stocks()->where('material_type', $material)->value('stock_kg') ?? 0;
    }

    public function getTotalStockAttribute(): float
    {
        return $this->stocks()->sum('stock_kg');
    }
}
