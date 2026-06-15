<?php

namespace App\Exports;

use App\Models\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaterialFlowExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Collection::query()
            ->join('aggregators', 'collections.aggregator_id', '=', 'aggregators.id')
            ->select(
                'collections.material_type',
                'aggregators.name as aggregator_name',
                DB::raw('SUM(collections.net_weight_kg) as total_kg'),
                DB::raw('SUM(collections.total_value) as total_rp')
            )
            ->groupBy('collections.material_type', 'aggregators.name')
            ->orderBy('collections.material_type')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Jenis Material',
            'Agregator',
            'Total Berat Bersih (Kg)',
            'Total Nilai Transaksi (Rp)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->material_type,
            $row->aggregator_name,
            $row->total_kg,
            $row->total_rp,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'FFF59E0B']]],
        ];
    }
}
