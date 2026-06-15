<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasUlids;

    protected $fillable = [
        'shipment_code', 'aggregator_id', 'recycler_id',
        'dispatched_by', 'confirmed_by', 'material_type',
        'shipped_weight_kg', 'received_weight_kg',
        'status', 'vehicle_info', 'notes', 'dispatched_at', 'received_at',
    ];

    protected function casts(): array
    {
        return [
            'shipped_weight_kg'   => 'float',
            'received_weight_kg'  => 'float',
            'dispatched_at'       => 'datetime',
            'received_at'         => 'datetime',
        ];
    }

    public function aggregator()   { return $this->belongsTo(Aggregator::class); }
    public function recycler()     { return $this->belongsTo(Recycler::class); }
    public function dispatcher()   { return $this->belongsTo(User::class, 'dispatched_by'); }
    public function confirmer()    { return $this->belongsTo(User::class, 'confirmed_by'); }

    public static function generateShipmentCode(): string
    {
        $date   = now()->format('Ymd');
        $prefix = "SHP-{$date}-";
        $last   = static::where('shipment_code', 'like', "{$prefix}%")
                        ->orderByDesc('shipment_code')
                        ->value('shipment_code');
        $seq    = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'dispatched' => 'Dikirim',
            'in_transit' => 'Dalam Perjalanan',
            'received'   => 'Diterima',
            'cancelled'  => 'Dibatalkan',
            default      => $this->status,
        };
    }
}
