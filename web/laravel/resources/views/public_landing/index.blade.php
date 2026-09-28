@extends('layouts.public_landing')

@section('title', 'PapSampah — Platform Pemantauan Sampah Liar Terpadu Kabupaten Jember')

@section('content')
    {{-- 1. Hero Section (Latar Hijau Hutan & Statistik Kaca Mengambang) --}}
    @include('public_landing.partials.hero', ['stats' => $stats, 'district' => $district])

    {{-- 2. About Platform Section (Sistem Pemantauan Terintegrasi) --}}
    @include('public_landing.partials.about', ['stats' => $stats])

    {{-- 3. Peta Heatmap Section untuk kelurahan dalam kecamatan aktif --}}
    @include('public_landing.partials.heatmap', compact('district', 'heatmapData', 'wastePoints', 'boundariesGeoJson', 'villages', 'wasteBanks', 'landfills', 'markerDisplayDays'))

    {{-- 4. Data Kecamatan & Search Section (2 Kolom Kartu + Kotak Info) --}}
    @include('public_landing.partials.kecamatan', ['villageCards' => $villageCards, 'district' => $district])

    {{-- 5. Panduan Penggunaan Mobile Section --}}
    {{-- 5. Panduan Penggunaan Mobile Section (6 Langkah Berurutan) --}}
    @include('public_landing.partials.panduan', ['guideSteps' => $guideSteps])

    {{-- 7. Download App CTA Section (Aplikasi Mobile & Mockup Smartphone) --}}
    @include('public_landing.partials.download')
@endsection
