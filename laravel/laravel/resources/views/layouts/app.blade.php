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
</head>
<body class="bg-base min-h-screen text-text-primary">
    @auth
    <nav class="fixed top-0 inset-x-0 z-50 bg-white/95 backdrop-blur-md border-b border-border-default">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between h-16">
                {{-- Logo --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                        <span class="w-8 h-8 bg-emphasis rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-text-inverse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                <path d="M2 17l10 5 10-5"/>
                                <path d="M2 12l10 5 10-5"/>
                            </svg>
                        </span>
                        <span class="font-display font-bold text-lg text-text-primary">Pap<span class="text-accent-secondary">Sampah</span></span>
                    </a>
                    <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent-primary/10 text-accent-primary">
                        {{ ucfirst(str_replace('_', ' ', Auth::user()->role->name)) }}
                    </span>
                </div>

                {{-- Center Nav Links --}}
                <div class="hidden md:flex items-center gap-1.5">
                    <a href="{{ route('dashboard') }}" class="px-3.5 py-1.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-accent-primary text-white font-semibold' : 'text-text-muted hover:text-text-primary hover:bg-base' }}">
                        Dashboard
                    </a>
                    @if(Auth::user()->hasRole('admin_desa') || Auth::user()->hasRole('super_admin_kecamatan'))
                    <a href="{{ route('petugas.index') }}" class="px-3.5 py-1.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('petugas.*') ? 'bg-accent-primary text-white font-semibold' : 'text-text-muted hover:text-text-primary hover:bg-base' }}">
                        Petugas Kebersihan
                    </a>
                    @endif
                </div>

                {{-- Right side --}}
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex items-center gap-2">
                        <span class="w-8 h-8 bg-accent-secondary/20 rounded-full flex items-center justify-center text-sm font-semibold text-text-primary">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                        <span class="text-sm font-medium text-text-primary">{{ Auth::user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-muted hover:text-accent-danger transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main class="{{ request()->routeIs('login') ? '' : 'pt-20' }}">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-6 mt-4">
                <div class="p-4 bg-accent-primary/10 border border-accent-primary/20 text-accent-primary rounded-2xl text-sm font-medium">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
