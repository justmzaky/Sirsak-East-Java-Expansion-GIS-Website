<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Exports\PenimbanganExport;
use App\Exports\PengirimanExport;
use App\Exports\MaterialFlowExport;
use App\Exports\RecyclerExport;
use App\Exports\EntitasExport;
use App\Exports\AktivitasExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('user')->orderByDesc('created_at')->limit(50)->get();
        return view('superadmin.laporan.index', compact('logs'));
    }

    public function exportPenimbangan()
    {
        return Excel::download(new PenimbanganExport, 'Laporan_Penimbangan_BSU.xlsx');
    }

    public function exportPengiriman()
    {
        return Excel::download(new PengirimanExport, 'Laporan_Pengiriman_Agregator.xlsx');
    }

    public function exportMaterialFlow()
    {
        return Excel::download(new MaterialFlowExport, 'Laporan_Material_Flow.xlsx');
    }

    public function exportRecycler()
    {
        return Excel::download(new RecyclerExport, 'Laporan_Penerimaan_Recycler.xlsx');
    }

    public function exportEntitas()
    {
        return Excel::download(new EntitasExport, 'Laporan_Daftar_Entitas.xlsx');
    }

    public function exportAktivitas()
    {
        return Excel::download(new AktivitasExport, 'Laporan_Log_Aktivitas.xlsx');
    }
}
