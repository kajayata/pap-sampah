<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Pap Sampah</title>

    {{-- Google Fonts --}}
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
</head>
<body class="bg-base min-h-screen flex">

    {{-- Left panel: brand --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-[#1a2e0d] via-emphasis to-[#3d5828] items-center justify-center p-12">
        {{-- Decorative circles --}}
        <div class="absolute top-20 left-10 w-64 h-64 bg-accent-secondary/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative z-10 text-center">
            {{-- Logo --}}
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-sm rounded-3xl mb-8 border border-white/20">
                <svg class="w-10 h-10 text-text-inverse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
            </div>

            <h1 class="font-display text-4xl font-black text-text-inverse mb-4">
                Pap<span class="text-accent-secondary">Sampah</span>
            </h1>
            <p class="text-white/70 text-lg max-w-sm mx-auto leading-relaxed">
                Sistem Pelaporan dan Penanganan Sampah Liar
            </p>

            {{-- Stats --}}
            <div class="flex items-center justify-center gap-6 mt-10">
                <div class="text-center">
                    <div class="font-display text-2xl font-black text-accent-secondary">1</div>
                    <div class="text-white/50 text-xs mt-0.5">Kecamatan</div>
                </div>
                <div class="w-px h-8 bg-white/20"></div>
                <div class="text-center">
                    <div class="font-display text-2xl font-black text-accent-secondary">5+</div>
                    <div class="text-white/50 text-xs mt-0.5">Desa</div>
                </div>
                <div class="w-px h-8 bg-white/20"></div>
                <div class="text-center">
                    <div class="font-display text-2xl font-black text-accent-secondary">24/7</div>
                    <div class="text-white/50 text-xs mt-0.5">Aktif</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right panel: form --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-md">

            {{-- Mobile logo --}}
            <div class="lg:hidden text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-emphasis rounded-2xl mb-4">
                    <svg class="w-7 h-7 text-text-inverse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5"/>
                        <path d="M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <h1 class="font-display text-2xl font-bold text-text-primary">Pap<span class="text-accent-secondary">Sampah</span></h1>
            </div>

            {{-- Card --}}
            <div class="bg-surface rounded-2xl border border-border-default p-8 shadow-sm">
                <div class="mb-6">
                    <h2 class="font-display text-2xl font-bold text-text-primary">Masuk ke Akun</h2>
                    <p class="text-text-muted text-sm mt-1">Gunakan email dan password Anda</p>
                </div>

                {{-- Error --}}
                @if($errors->any())
                    <div class="mb-5 p-4 bg-accent-danger/10 border border-accent-danger/20 rounded-xl flex items-start gap-3">
                        <svg class="w-5 h-5 text-accent-danger mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                        <span class="text-sm text-accent-danger">{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-text-primary mb-1.5">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                                    <path d="M22 7l-10 7L2 7"/>
                                </svg>
                            </div>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                class="w-full pl-11 pr-4 py-3 bg-base border border-border-default rounded-2xl text-text-primary placeholder:text-text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent-primary/30 focus:border-accent-primary transition-all duration-200"
                                placeholder="admin@papsampah.id"
                            >
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-text-primary mb-1.5">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                                </svg>
                            </div>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                class="w-full pl-11 pr-12 py-3 bg-base border border-border-default rounded-2xl text-text-primary placeholder:text-text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent-primary/30 focus:border-accent-primary transition-all duration-200"
                                placeholder="Masukkan password"
                            >
                            <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-text-muted hover:text-text-primary transition-colors" tabindex="-1">
                                <svg id="eye-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg id="eye-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                                    <line x1="1" y1="1" x2="23" y2="23"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Remember --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-border-default text-accent-primary focus:ring-accent-primary/30 bg-base">
                            <span class="text-sm text-text-muted">Ingat saya</span>
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="w-full py-3 bg-accent-primary hover:bg-emphasis/90 text-text-inverse font-semibold rounded-2xl transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-accent-primary/20 active:translate-y-0"
                    >
                        Masuk
                    </button>
                </form>
            </div>

            {{-- Footer --}}
            <p class="text-center text-xs text-text-muted mt-6">
                &copy; {{ date('Y') }} Pap Sampah — Kecamatan, Kabupaten Jember
            </p>
        </div>
    </div>

    <script>
        document.getElementById('toggle-password').addEventListener('click', function() {
            const input = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');

            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        });
    </script>

</body>
</html>
