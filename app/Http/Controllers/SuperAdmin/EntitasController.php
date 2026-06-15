<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Aggregator;
use App\Models\Recycler;
use App\Models\WasteUnit;
use Illuminate\Http\Request;

class EntitasController extends Controller
{
    // ─── AGGREGATOR ───────────────────────────────────────────────

    public function aggregatorIndex()
    {
        $aggregators = Aggregator::withCount('wasteUnits')->orderBy('code')->paginate(20);
        return view('superadmin.entitas.aggregator-index', compact('aggregators'));
    }

    public function aggregatorCreate() { return view('superadmin.entitas.aggregator-form'); }

    public function aggregatorStore(Request $request)
    {
        $data = $request->validate([
            'code'      => 'required|string|max:10|unique:aggregators,code',
            'name'      => 'required|string|max:150',
            'pic_name'  => 'nullable|string|max:100',
            'village'   => 'nullable|string|max:100',
            'district'  => 'nullable|string|max:100',
            'regency'   => 'required|string|max:100',
            'phone'     => 'nullable|string|max:20',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ]);

        $agg = Aggregator::create($data);
        ActivityLog::record('created', "Agregator {$agg->code} - {$agg->name} dibuat", $agg);

        return redirect()->route('superadmin.entitas.aggregator.index')
            ->with('success', "Agregator {$agg->code} berhasil ditambahkan.");
    }

    public function aggregatorEdit(Aggregator $aggregator)
    {
        return view('superadmin.entitas.aggregator-form', compact('aggregator'));
    }

    public function aggregatorUpdate(Request $request, Aggregator $aggregator)
    {
        $data = $request->validate([
            'code'      => "required|string|max:10|unique:aggregators,code,{$aggregator->id}",
            'name'      => 'required|string|max:150',
            'pic_name'  => 'nullable|string|max:100',
            'village'   => 'nullable|string|max:100',
            'district'  => 'nullable|string|max:100',
            'regency'   => 'required|string|max:100',
            'phone'     => 'nullable|string|max:20',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ]);

        $old = $aggregator->toArray();
        $aggregator->update($data);
        ActivityLog::record('updated', "Agregator {$aggregator->code} diperbarui", $aggregator, ['before' => $old, 'after' => $aggregator->toArray()]);

        return redirect()->route('superadmin.entitas.aggregator.index')
            ->with('success', "Agregator {$aggregator->code} berhasil diperbarui.");
    }

    public function aggregatorDestroy(Aggregator $aggregator)
    {
        if ($aggregator->wasteUnits()->exists() || $aggregator->collections()->exists() || $aggregator->shipments()->exists()) {
            return back()->with('error', "Agregator {$aggregator->code} tidak dapat dihapus karena masih menampung BSU atau riwayat transaksi.");
        }
        $aggregator->delete();
        ActivityLog::record('deleted', "Agregator {$aggregator->code} dihapus", $aggregator);
        return back()->with('success', "Agregator {$aggregator->code} berhasil dihapus.");
    }

    // ─── WASTE UNIT (BSU) ─────────────────────────────────────────

    public function bsuIndex()
    {
        $wasteUnits = WasteUnit::with('aggregator')->orderBy('code')->paginate(20);
        $aggregators = Aggregator::where('is_active', true)->orderBy('code')->get();
        return view('superadmin.entitas.bsu-index', compact('wasteUnits', 'aggregators'));
    }

    public function bsuCreate()
    {
        $aggregators = Aggregator::where('is_active', true)->orderBy('code')->get();
        return view('superadmin.entitas.bsu-form', compact('aggregators'));
    }

    public function bsuStore(Request $request)
    {
        $data = $request->validate([
            'code'          => 'required|string|max:10|unique:waste_units,code',
            'aggregator_id' => 'nullable|ulid|exists:aggregators,id',
            'name'          => 'required|string|max:150',
            'village'       => 'nullable|string|max:100',
            'district'      => 'nullable|string|max:100',
            'regency'       => 'required|string|max:100',
            'phone'         => 'nullable|string|max:20',
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',
            'joined_at'     => 'nullable|date',
            'is_active'     => 'boolean',
        ]);

        $bsu = WasteUnit::create($data);
        ActivityLog::record('created', "BSU {$bsu->code} - {$bsu->name} dibuat", $bsu);

        return redirect()->route('superadmin.entitas.bsu.index')
            ->with('success', "BSU {$bsu->code} berhasil ditambahkan.");
    }

