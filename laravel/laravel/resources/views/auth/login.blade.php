<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Pap Sampah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-green-700">Pap Sampah</h1>
            <p class="text-gray-500 mt-1">Sistem Pelaporan Sampah</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Masuk ke Akun</h2>

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition"
                        placeholder="admin@papsampah.id"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition"
                        placeholder="••••••••"
                    >
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        Ingat saya
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition"
                >
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            &copy; {{ date('Y') }} Pap Sampah — Jember
        </p>
    </div>
</body>
</html>
