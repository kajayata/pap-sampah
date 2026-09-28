<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pap Sampah')</title>

    {{-- Google Fonts: Fraunces + Outfit --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        base: '#FAFAF5',
                        surface: '#FFFFFF',
                        emphasis: '#5B7E3C',
                        'accent-primary': '#5B7E3C',
                        'accent-secondary': '#FFD65A',
                        'accent-warning': '#FF9D23',
                        'accent-danger': '#EA5252',
                        'text-primary': '#1C1C1C',
                        'text-muted': 'rgba(28,28,28,0.60)',
                        'text-inverse': '#FFFFFF',
                        'border-default': 'rgba(0,0,0,0.08)',
                        'border-accent': 'rgba(91,126,60,0.20)',
                    },
                    fontFamily: {
                        display: ['Fraunces', 'serif'],
                        sans: ['Outfit', 'sans-serif'],
                    },
                    borderRadius: {
                        '4xl': '24px',
                    },
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Outfit', sans-serif; }
        h1, h2, h3, .font-display { font-family: 'Fraunces', serif; }
    </style>

    @stack('styles')
    @stack('head')
</head>
<body class="bg-base min-h-screen text-text-primary">
    @auth
    <header class="fixed inset-x-0 top-0 z-40 flex h-16 items-center justify-between border-b border-border-default bg-surface px-4 lg:hidden">
        <button type="button" data-sidebar-toggle aria-controls="admin-sidebar" aria-expanded="false" aria-label="Buka menu" class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-text-primary hover:bg-base">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/>
            </svg>
        </button>
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emphasis">
                <svg class="h-5 w-5 text-text-inverse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
                </svg>
            </span>
            <span class="font-display text-lg font-bold text-text-primary">Pap<span class="text-accent-primary">Sampah</span></span>
        </a>
        <span class="h-10 w-10" aria-hidden="true"></span>
    </header>

    <button type="button" data-sidebar-overlay aria-label="Tutup menu" class="fixed inset-0 z-40 hidden bg-text-primary/40 lg:hidden"></button>

    <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-border-default bg-surface transition-transform duration-200 lg:translate-x-0">
        <div class="flex h-16 shrink-0 items-center justify-between border-b border-border-default px-5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emphasis">
                    <svg class="h-5 w-5 text-text-inverse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
                    </svg>
                </span>
                <span class="font-display text-lg font-bold text-text-primary">Pap<span class="text-accent-primary">Sampah</span></span>
            </a>
            <button type="button" data-sidebar-close aria-label="Tutup menu" class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-text-muted hover:bg-base lg:hidden">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <div class="border-b border-border-default px-5 py-4">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-accent-secondary/30 text-sm font-bold text-text-primary">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-text-primary">{{ Auth::user()->name }}</p>
                    <p class="truncate text-xs text-text-muted">{{ ucfirst(str_replace('_', ' ', Auth::user()->role?->name ?? 'Pengguna')) }}</p>
                </div>
            </div>
        </div>

        <nav aria-label="Navigasi admin" class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
            <div>
                <p class="px-3 pb-2 text-xs font-semibold uppercase text-text-muted">Menu Utama</p>
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}" aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-accent-primary text-text-inverse' : 'text-text-muted hover:bg-base hover:text-text-primary' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('map.index') }}" aria-current="{{ request()->routeIs('map.*') ? 'page' : 'false' }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('map.*') ? 'bg-accent-primary text-text-inverse' : 'text-text-muted hover:bg-base hover:text-text-primary' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6l6-3 6 3 6-3v15l-6 3-6-3-6 3z"/><path d="M9 3v15M15 6v15"/></svg>
                        Peta & Heatmap
                    </a>
                    <a href="{{ route('reports.index') }}" aria-current="{{ request()->routeIs('reports.*') ? 'page' : 'false' }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('reports.*') ? 'bg-accent-primary text-text-inverse' : 'text-text-muted hover:bg-base hover:text-text-primary' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h8"/></svg>
                        Laporan Sampah
                    </a>
                </div>
            </div>

            <div>
                <p class="px-3 pb-2 text-xs font-semibold uppercase text-text-muted">Pengelolaan</p>
                <div class="space-y-1">
                    @if(Auth::user()->hasRole('admin_desa') || Auth::user()->hasRole('super_admin_kecamatan'))
                    <a href="{{ route('petugas.index') }}" aria-current="{{ request()->routeIs('petugas.*') ? 'page' : 'false' }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('petugas.*') ? 'bg-accent-primary text-text-inverse' : 'text-text-muted hover:bg-base hover:text-text-primary' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/></svg>
                        Petugas
                    </a>
                    @endif
                    <a href="{{ route('waste-banks.index') }}" aria-current="{{ request()->routeIs('waste-banks.*') ? 'page' : 'false' }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('waste-banks.*') ? 'bg-accent-primary text-text-inverse' : 'text-text-muted hover:bg-base hover:text-text-primary' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10h18M5 10V7l7-4 7 4v3M5 10v10h14V10M9 20v-6h6v6"/></svg>
                        Bank Sampah
                    </a>
                    @if(Auth::user()->hasRole('super_admin_kecamatan'))
                    <a href="{{ route('landfills.index') }}" aria-current="{{ request()->routeIs('landfills.*') ? 'page' : 'false' }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('landfills.*') ? 'bg-accent-primary text-text-inverse' : 'text-text-muted hover:bg-base hover:text-text-primary' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-5h6v5M9 10h.01M15 10h.01"/></svg>
                        TPA & TPS-3R
                    </a>
                    @endif
                </div>
            </div>

            <div>
                <p class="px-3 pb-2 text-xs font-semibold uppercase text-text-muted">Informasi</p>
                <a href="{{ route('news.index') }}" aria-current="{{ request()->routeIs('news.*') ? 'page' : 'false' }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('news.*') ? 'bg-accent-primary text-text-inverse' : 'text-text-muted hover:bg-base hover:text-text-primary' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
                    Berita & Edukasi
                </a>
            </div>
        </nav>

        <div class="shrink-0 border-t border-border-default p-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-text-muted transition-colors hover:bg-accent-danger/10 hover:text-accent-danger">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>
    @endauth

    <main class="{{ auth()->check() ? 'min-h-screen pt-20 lg:ml-64 lg:pt-0' : (request()->routeIs('login') ? '' : 'pt-20') }}">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-6 mt-4">
                <div class="p-4 bg-accent-primary/10 border border-accent-primary/20 text-accent-primary rounded-2xl text-sm font-medium">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @auth
    <script>
        (() => {
            const sidebar = document.getElementById('admin-sidebar');
            const toggle = document.querySelector('[data-sidebar-toggle]');
            const closeButton = document.querySelector('[data-sidebar-close]');
            const overlay = document.querySelector('[data-sidebar-overlay]');

            const setSidebarOpen = (isOpen) => {
                sidebar.classList.toggle('-translate-x-full', !isOpen);
                overlay.classList.toggle('hidden', !isOpen);
                toggle.setAttribute('aria-expanded', String(isOpen));
                toggle.setAttribute('aria-label', isOpen ? 'Tutup menu' : 'Buka menu');
            };

            toggle.addEventListener('click', () => setSidebarOpen(true));
            closeButton.addEventListener('click', () => setSidebarOpen(false));
            overlay.addEventListener('click', () => setSidebarOpen(false));
            sidebar.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => {
                    if (window.matchMedia('(max-width: 1023px)').matches) setSidebarOpen(false);
                });
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') setSidebarOpen(false);
            });
        })();
    </script>
    @endauth

    @stack('scripts')
</body>
</html>
