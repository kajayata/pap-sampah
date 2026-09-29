<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @yield('title', 'Pap Sampah')
    </title>


    {{-- =====================================================
         GOOGLE FONTS
    ====================================================== --}}

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,900&family=Outfit:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >


    {{-- =====================================================
         TAILWIND
    ====================================================== --}}

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

                        display: [
                            'Fraunces',
                            'serif'
                        ],

                        sans: [
                            'Outfit',
                            'sans-serif'
                        ],

                    },


                    borderRadius: {

                        '4xl': '24px',

                    },

                }

            }

        }

    </script>


    {{-- =====================================================
         CUSTOM CSS
    ====================================================== --}}

    <style>

        /* =================================================
           GLOBAL
        ================================================= */

        body {
            font-family: 'Outfit', sans-serif;
        }


        h1,
        h2,
        h3,
        .font-display {
            font-family: 'Fraunces', serif;
        }


        /* =================================================
           TOP NAVBAR
           
           Sidebar width:
           256px = Tailwind w-64

           Sidebar header:
           64px = Tailwind h-16

           Topbar:
           64px

           Jadi garis bawah keduanya benar-benar sejajar.
        ================================================= */

        .top-navbar {

            position: fixed;

            top: 0;

            right: 0;

            left: 0;

            height: 64px;

            display: flex;

            align-items: center;

            justify-content: flex-end;

            padding: 0 28px;

            background: #FFFFFF;

            border-bottom: 1px solid rgba(0, 0, 0, 0.08);

            box-sizing: border-box;

            z-index: 40;

        }


        /*
         * Desktop:
         * Topbar dimulai setelah sidebar.
         *
         * w-64 = 256px
         */

        @media (min-width: 1024px) {

            .top-navbar {

                left: 256px;

            }

        }


        /* =================================================
           PROFILE WRAPPER
        ================================================= */

        .topbar-profile-wrapper {

            position: relative;

        }


        /* =================================================
           PROFILE BUTTON
        ================================================= */

        .topbar-profile {

            display: flex;

            align-items: center;

            gap: 10px;

            padding: 5px 8px;

            border: none;

            background: transparent;

            border-radius: 12px;

            cursor: pointer;

            transition:
                background 0.2s ease;

        }


        .topbar-profile:hover {

            background: #FAFAF5;

        }


        /* =================================================
           PROFILE AVATAR
        ================================================= */

        .topbar-avatar {

            width: 40px;

            height: 40px;

            display: flex;

            align-items: center;

            justify-content: center;

            flex-shrink: 0;

            border-radius: 50%;

            background: rgba(255, 214, 90, 0.35);

            color: #1C1C1C;

            font-size: 14px;

            font-weight: 700;

        }


        /* =================================================
           PROFILE TEXT
        ================================================= */

        .topbar-profile-info {

            display: flex;

            flex-direction: column;

            line-height: 1.2;

            text-align: left;

        }


        .topbar-profile-name {

            font-size: 14px;

            font-weight: 600;

            color: #1C1C1C;

        }


        .topbar-profile-role {

            margin-top: 3px;

            font-size: 11px;

            color: rgba(28, 28, 28, 0.55);

        }


        /* =================================================
           CHEVRON
        ================================================= */

        .profile-chevron {

            margin-left: 2px;

            color: rgba(28, 28, 28, 0.45);

            transition:
                transform 0.2s ease;

        }


        .profile-chevron.active {

            transform: rotate(180deg);

        }


        /* =================================================
           PROFILE DROPDOWN
        ================================================= */

        .profile-dropdown {

            position: absolute;

            top: calc(100% + 10px);

            right: 0;

            width: 220px;

            background: #FFFFFF;

            border: 1px solid rgba(0, 0, 0, 0.08);

            border-radius: 14px;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.10),
                0 3px 10px rgba(0, 0, 0, 0.05);

            overflow: hidden;

            z-index: 100;

            transform-origin: top right;

            animation:
                profileDropdownAnimation 0.15s ease;

        }


        @keyframes profileDropdownAnimation {

            from {

                opacity: 0;

                transform:
                    translateY(-5px)
                    scale(0.98);

            }

            to {

                opacity: 1;

                transform:
                    translateY(0)
                    scale(1);

            }

        }


        /* =================================================
           DROPDOWN ITEM
        ================================================= */

        .profile-dropdown-item {

            width: 100%;

            display: flex;

            align-items: center;

            gap: 14px;

            padding: 14px 17px;

            border: none;

            background: transparent;

            color: #1C1C1C;

            text-decoration: none;

            font-family: 'Outfit', sans-serif;

            font-size: 14px;

            font-weight: 500;

            text-align: left;

            cursor: pointer;

            transition:
                background 0.2s ease,
                color 0.2s ease;

        }


        .profile-dropdown-item svg {

            flex-shrink: 0;

            color: rgba(28, 28, 28, 0.45);

            transition:
                color 0.2s ease;

        }


        .profile-dropdown-item:hover {

            background: #FAFAF5;

        }


        .profile-dropdown-item:hover svg {

            color: #5B7E3C;

        }


        /* =================================================
           DROPDOWN DIVIDER
        ================================================= */

        .profile-dropdown-divider {

            height: 1px;

            background: rgba(0, 0, 0, 0.08);

            margin: 2px 0;

        }


        /* =================================================
           LOGOUT
        ================================================= */

        .profile-logout {

            color: #EA5252;

        }


        .profile-logout svg {

            color: #EA5252;

        }


        .profile-logout:hover {

            background: rgba(234, 82, 82, 0.08);

            color: #EA5252;

        }


        .profile-logout:hover svg {

            color: #EA5252;

        }


        /* =================================================
           MOBILE NAVBAR
        ================================================= */

        .mobile-navbar-left {

            display: flex;

            align-items: center;

            gap: 10px;

        }


        .mobile-menu-button {

            width: 40px;

            height: 40px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            border: none;

            border-radius: 10px;

            background: transparent;

            color: #1C1C1C;

            cursor: pointer;

        }


        .mobile-menu-button:hover {

            background: #FAFAF5;

        }


        .mobile-brand {

            display: flex;

            align-items: center;

            gap: 8px;

        }


        .mobile-brand-icon {

            width: 34px;

            height: 34px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 9px;

            background: #5B7E3C;

        }


        .mobile-brand-name {

            font-family: 'Fraunces', serif;

            font-size: 18px;

            font-weight: 700;

            color: #1C1C1C;

        }


        .mobile-brand-name span {

            color: #5B7E3C;

        }


        /* =================================================
           MOBILE PROFILE
        ================================================= */

        @media (max-width: 767px) {

            .topbar-profile-info {

                display: none;

            }


            .topbar-profile {

                padding: 3px;

            }


            .topbar-avatar {

                width: 38px;

                height: 38px;

            }


            .profile-chevron {

                display: none;

            }


            .profile-dropdown {

                right: 0;

                width: 210px;

            }

        }

    </style>


    @stack('styles')

    @stack('head')

