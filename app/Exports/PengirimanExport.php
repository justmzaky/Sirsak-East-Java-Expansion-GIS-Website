<?php

namespace App\Exports;

use App\Models\Shipment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PengirimanExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function query()
    {
        return Shipment::query()->with(['aggregator', 'recycler'])->orderBy('dispatched_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'No. Pengiriman',
            'Tanggal Kirim',
            'Agregator Pengirim',
            'Recycler Penerima',
            'Jenis Material',
            'Info Kendaraan',
            'Berat Kirim (Kg)',
            'Berat Diterima (Kg)',
            'Status',
            'Tanggal Terima',
        ];
    }

    public function map($shipment): array
    {
        $statusLabel = [
            'pending' => 'Menunggu',
            'in_transit' => 'Dalam Perjalanan',
            'received' => 'Diterima',
            'cancelled' => 'Dibatalkan'
        ];

        return [
            $shipment->shipment_code,
            $shipment->dispatched_at ? $shipment->dispatched_at->format('d/m/Y') : '-',
            $shipment->aggregator ? $shipment->aggregator->name : '-',
            $shipment->recycler ? $shipment->recycler->name : '-',
            $shipment->material_type,
            $shipment->vehicle_info,
            $shipment->shipped_weight_kg,
            $shipment->received_weight_kg ?? '-',
            $statusLabel[$shipment->status] ?? $shipment->status,
            $shipment->received_at ? $shipment->received_at->format('d/m/Y') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'FF3B82F6']]],
        ];
    }
}
