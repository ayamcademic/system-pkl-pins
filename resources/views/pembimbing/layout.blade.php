<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Sistem PKL - PT PINS' }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

  @vite(['resources/css/app.css','resources/js/app.js'])
  <link rel="stylesheet" href="{{ asset('custom-ui.css') }}">
  @stack('head')
</head>
<body class="font-sans">
  <div x-data="{ open:false }" class="min-h-screen">
    <header class="sticky top-0 z-40 border-b border-black/5 bg-surface-light/80 backdrop-blur">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 py-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
          <button type="button" class="lg:hidden inline-flex h-10 w-10 items-center justify-center rounded-xl border border-black/10 bg-white/70 hover:bg-white"
                  @click="open = true" aria-label="Buka menu">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>

          <div class="flex items-center gap-3 min-w-0">
            <img src="{{ asset('images/PINS_logo_.png') }}" alt="PINS" class="h-8 w-auto shrink-0" />
            <div class="min-w-0">
              <div class="text-xs text-text-secondary">PT PINS Indonesia</div>
              <div class="font-semibold text-text-main leading-tight truncate">{{ $pageTitle ?? 'Dashboard Pembimbing' }}</div>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
          <div class="hidden md:flex items-center gap-3">
            <span class="topbar-badge text-sm text-red-700">Mode Pembimbing</span>
            <div class="text-right">
              <div class="text-ml font-semibold text-text-main ">{{ auth()->user()->name ?? 'User' }}</div>
              <!-- <div class="text-xs text-text-secondary">Kelola peserta, akun, dan laporan</div> -->
            </div>
          </div>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="pins-btn-ghost">Logout</button>
          </form>
        </div>
      </div>
    </header>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 py-6 grid grid-cols-1 lg:grid-cols-[260px,1fr] gap-6">
      <aside class="hidden lg:block">
        <div class="pins-card p-4 sticky top-24">
          <div class="soft-panel mb-4">
            <div class="eyebrow">Workspace</div>
            <div class="mt-2 text-lg font-semibold text-text-main">Sistem Manajemen PKL</div>
            <div class="helper-text mt-1">PT Pins Indonesia </div>
          </div>

          <nav class="space-y-2">
            <a href="{{ route('pembimbing.dashboard') }}"
               class="sidebar-link flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('pembimbing.dashboard') ? 'sidebar-link-active' : 'text-text-main' }}">
              <span>Dashboard</span>
              <span class="text-xs opacity-80">01</span>
            </a>

            <a href="{{ route('pembimbing.peserta.index') }}"
               class="sidebar-link flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('pembimbing.peserta.*') ? 'sidebar-link-active' : 'text-text-main' }}">
              <span>Data Peserta</span>
              <span class="text-xs opacity-80">02</span>
            </a>

            <a href="{{ route('pembimbing.excel.export') }}"
               class="sidebar-link flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-medium text-text-main">
              <span>Export Excel</span>
              <span class="text-xs opacity-80">03</span>
            </a>

            <a href="{{ route('profile.edit') }}"
               class="sidebar-link flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('profile.edit') ? 'sidebar-link-active' : 'text-text-main' }}">
              <span>Profil</span>
              <span class="text-xs opacity-80">04</span>
            </a>
          </nav>

          <div class="notice-box mt-4 text-sm">
            <!-- <div class="font-semibold text-text-main">Shortcut kerja</div> -->
            <div class="helper-text mt-1">Made with ❤️ by Ay</div>
          </div>
        </div>
      </aside>

      <main class="min-w-0">
        @foreach (['success' => 'status-pill-success', 'error' => 'status-pill-danger', 'info' => 'status-pill-neutral'] as $flash => $pill)
          @if (session($flash))
            <div class="mb-4 pins-card p-4 border border-black/10">
              <div class="flex items-start gap-3">
                <div class="mt-0.5 h-10 w-10 rounded-2xl bg-black/5 flex items-center justify-center">{{ $flash === 'success' ? '✓' : ($flash === 'error' ? '!' : 'i') }}</div>
                <div>
                  <span class="status-pill {{ $pill }}">{{ strtoupper($flash) }}</span>
                  <div class="mt-2 text-sm text-text-main">{{ session($flash) }}</div>
                </div>
              </div>
            </div>
          @endif
        @endforeach

        @if ($errors->any())
          <div class="mb-4 pins-card p-4 border border-red-200">
            <div class="text-sm text-red-700">
              <ul class="list-disc ms-4 space-y-1">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        @endif

        @yield('content')
      </main>
    </div>

    <div class="lg:hidden" x-show="open" x-cloak>
      <div class="fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/30" @click="open=false"></div>
        <div class="absolute left-0 top-0 bottom-0 w-80 max-w-[86vw] bg-surface-light shadow-soft p-4 overflow-y-auto">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
              <img src="{{ asset('images/PINS_logo_.png') }}" alt="PINS" class="h-8 w-auto"/>
              <div>
                <div class="text-xs text-text-secondary">PT PINS Indonesia</div>
                <div class="font-semibold text-text-main">Menu Pembimbing</div>
              </div>
            </div>
            <button class="h-10 w-10 rounded-xl border border-black/10 hover:bg-black/5" @click="open=false" aria-label="Tutup">✕</button>
          </div>

          <nav class="mt-5 space-y-2">
            <a href="{{ route('pembimbing.dashboard') }}" class="sidebar-link block rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('pembimbing.dashboard') ? 'sidebar-link-active' : 'text-text-main' }}">Dashboard</a>
            <a href="{{ route('pembimbing.peserta.index') }}" class="sidebar-link block rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('pembimbing.peserta.*') ? 'sidebar-link-active' : 'text-text-main' }}">Data Peserta</a>
            <a href="{{ route('pembimbing.excel.export') }}" class="sidebar-link block rounded-2xl px-4 py-3 text-sm font-medium text-text-main">Export Excel</a>
            <a href="{{ route('profile.edit') }}" class="sidebar-link block rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('profile.edit') ? 'sidebar-link-active' : 'text-text-main' }}">Profil</a>
          </nav>
        </div>
      </div>
    </div>
  </div>

  @stack('scripts')
</body>
</html>
