<?php

namespace App\Exports;

use App\Models\Collection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenimbanganExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function query()
    {
        return Collection::query()->with(['wasteUnit', 'aggregator'])->orderBy('collected_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'No. Transaksi',
            'Tanggal',
            'BSU Asal',
            'Agregator Tujuan',
            'Jenis Material',
            'Kondisi',
            'Berat Kotor (Kg)',
            'Berat Potongan/Tare (Kg)',
            'Berat Bersih (Kg)',
            'Harga Satuan (Rp)',
            'Total Nilai (Rp)',
        ];
    }

    public function map($collection): array
    {
        return [
            $collection->transaction_code,
            $collection->collected_at ? $collection->collected_at->format('d/m/Y') : '-',
            $collection->wasteUnit ? $collection->wasteUnit->name : '-',
            $collection->aggregator ? $collection->aggregator->name : '-',
            $collection->material_type,
            $collection->material_condition,
            $collection->gross_weight_kg,
            $collection->tare_weight_kg,
            $collection->net_weight_kg,
            $collection->price_per_kg,
            $collection->total_value,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'FF10B981']]],
        ];
    }
}