</head>


<body class="min-h-screen bg-base text-text-primary">


    {{-- =====================================================
         AUTHENTICATED USER
    ====================================================== --}}

    @auth


        {{-- =================================================
             TOP NAVBAR
             
             DESKTOP:
             Hanya profile di kanan.

             MOBILE:
             Hamburger + PapSampah + profile.
        ================================================== --}}

        <header class="top-navbar">


            {{-- MOBILE LEFT --}}

            <div class="mobile-navbar-left lg:hidden">


                {{-- Hamburger --}}

                <button
                    type="button"
                    data-sidebar-toggle
                    aria-controls="admin-sidebar"
                    aria-expanded="false"
                    aria-label="Buka menu"
                    class="mobile-menu-button"
                >

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <line
                            x1="4"
                            y1="6"
                            x2="20"
                            y2="6"
                        />

                        <line
                            x1="4"
                            y1="12"
                            x2="20"
                            y2="12"
                        />

                        <line
                            x1="4"
                            y1="18"
                            x2="20"
                            y2="18"
                        />

                    </svg>

                </button>


                {{-- Mobile Brand --}}

                <a
                    href="{{ route('dashboard') }}"
                    class="mobile-brand"
                >

                    <span class="mobile-brand-icon">

                        <svg
                            class="h-5 w-5 text-text-inverse"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >

                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>

                            <path d="M2 17l10 5 10-5"/>

                            <path d="M2 12l10 5 10-5"/>

                        </svg>

                    </span>


                    <span class="mobile-brand-name">

                        Pap<span>Sampah</span>

                    </span>

                </a>

            </div>


            {{-- =================================================
                 PROFILE USER
                 
                 Ini satu-satunya profile di topbar.
            ================================================== --}}

            <div class="topbar-profile-wrapper">


                {{-- PROFILE BUTTON --}}

                <button
                    type="button"
                    class="topbar-profile"
                    id="profile-button"
                    aria-expanded="false"
                    aria-haspopup="true"
                >


                    {{-- Avatar --}}

                    <div class="topbar-avatar">

                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}

                    </div>


                    {{-- Nama + Role --}}

                    <div class="topbar-profile-info">

                        <span class="topbar-profile-name">

                            {{ Auth::user()->name }}

                        </span>


                        <span class="topbar-profile-role">

                            {{ ucfirst(str_replace('_', ' ', Auth::user()->role?->name ?? 'Pengguna')) }}

                        </span>

                    </div>


                    {{-- Chevron --}}

                    <svg
                        class="profile-chevron"
                        id="profile-chevron"
                        width="16"
                        height="16"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <polyline points="6 9 12 15 18 9"></polyline>

                    </svg>

                </button>


                {{-- =================================================
                     PROFILE DROPDOWN
                ================================================== --}}

                <div
                    id="profile-dropdown"
                    class="profile-dropdown hidden"
                >


                    {{-- PROFILE --}}

                    <a
                        href="#"
                        class="profile-dropdown-item"
                    >

                        <svg
                            width="20"
                            height="20"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >

                            <path d="M20 21a8 8 0 0 0-16 0"></path>

                            <circle
                                cx="12"
                                cy="7"
                                r="4"
                            ></circle>

                        </svg>


                        <span>
                            Profile
                        </span>

                    </a>


                    {{-- DIVIDER --}}

                    <div class="profile-dropdown-divider"></div>


                    {{-- LOGOUT --}}

                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >

                        @csrf


                        <button
                            type="submit"
                            class="profile-dropdown-item profile-logout"
                        >

                            <svg
                                width="20"
                                height="20"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>

                                <polyline points="16 17 21 12 16 7"></polyline>

                                <line
                                    x1="21"
                                    y1="12"
                                    x2="9"
                                    y2="12"
                                ></line>

                            </svg>


                            <span>
                                Logout
                            </span>

                        </button>

                    </form>

                </div>

            </div>

        </header>


        {{-- =================================================
             MOBILE SIDEBAR OVERLAY
        ================================================== --}}

        <button
            type="button"
            data-sidebar-overlay
            aria-label="Tutup menu"
            class="fixed inset-0 z-40 hidden bg-text-primary/40 lg:hidden"
        ></button>


        {{-- =================================================
             SIDEBAR
        ================================================== --}}

        <aside
            id="admin-sidebar"
            class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-border-default bg-surface transition-transform duration-200 lg:translate-x-0"
        >


            {{-- =================================================
                 LOGO
                 
                 Tinggi tepat 64px.
                 Sejajar dengan topbar.
            ================================================== --}}

            <div
                class="flex h-16 shrink-0 items-center justify-between border-b border-border-default px-5"
            >


                {{-- LOGO PAP SAMPAH --}}

                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-2.5"
                >


                    <span
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-emphasis"
                    >

                        <svg
                            class="h-5 w-5 text-text-inverse"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >

                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>

                            <path d="M2 17l10 5 10-5"/>

                            <path d="M2 12l10 5 10-5"/>

                        </svg>

                    </span>


                    <span class="font-display text-lg font-bold text-text-primary">

                        Pap<span class="text-accent-primary">
                            Sampah
                        </span>

                    </span>

                </a>


                {{-- MOBILE CLOSE BUTTON --}}

                <button
                    type="button"
                    data-sidebar-close
                    aria-label="Tutup menu"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-text-muted hover:bg-base lg:hidden"
                >

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <line
                            x1="18"
                            y1="6"
                            x2="6"
                            y2="18"
                        />

                        <line
                            x1="6"
                            y1="6"
                            x2="18"
                            y2="18"
                        />

                    </svg>

                </button>

            </div>


            {{-- =================================================
                 NAVIGATION
                 
                 TIDAK ADA PROFILE USER DI SINI.
            ================================================== --}}

            <nav
                aria-label="Navigasi admin"
                class="flex-1 space-y-6 overflow-y-auto px-3 py-5"
            >


                {{-- =================================================
                     MENU UTAMA
                ================================================== --}}

                <div>

                    <p class="px-3 pb-2 text-xs font-semibold uppercase text-text-muted">

                        Menu Utama

                    </p>


                    <div class="space-y-1">


                        {{-- DASHBOARD --}}

                        <a
                            href="{{ route('dashboard') }}"
                            aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-accent-primary text-text-inverse' : 'text-text-muted hover:bg-base hover:text-text-primary' }}"
                        >

                            <svg
                                class="h-5 w-5 shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <rect
                                    x="3"
                                    y="3"
                                    width="7"
                                    height="7"
                                />

                                <rect
                                    x="14"
                                    y="3"
                                    width="7"
                                    height="7"
                                />

                                <rect
                                    x="14"
                                    y="14"
                                    width="7"
                                    height="7"
                                />

                                <rect
                                    x="3"
                                    y="14"
                                    width="7"
                                    height="7"
                                />

                            </svg>


                            Dashboard

                        </a>


                        {{-- PETA --}}

                        <a
                            href="{{ route('map.index') }}"
                            aria-current="{{ request()->routeIs('map.*') ? 'page' : 'false' }}"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('map.*') ? 'bg-accent-primary text-text-inverse' : 'text-text-muted hover:bg-base hover:text-text-primary' }}"
                        >

                            <svg
                                class="h-5 w-5 shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <path d="M3 6l6-3 6 3 6-3v15l-6 3-6-3-6 3z"/>

                                <path d="M9 3v15"/>

                                <path d="M15 6v15"/>

                            </svg>


                            Peta & Heatmap

                        </a>


                        {{-- LAPORAN --}}

                        <a
                            href="{{ route('reports.index') }}"
                            aria-current="{{ request()->routeIs('reports.*') ? 'page' : 'false' }}"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('reports.*') ? 'bg-accent-primary text-text-inverse' : 'text-text-muted hover:bg-base hover:text-text-primary' }}"
                        >

                            <svg
                                class="h-5 w-5 shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>

                                <path d="M14 2v6h6"/>

                                <path d="M8 13h8"/>

                                <path d="M8 17h8"/>

                            </svg>


                            Laporan Sampah

                        </a>

                    </div>

                </div>


                {{-- =================================================
                     PENGELOLAAN
                ================================================== --}}

                <div>

                    <p class="px-3 pb-2 text-xs font-semibold uppercase text-text-muted">

                        Pengelolaan

                    </p>


                    <div class="space-y-1">


                        {{-- PETUGAS --}}

                        @if(
                            Auth::user()->hasRole('admin_desa')
                            ||
                            Auth::user()->hasRole('super_admin_kecamatan')
                        )

                            <a
                                href="{{ route('petugas.index') }}"
                                aria-current="{{ request()->routeIs('petugas.*') ? 'page' : 'false' }}"
                                class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('petugas.*') ? 'bg-accent-primary text-text-inverse' : 'text-text-muted hover:bg-base hover:text-text-primary' }}"
                            >

                                <svg
                                    class="h-5 w-5 shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >

                                    <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/>

                                    <circle
                                        cx="9"
                                        cy="7"
                                        r="4"
                                    />

                                    <path d="M20 8v6"/>

                                    <path d="M23 11h-6"/>

                                </svg>


                                Petugas

                            </a>

                        @endif


                        {{-- BANK SAMPAH --}}

                        <a
                            href="{{ route('waste-banks.index') }}"
                            aria-current="{{ request()->routeIs('waste-banks.*') ? 'page' : 'false' }}"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('waste-banks.*') ? 'bg-accent-primary text-text-inverse' : 'text-text-muted hover:bg-base hover:text-text-primary' }}"
                        >

                            <svg
                                class="h-5 w-5 shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <path d="M3 10h18"/>

                                <path d="M5 10V7l7-4 7 4v3"/>

                                <path d="M5 10v10h14V10"/>

                                <path d="M9 20v-6h6v6"/>

                            </svg>


                            Bank Sampah

                        </a>


                        {{-- TPA --}}

                        @if(
                            Auth::user()->hasRole('super_admin_kecamatan')
                        )

                            <a
                                href="{{ route('landfills.index') }}"
                                aria-current="{{ request()->routeIs('landfills.*') ? 'page' : 'false' }}"
                                class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('landfills.*') ? 'bg-accent-primary text-text-inverse' : 'text-text-muted hover:bg-base hover:text-text-primary' }}"
                            >

                                <svg
                                    class="h-5 w-5 shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >

                                    <path d="M3 21h18"/>

                                    <path d="M5 21V8l7-5 7 5v13"/>

                                    <path d="M9 21v-5h6v5"/>

                                    <path d="M9 10h.01"/>

                                    <path d="M15 10h.01"/>

                                </svg>


                                TPA & TPS-3R

                            </a>

                        @endif

                    </div>

                </div>


                {{-- =================================================
                     INFORMASI
                ================================================== --}}

                <div>

                    <p class="px-3 pb-2 text-xs font-semibold uppercase text-text-muted">

                        Informasi

                    </p>


                    {{-- BERITA --}}

                    <a
                        href="{{ route('news.index') }}"
                        aria-current="{{ request()->routeIs('news.*') ? 'page' : 'false' }}"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('news.*') ? 'bg-accent-primary text-text-inverse' : 'text-text-muted hover:bg-base hover:text-text-primary' }}"
                    >

                        <svg
                            class="h-5 w-5 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >

                            <path d="M4 19.5A2.5 2.5 0 016.5 17H20"/>

                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>

                        </svg>


                        Berita & Edukasi

                    </a>

                </div>

            </nav>

        </aside>

    @endauth


    {{-- =====================================================
         MAIN CONTENT
         
         64px top padding:
         mengikuti tinggi topbar.

         256px left margin:
         mengikuti lebar sidebar.
    ====================================================== --}}

    <main
        class="{{ auth()->check() ? 'min-h-screen pt-16 lg:ml-64 lg:pt-16' : (request()->routeIs('login') ? '' : 'pt-16') }}"
    >


        {{-- =================================================
             SUCCESS MESSAGE
        ================================================== --}}

        @if(session('success'))

            <div class="mx-auto mt-4 max-w-7xl px-6">

                <div
                    class="rounded-2xl border border-accent-primary/20 bg-accent-primary/10 p-4 text-sm font-medium text-accent-primary"
                >

                    {{ session('success') }}

                </div>

            </div>

        @endif


        {{-- =================================================
             CONTENT DARI SETIAP HALAMAN
        ================================================== --}}

        @yield('content')


    </main>


    @auth

        {{-- =====================================================
             JAVASCRIPT
        ====================================================== --}}

        <script>

            (() => {


                /* =================================================
                   SIDEBAR
                ================================================= */

                const sidebar =
                    document.getElementById(
                        'admin-sidebar'
                    );


                const sidebarToggle =
                    document.querySelector(
                        '[data-sidebar-toggle]'
                    );


                const sidebarClose =
                    document.querySelector(
                        '[data-sidebar-close]'
                    );


                const sidebarOverlay =
                    document.querySelector(
                        '[data-sidebar-overlay]'
                    );


                const setSidebarOpen = (isOpen) => {


                    if (
                        !sidebar ||
                        !sidebarOverlay
                    ) {

                        return;

                    }


                    sidebar.classList.toggle(
                        '-translate-x-full',
                        !isOpen
                    );


                    sidebarOverlay.classList.toggle(
                        'hidden',
                        !isOpen
                    );


                    if (sidebarToggle) {

                        sidebarToggle.setAttribute(
                            'aria-expanded',
                            String(isOpen)
                        );

                        sidebarToggle.setAttribute(
                            'aria-label',
                            isOpen
                                ? 'Tutup menu'
                                : 'Buka menu'
                        );

                    }

                };


                /* Open sidebar */

                if (sidebarToggle) {

                    sidebarToggle.addEventListener(
                        'click',
                        () => {

                            setSidebarOpen(true);

                        }
                    );

                }


                /* Close sidebar */

                if (sidebarClose) {

                    sidebarClose.addEventListener(
                        'click',
                        () => {

                            setSidebarOpen(false);

                        }
                    );

                }


                /* Click overlay */

                if (sidebarOverlay) {

                    sidebarOverlay.addEventListener(
                        'click',
                        () => {

                            setSidebarOpen(false);

                        }
                    );

                }


                /* Click menu */

                if (sidebar) {

                    sidebar
                        .querySelectorAll('a')
                        .forEach((link) => {

                            link.addEventListener(
                                'click',
                                () => {

                                    if (
                                        window.matchMedia(
                                            '(max-width: 1023px)'
                                        ).matches
                                    ) {

                                        setSidebarOpen(false);

                                    }

                                }
                            );

                        });

                }


                /* =================================================
                   PROFILE DROPDOWN
                ================================================= */

                const profileButton =
                    document.getElementById(
                        'profile-button'
                    );


                const profileDropdown =
                    document.getElementById(
                        'profile-dropdown'
                    );


                const profileChevron =
                    document.getElementById(
                        'profile-chevron'
                    );


                /* Open / close dropdown */

                if (
                    profileButton &&
                    profileDropdown
                ) {

                    profileButton.addEventListener(
                        'click',
                        (event) => {

                            event.stopPropagation();


                            const isOpen =
                                !profileDropdown
                                    .classList
                                    .contains('hidden');


                            if (isOpen) {


                                /* CLOSE */

                                profileDropdown
                                    .classList
                                    .add('hidden');


                                profileChevron
                                    ?.classList
                                    .remove('active');


                                profileButton
                                    .setAttribute(
                                        'aria-expanded',
                                        'false'
                                    );


                            } else {


                                /* OPEN */

                                profileDropdown
                                    .classList
                                    .remove('hidden');


                                profileChevron
                                    ?.classList
                                    .add('active');


                                profileButton
                                    .setAttribute(
                                        'aria-expanded',
                                        'true'
                                    );

                            }

                        }
                    );


                    /* Click outside */

                    document.addEventListener(
                        'click',
                        (event) => {


                            if (

                                !profileDropdown
                                    .contains(event.target)

                                &&

                                !profileButton
                                    .contains(event.target)

                            ) {


                                profileDropdown
                                    .classList
                                    .add('hidden');


                                profileChevron
                                    ?.classList
                                    .remove('active');


                                profileButton
                                    .setAttribute(
                                        'aria-expanded',
                                        'false'
                                    );

                            }

                        }
                    );


                    /* ESC */

                    document.addEventListener(
                        'keydown',
                        (event) => {


                            if (
                                event.key === 'Escape'
                            ) {


                                profileDropdown
                                    .classList
                                    .add('hidden');


                                profileChevron
                                    ?.classList
                                    .remove('active');


                                profileButton
                                    .setAttribute(
                                        'aria-expanded',
                                        'false'
                                    );

                            }

                        }
                    );

                }


            })();

        </script>

    @endauth


    @stack('scripts')


</body>

</html>