    public function bsuEdit(WasteUnit $wasteUnit)
    {
        $aggregators = Aggregator::where('is_active', true)->orderBy('code')->get();
        return view('superadmin.entitas.bsu-form', compact('wasteUnit', 'aggregators'));
    }

    public function bsuUpdate(Request $request, WasteUnit $wasteUnit)
    {
        $data = $request->validate([
            'code'          => "required|string|max:10|unique:waste_units,code,{$wasteUnit->id}",
            'aggregator_id' => 'nullable|ulid|exists:aggregators,id',
            'name'          => 'required|string|max:150',
            'village'       => 'nullable|string|max:100',
            'district'      => 'nullable|string|max:100',
            'regency'       => 'required|string|max:100',
            'phone'         => 'nullable|string|max:20',
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',
            'joined_at'     => 'nullable|date',
            'is_active'     => 'boolean',
        ]);

        $old = $wasteUnit->toArray();
        $wasteUnit->update($data);
        ActivityLog::record('updated', "BSU {$wasteUnit->code} diperbarui", $wasteUnit, ['before' => $old, 'after' => $wasteUnit->toArray()]);

        return redirect()->route('superadmin.entitas.bsu.index')
            ->with('success', "BSU {$wasteUnit->code} berhasil diperbarui.");
    }

    public function bsuDestroy(WasteUnit $wasteUnit)
    {
        if ($wasteUnit->collections()->exists()) {
            return back()->with('error', "BSU {$wasteUnit->code} tidak dapat dihapus karena masih memiliki riwayat penimbangan.");
        }
        $wasteUnit->delete();
        ActivityLog::record('deleted', "BSU {$wasteUnit->code} dihapus", $wasteUnit);
        return back()->with('success', "BSU {$wasteUnit->code} berhasil dihapus.");
    }

    // ─── RECYCLER ─────────────────────────────────────────────────

    public function recyclerIndex()
    {
        $recyclers = Recycler::withCount('shipments')->orderBy('code')->paginate(20);
        return view('superadmin.entitas.recycler-index', compact('recyclers'));
    }

    public function recyclerCreate() { return view('superadmin.entitas.recycler-form'); }

    public function recyclerStore(Request $request)
    {
        $data = $request->validate([
            'code'         => 'required|string|max:10|unique:recyclers,code',
            'name'         => 'required|string|max:150',
            'company_type' => 'nullable|string|max:50',
            'pic_name'     => 'nullable|string|max:100',
            'address'      => 'nullable|string',
            'regency'      => 'required|string|max:100',
            'phone'        => 'nullable|string|max:20',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'is_active'    => 'boolean',
        ]);

        $rec = Recycler::create($data);
        ActivityLog::record('created', "Recycler {$rec->code} - {$rec->name} dibuat", $rec);

        return redirect()->route('superadmin.entitas.recycler.index')
            ->with('success', "Recycler {$rec->code} berhasil ditambahkan.");
    }

    public function recyclerEdit(Recycler $recycler)
    {
        return view('superadmin.entitas.recycler-form', compact('recycler'));
    }

    public function recyclerUpdate(Request $request, Recycler $recycler)
    {
        $data = $request->validate([
            'code'         => "required|string|max:10|unique:recyclers,code,{$recycler->id}",
            'name'         => 'required|string|max:150',
            'company_type' => 'nullable|string|max:50',
            'pic_name'     => 'nullable|string|max:100',
            'address'      => 'nullable|string',
            'regency'      => 'required|string|max:100',
            'phone'        => 'nullable|string|max:20',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'is_active'    => 'boolean',
        ]);

        $old = $recycler->toArray();
        $recycler->update($data);
        ActivityLog::record('updated', "Recycler {$recycler->code} diperbarui", $recycler, ['before' => $old, 'after' => $recycler->toArray()]);

        return redirect()->route('superadmin.entitas.recycler.index')
            ->with('success', "Recycler {$recycler->code} berhasil diperbarui.");
    }

    public function recyclerDestroy(Recycler $recycler)
    {
        if ($recycler->shipments()->exists()) {
            return back()->with('error', "Recycler {$recycler->code} tidak dapat dihapus karena masih memiliki riwayat pengiriman.");
        }
        $recycler->delete();
        ActivityLog::record('deleted', "Recycler {$recycler->code} dihapus", $recycler);
        return back()->with('success', "Recycler {$recycler->code} berhasil dihapus.");
    }
}
