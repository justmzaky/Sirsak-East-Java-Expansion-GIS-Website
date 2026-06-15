<?php

namespace App\Exports;

use App\Models\Aggregator;
use App\Models\WasteUnit;
use App\Models\Recycler;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EntitasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        $aggregators = Aggregator::all()->map(function($item) {
            $item->entity_type = 'Aggregator (BSI)';
            return $item;
        });

        $bsus = WasteUnit::all()->map(function($item) {
            $item->entity_type = 'Waste Unit (BSU)';
            return $item;
        });

        $recyclers = Recycler::all()->map(function($item) {
            $item->entity_type = 'Recycler (Pabrik)';
            return $item;
        });

        return $aggregators->concat($bsus)->concat($recyclers);
    }

    public function headings(): array
    {
        return [
            'Tipe Entitas',
            'Kode',
            'Nama Entitas',
            'Kab/Kota',
            'Kecamatan',
            'Kelurahan/Desa',
            'No. Telepon',
            'Status Aktif',
        ];
    }

    public function map($row): array
    {
        return [
            $row->entity_type,
            $row->code,
            $row->name,
            $row->regency,
            $row->district ?? '-',
            $row->village ?? '-',
            $row->phone,
            $row->is_active ? 'Aktif' : 'Tidak Aktif',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'FF6B7280']]],
        ];
    }
}
