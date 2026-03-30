<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistem PKL - PT PINS') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('custom-ui.css') }}">
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex items-stretch">
        <aside class="hidden lg:flex w-[48%] p-10 items-end relative overflow-hidden">
            <img src="{{ asset('images/uwu.jpg') }}" alt="PT PINS" class="absolute inset-0 h-full w-full object-cover opacity-50" />
            <div class="absolute inset-0 bg-gradient-to-r from-white/90 via-white/72 to-white/92"></div>
            <div class="absolute inset-0" aria-hidden="true">
                <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-primary/10 blur-3xl"></div>
                <div class="absolute top-1/3 -right-24 h-72 w-72 rounded-full bg-black/10 blur-3xl"></div>
                <div class="absolute -bottom-24 left-1/4 h-72 w-72 rounded-full bg-primary/10 blur-3xl"></div>
            </div>

            <div class="relative w-full">
                <div class="inline-flex items-center gap-3 rounded-2xl bg-surface-light/80 backdrop-blur px-4 py-3 border border-black/5 shadow-soft">
                    <div class="h-10 w-10 rounded-xl bg-white flex items-center justify-center border border-black/10 shadow-sm overflow-hidden">
                        <img src="{{ asset('images/PINS_logo_.png') }}" alt="PINS Logo" class="h-6 w-auto">
                    </div>

                    <div>
                        <div class="text-xs text-text-secondary">PT PINS Indonesia</div>
                        <div class="font-semibold text-text-main leading-tight">Sistem Manajemen PKL</div>
                    </div>
                </div>

                <div class="mt-8 max-w-md">
                    <h1 class="font-serif text-4xl leading-tight text-text-main">Semua aktivitas PKL dalam satu dashboard!</h1>
                    <p class="mt-3 text-text-secondary">
                        “The expert in anything was once a beginner.”<br>
— Helen Hayes
                    </p>

                    <div class="mt-8 flex flex-wrap gap-2">
                        <span class="status-pill status-pill-neutral">Monitoring Harian Peserta PKL</span>
                        <span class="status-pill status-pill-neutral">Data Peserta PKL</span>
                        <span class="status-pill status-pill-neutral">Import / Export Excel</span>
                    </div>
                </div>

                <div class="mt-10 text-xs text-text-secondary">© {{ date('Y') }} Sistem PKL PT PINS</div>
            </div>
        </aside>

        <main class="flex-1 flex items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
