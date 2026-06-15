<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WasteUnit extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'code', 'aggregator_id', 'name', 'village', 'district',
        'regency', 'phone', 'latitude', 'longitude', 'is_active', 'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'latitude'  => 'float',
            'longitude' => 'float',
            'joined_at' => 'date',
        ];
    }

    public function aggregator()
    {
        return $this->belongsTo(Aggregator::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function getTotalCollectedAttribute(): float
    {
        return $this->collections()->sum('net_weight_kg');
    }
}
