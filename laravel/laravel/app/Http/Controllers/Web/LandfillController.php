<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Landfill;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LandfillController extends Controller
{
    public function index(Request $request): View
    {
        $landfills = Landfill::withCoordinates()
            ->with('village:id,name')
            ->orderBy('name')
            ->paginate(10);

        return view('landfills.index', compact('landfills'));
    }

    public function create(): View
    {
        $villages = Village::where('is_active', true)->orderBy('name')->get();
        $landfill = new Landfill(['is_active' => true]);

        return view('landfills.form', compact('landfill', 'villages'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'village_id' => ['required', 'integer', 'exists:villages,id'],
            'name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'is_active' => ['boolean'],
        ]);

        $lat = (float) $request->input('latitude');
        $lng = (float) $request->input('longitude');

        DB::statement("
            INSERT INTO landfills (village_id, name, address, description, is_active, location)
            VALUES (?, ?, ?, ?, ?, ST_SetSRID(ST_Point(?, ?), 4326))
        ", [
            $request->input('village_id'),
            $request->input('name'),
            $request->input('address'),
            $request->input('description'),
            $request->boolean('is_active', true),
            $lng,
            $lat,
        ]);

        return redirect()->route('landfills.index')->with('success', 'Fasilitas TPA / TPS-3R berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $landfill = Landfill::withCoordinates()->findOrFail($id);
        $villages = Village::where('is_active', true)->orderBy('name')->get();

        return view('landfills.form', compact('landfill', 'villages'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'village_id' => ['required', 'integer', 'exists:villages,id'],
            'name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'is_active' => ['boolean'],
        ]);

        $lat = (float) $request->input('latitude');
        $lng = (float) $request->input('longitude');

        DB::statement("
            UPDATE landfills
            SET village_id = ?, name = ?, address = ?, description = ?, is_active = ?, location = ST_SetSRID(ST_Point(?, ?), 4326)
            WHERE id = ?
        ", [
            $request->input('village_id'),
            $request->input('name'),
            $request->input('address'),
            $request->input('description'),
            $request->boolean('is_active', true),
            $lng,
            $lat,
            $id,
        ]);

        return redirect()->route('landfills.index')->with('success', 'Data TPA berhasil diperbarui.');
    }

    public function toggleStatus(int $id): RedirectResponse
    {
        $landfill = Landfill::findOrFail($id);
        $landfill->is_active = !$landfill->is_active;
        $landfill->save();

        $status = $landfill->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Fasilitas {$landfill->name} berhasil {$status}.");
    }
}
