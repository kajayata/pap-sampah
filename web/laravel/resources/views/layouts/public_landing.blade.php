<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SampahJember — Platform Pemantauan Sampah Liar Kabupaten Jember')</title>
    <meta name="description" content="Platform pemantauan dan pelaporan pembuangan sampah liar terintegrasi di seluruh wilayah Kabupaten Jember.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400..900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        fraunces: ['Fraunces', 'Georgia', 'serif'],
                        outfit: ['Outfit', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    @vite(['resources/css/sampah-jember-landing.css', 'resources/js/sampah-jember-landing.js'])
</head>
<body class="bg-[#fafaf5] text-stone-900 font-outfit antialiased selection:bg-[#ffd65a] selection:text-[#263e1c] flex flex-col min-h-screen">
    @include('public_landing.partials.navbar')

    <main class="flex-grow">
        @yield('content')
    </main>

    @include('public_landing.partials.footer', ['district' => $district ?? null])
</body>
</html>
