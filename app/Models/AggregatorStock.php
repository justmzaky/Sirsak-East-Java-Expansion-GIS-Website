<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class AggregatorStock extends Model
{
    use HasUlids;

    public $timestamps = false;

    protected $fillable = ['aggregator_id', 'material_type', 'stock_kg', 'last_updated_at'];

    protected function casts(): array
    {
        return [
            'stock_kg'         => 'float',
            'last_updated_at'  => 'datetime',
        ];
    }

    public function aggregator()
    {
        return $this->belongsTo(Aggregator::class);
    }
}
