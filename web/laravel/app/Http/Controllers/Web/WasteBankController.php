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
            $term = trim($request->input('search'));
            $search = '%' . $term . '%';
            $query->where(function ($q) use ($search, $term) {
                $q->where('name', 'ilike', $search)
                  ->orWhere('address', 'ilike', $search)
                  ->orWhere('phone', 'ilike', $search)
                  ->orWhere('description', 'ilike', $search)
                  ->orWhereHas('village', function ($villageQuery) use ($search) {
                      $villageQuery->where('name', 'ilike', $search);
                  });

                if (strtolower($term) === 'aktif') {
                    $q->orWhere('is_active', true);
                } elseif (strtolower($term) === 'nonaktif') {
                    $q->orWhere('is_active', false);
                }
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
            'name' => ['required', 'string', 'max:150', 'unique:waste_banks,name'],
            'address' => ['required', 'string', 'max:255', 'regex:/^[\pL\pN\s().,\/]+$/u', 'unique:waste_banks,address'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{1,13}$/', 'unique:waste_banks,phone'],
            'description' => ['nullable', 'string', 'max:1000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'is_active' => ['boolean'],
        ], [
            'village_id.required' => 'Kelurahan wajib dipilih.',
            'village_id.exists' => 'Kelurahan yang dipilih tidak valid.',

            'name.required' => 'Nama Bank Sampah wajib diisi.',
            'name.max' => 'Nama Bank Sampah maksimal 150 karakter.',
            'name.unique' => 'Nama Bank Sampah ini sudah terdaftar, gunakan nama yang lain.',

            'address.required' => 'Alamat wajib diisi.',
            'address.max' => 'Alamat maksimal 255 karakter.',
            'address.regex' => 'Format alamat mengandung karakter yang tidak diizinkan.',
            'address.unique' => 'Alamat ini sudah terdaftar oleh Bank Sampah lain.',

            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => 'Nomor telepon harus berupa angka dan terdiri dari 1 hingga 13 digit.',
            'phone.unique' => 'Nomor telepon ini sudah digunakan oleh Bank Sampah lain.',

            'latitude.required' => 'Titik koordinat latitude wajib diisi.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'latitude.between' => 'Nilai latitude harus berada di antara -90 dan 90.',

            'longitude.required' => 'Titik koordinat longitude wajib diisi.',
            'longitude.numeric' => 'Longitude harus berupa angka.',
            'longitude.between' => 'Nilai longitude harus berada di antara -180 dan 180.',
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
            'name' => ['required', 'string', 'max:150', 'unique:waste_banks,name,' . $id],
            'address' => ['required', 'string', 'max:255', 'regex:/^[\pL\pN\s().,\/]+$/u', 'unique:waste_banks,address,' . $id],
            'phone' => ['nullable', 'string', 'regex:/^[0-9]{1,13}$/', 'unique:waste_banks,phone,' . $id],
            'description' => ['nullable', 'string', 'max:1000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'is_active' => ['boolean'],
        ], [
            'village_id.required' => 'Kelurahan wajib dipilih.',
            'village_id.exists' => 'Kelurahan yang dipilih tidak valid.',

            'name.required' => 'Nama Bank Sampah wajib diisi.',
            'name.max' => 'Nama Bank Sampah maksimal 150 karakter.',
            'name.unique' => 'Nama Bank Sampah ini sudah terdaftar, gunakan nama yang lain.',

            'address.required' => 'Alamat wajib diisi.',
            'address.max' => 'Alamat maksimal 255 karakter.',
            'address.regex' => 'Format alamat mengandung karakter yang tidak diizinkan.',
            'address.unique' => 'Alamat ini sudah terdaftar oleh Bank Sampah lain.',

            'phone.regex' => 'Nomor telepon harus berupa angka dan terdiri dari 1 hingga 13 digit.',
            'phone.unique' => 'Nomor telepon ini sudah digunakan oleh Bank Sampah lain.',

            'latitude.required' => 'Titik koordinat latitude wajib diisi.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'latitude.between' => 'Nilai latitude harus berada di antara -90 dan 90.',

            'longitude.required' => 'Titik koordinat longitude wajib diisi.',
            'longitude.numeric' => 'Longitude harus berupa angka.',
            'longitude.between' => 'Nilai longitude harus berada di antara -180 dan 180.',
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

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $user = $request->user()->loadMissing('role');
        $isSuperAdmin = $user->role?->name === 'super_admin_kecamatan';
        $wasteBank = WasteBank::findOrFail($id);

        if (!$isSuperAdmin && (int) $wasteBank->village_id !== (int) $user->village_id) {
            abort(403, 'Anda tidak berwenang menghapus Bank Sampah di luar kelurahan Anda.');
        }

        $wasteBank->delete();

        return redirect()->route('waste-banks.index')->with('success', 'Bank Sampah berhasil dihapus.');
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
