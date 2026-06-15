<?php

namespace App\Exports;

use App\Models\Shipment;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RecyclerExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Shipment::query()
            ->join('recyclers', 'shipments.recycler_id', '=', 'recyclers.id')
            ->select(
                'recyclers.name as recycler_name',
                'shipments.material_type',
                DB::raw('SUM(shipments.received_weight_kg) as total_received_kg')
            )
            ->where('shipments.status', 'received')
            ->groupBy('recyclers.name', 'shipments.material_type')
            ->orderBy('recyclers.name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Pabrik Daur Ulang (Recycler)',
            'Jenis Material',
            'Total Diterima (Kg)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->recycler_name,
            $row->material_type,
            $row->total_received_kg,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'FF8B5CF6']]],
        ];
    }
}
