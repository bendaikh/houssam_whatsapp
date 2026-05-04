<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ChatEasy') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }
    </style>
</head>
<body class="antialiased bg-[#0a1628] text-white">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
        <!-- Logo -->
        <div class="mb-8">
            <a href="/">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-xl">C</span>
                    </div>
                    <span class="text-3xl font-bold text-white">ChatEasy</span>
                </div>
            </a>
        </div>

        <!-- Card -->
        <div class="w-full sm:max-w-md">
            <div class="bg-[#0f1c2e] border border-white/10 shadow-xl rounded-2xl p-8">
                {{ $slot }}
            </div>
        </div>

        <!-- Footer Link -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-400">
                {{ isset($footerText) ? $footerText : '' }}
            </p>
        </div>
    </div>
</body>
</html>
