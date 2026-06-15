<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recycler extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'company_type', 'pic_name',
        'address', 'regency', 'phone', 'latitude', 'longitude', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'latitude'  => 'float',
            'longitude' => 'float',
        ];
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function getTotalReceivedAttribute(): float
    {
        return $this->shipments()->where('status', 'received')->sum('received_weight_kg');
    }
}
