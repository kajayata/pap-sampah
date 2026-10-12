<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (User::where('email', 'kec.sumbersari@papsampah.id')->doesntExist()) {
            $this->seed(\Database\Seeders\DatabaseSeeder::class);
        }
    }

    public function test_super_admin_dashboard_renders_with_summary_panels(): void
    {
        $superAdmin = User::where('email', 'kec.sumbersari@papsampah.id')->firstOrFail();

        $this->actingAs($superAdmin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Tingkat Penyelesaian')
            ->assertSee('Distribusi Status')
            ->assertSee('Petugas Teraktif')
            ->assertSee('Laporan Aktif per Kelurahan');
    }

    public function test_village_admin_dashboard_renders_without_village_breakdown(): void
    {
        $adminDesa = User::whereHas('role', fn ($q) => $q->where('name', 'admin_desa'))->firstOrFail();

        $this->actingAs($adminDesa)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Tingkat Penyelesaian')
            ->assertDontSee('Laporan Aktif per Kelurahan');
    }
}
