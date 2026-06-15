<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasUlids;

    protected $fillable = [
        'transaction_code', 'waste_unit_id', 'aggregator_id', 'recorded_by',
        'material_type', 'material_condition',
        'gross_weight_kg', 'tare_weight_kg', 'net_weight_kg',
        'price_per_kg', 'total_value', 'notes', 'collected_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_weight_kg' => 'float',
            'tare_weight_kg'  => 'float',
            'net_weight_kg'   => 'float',
            'price_per_kg'    => 'float',
            'total_value'     => 'float',
            'collected_at'    => 'date',
        ];
    }

    public function wasteUnit()
    {
        return $this->belongsTo(WasteUnit::class);
    }

    public function aggregator()
    {
        return $this->belongsTo(Aggregator::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function generateTransactionCode(): string
    {
        $date   = now()->format('Ymd');
        $prefix = "WC-{$date}-";
        $last   = static::where('transaction_code', 'like', "{$prefix}%")
                        ->orderByDesc('transaction_code')
                        ->value('transaction_code');
        $seq    = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
