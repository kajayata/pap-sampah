<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\WasteBank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WasteBankController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->loadMissing('role');
        $isSuperAdmin = $user->role?->name === 'super_admin_kecamatan';

        $query = WasteBank::withCoordinates()->with('village:id,name');

        if (!$isSuperAdmin && $user->village_id) {
            $query->where('village_id', $user->village_id);
        } elseif ($request->filled('village_id')) {
            $query->where('village_id', (int) $request->input('village_id'));
        }

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', $search)
                  ->orWhere('address', 'ilike', $search);
            });
        }

        $wasteBanks = $query->orderBy('name')->paginate(10)->withQueryString();
        $villages = Village::where('is_active', true)->orderBy('name')->get();

        return view('waste_banks.index', compact('wasteBanks', 'villages', 'isSuperAdmin'));
    }

    public function create(Request $request): View
    {
        $user = $request->user()->loadMissing('role');
        $isSuperAdmin = $user->role?->name === 'super_admin_kecamatan';

        $villages = $isSuperAdmin
            ? Village::where('is_active', true)->orderBy('name')->get()
            : Village::where('id', $user->village_id)->get();

        $wasteBank = new WasteBank([
            'is_active' => true,
            'village_id' => $user->village_id,
        ]);

        return view('waste_banks.form', compact('wasteBank', 'villages', 'isSuperAdmin'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'village_id' => ['required', 'integer', 'exists:villages,id'],
            'name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:1000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'is_active' => ['boolean'],
        ]);

        $lat = (float) $request->input('latitude');
        $lng = (float) $request->input('longitude');

        DB::statement("
            INSERT INTO waste_banks (village_id, name, address, phone, description, is_active, location)
            VALUES (?, ?, ?, ?, ?, ?, ST_SetSRID(ST_Point(?, ?), 4326))
        ", [
            $request->input('village_id'),
            $request->input('name'),
            $request->input('address'),
            $request->input('phone'),
            $request->input('description'),
            $request->boolean('is_active', true),
            $lng,
            $lat,
        ]);

        return redirect()->route('waste-banks.index')->with('success', 'Bank Sampah berhasil ditambahkan.');
    }

    public function edit(Request $request, int $id): View
    {
        $user = $request->user()->loadMissing('role');
        $isSuperAdmin = $user->role?->name === 'super_admin_kecamatan';

        $wasteBank = WasteBank::withCoordinates()->findOrFail($id);

        if (!$isSuperAdmin && (int) $wasteBank->village_id !== (int) $user->village_id) {
            abort(403, 'Anda tidak berwenang mengedit Bank Sampah di luar kelurahan Anda.');
        }

        $villages = $isSuperAdmin
            ? Village::where('is_active', true)->orderBy('name')->get()
            : Village::where('id', $user->village_id)->get();

        return view('waste_banks.form', compact('wasteBank', 'villages', 'isSuperAdmin'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $user = $request->user()->loadMissing('role');
        $isSuperAdmin = $user->role?->name === 'super_admin_kecamatan';

        $wasteBank = WasteBank::findOrFail($id);

        if (!$isSuperAdmin && (int) $wasteBank->village_id !== (int) $user->village_id) {
            abort(403, 'Anda tidak berwenang memperbarui Bank Sampah di luar kelurahan Anda.');
        }

        $request->validate([
            'village_id' => ['required', 'integer', 'exists:villages,id'],
            'name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:1000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'is_active' => ['boolean'],
        ]);

        $lat = (float) $request->input('latitude');
        $lng = (float) $request->input('longitude');

        DB::statement("
            UPDATE waste_banks
            SET village_id = ?, name = ?, address = ?, phone = ?, description = ?, is_active = ?, location = ST_SetSRID(ST_Point(?, ?), 4326)
            WHERE id = ?
        ", [
            $request->input('village_id'),
            $request->input('name'),
            $request->input('address'),
            $request->input('phone'),
            $request->input('description'),
            $request->boolean('is_active', true),
            $lng,
            $lat,
            $id,
        ]);

        return redirect()->route('waste-banks.index')->with('success', 'Data Bank Sampah berhasil diperbarui.');
    }

    public function toggleStatus(int $id): RedirectResponse
    {
        $wasteBank = WasteBank::findOrFail($id);
        $wasteBank->is_active = !$wasteBank->is_active;
        $wasteBank->save();

        $status = $wasteBank->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Bank Sampah {$wasteBank->name} berhasil {$status}.");
    }
}
