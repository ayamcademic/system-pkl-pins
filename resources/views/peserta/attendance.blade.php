@extends('peserta.layout')

@php
  $pageTitle = 'Absensi';
  $in = $attendanceToday?->check_in_at;
  $out = $attendanceToday?->check_out_at;
@endphp

@section('content')
  <div class="space-y-6">
    <div class="hero-card p-6">
      <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
          <div class="eyebrow">Absensi Peserta</div>
          <h1 class="mt-2 font-serif text-3xl text-text-main">{{ $today->locale('id')->translatedFormat('l, d F Y') }}</h1>
          <p class="mt-2 text-sm text-text-secondary">Masuk pas datang, pulang pas beres. Terdengar sederhana karena memang harusnya begitu.</p>
        </div>
        <span class="status-pill {{ !$in ? 'status-pill-warning' : ($out ? 'status-pill-success' : 'status-pill-primary') }}">
          {{ !$in ? 'Belum mulai' : ($out ? 'Hari ini selesai' : 'Masih aktif') }}
        </span>
      </div>
    </div>

    <div class="quick-grid two">
      <div class="pins-card p-5">
        <div class="section-title">Aksi Cepat</div>
        <div class="section-subtitle mt-1">Tombol utama dikasih prioritas, jadi kamu tidak perlu mikir lima menit cuma buat absen.</div>

        <div class="mt-5 action-stack">
          <form method="POST" action="{{ route('peserta.absensi.masuk') }}">
            @csrf
            <button class="pins-btn-primary" {{ $attendanceToday?->check_in_at ? 'disabled' : '' }}>Absen Datang</button>
          </form>
          <form method="POST" action="{{ route('peserta.absensi.pulang') }}">
            @csrf
            <button class="pins-btn-ghost" {{ (!$attendanceToday?->check_in_at || $attendanceToday?->check_out_at) ? 'disabled' : '' }}>Absen Pulang</button>
          </form>
        </div>

        <div class="mt-5 quick-grid two">
          <div class="task-card">
            <div class="helper-text">Check-in</div>
            <div class="mt-1 text-3xl font-semibold text-text-main">{{ $in?->format('H:i') ?? '--:--' }}</div>
            <div class="helper-text mt-2">IP masuk: {{ $attendanceToday?->check_in_ip ?? '-' }}</div>
          </div>
          <div class="task-card">
            <div class="helper-text">Check-out</div>
            <div class="mt-1 text-3xl font-semibold text-text-main">{{ $out?->format('H:i') ?? '--:--' }}</div>
            <div class="helper-text mt-2">IP pulang: {{ $attendanceToday?->check_out_ip ?? '-' }}</div>
          </div>
        </div>
      </div>

      <div class="pins-card p-5">
        <div class="section-title">Panduan Singkat</div>
        <div class="history-list mt-4">
          <div class="history-item">
            <div class="font-semibold text-text-main">1. Absen datang</div>
            <div class="helper-text mt-1">Lakuin saat benar-benar sampai dan siap kerja.</div>
          </div>
          <div class="history-item">
            <div class="font-semibold text-text-main">2. Kerjain task hari ini</div>
            <div class="helper-text mt-1">Setelah ada kerjaan, isi report task di menu laporan.</div>
          </div>
          <div class="history-item">
            <div class="font-semibold text-text-main">3. Absen pulang</div>
            <div class="helper-text mt-1">Pakai saat pekerjaan selesai. Jangan diborong dari pagi, manusia aneh.</div>
          </div>
        </div>
      </div>
    </div>

    <div class="table-shell">
      <div class="px-5 py-4 border-b border-black/5 bg-white/60">
        <div class="section-title">Riwayat Absensi</div>
        <div class="section-subtitle mt-1">Semua catatan check-in dan check-out kamu.</div>
      </div>

      <div class="overflow-x-auto">
        <table class="table-modern text-sm">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Masuk</th>
              <th>Pulang</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($history as $a)
              <tr>
                <td>
                  <div class="font-semibold text-text-main">{{ $a->date->locale('id')->translatedFormat('d F Y') }}</div>
                  <div class="helper-text mt-1">{{ $a->date->locale('id')->translatedFormat('l') }}</div>
                </td>
                <td class="text-text-main">{{ $a->check_in_at?->format('H:i') ?? '-' }}</td>
                <td class="text-text-main">{{ $a->check_out_at?->format('H:i') ?? '-' }}</td>
                <td>
                  <span class="status-pill {{ $a->check_out_at ? 'status-pill-success' : ($a->check_in_at ? 'status-pill-primary' : 'status-pill-neutral') }}">
                    {{ $a->check_out_at ? 'Lengkap' : ($a->check_in_at ? 'Belum pulang' : 'Kosong') }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4">
                  <div class="empty-state">Belum ada riwayat absensi.</div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div>{{ $history->links() }}</div>
  </div>
@endsection
