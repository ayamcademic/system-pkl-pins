@extends('peserta.layout')

@php
  $pageTitle = 'Rekap Mingguan';

  $totalMasuk = 0;
  $totalPulang = 0;
  $totalLaporan = 0;
  $bolong = 0;

  foreach($days as $row){
    $a = $row['attendance'];
    $r = $row['report'];

    if ($a?->check_in_at) $totalMasuk++;
    if ($a?->check_out_at) $totalPulang++;
    if ($r) $totalLaporan++;
    if (!$a?->check_in_at && !$r) $bolong++;
  }
@endphp

@section('content')
  <div class="space-y-6">
    <div class="hero-card p-6">
      <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
          <div class="eyebrow">Rekap Mingguan</div>
          <h1 class="mt-2 font-serif text-3xl text-text-main">{{ $start->locale('id')->translatedFormat('d F Y') }} - {{ $end->locale('id')->translatedFormat('d F Y') }}</h1>
          <p class="mt-2 text-sm text-text-secondary">Ringkasan absensi dan report task per hari dalam satu minggu kerja.</p>
        </div>
        <div class="action-stack">
          <a href="{{ route('peserta.rekap', ['week' => $prevWeek]) }}" class="pins-btn-soft">Minggu lalu</a>
          <a href="{{ route('peserta.rekap', ['week' => $nextWeek]) }}" class="pins-btn-soft">Minggu depan</a>
        </div>
      </div>
    </div>

    <div class="quick-grid four">
      <div class="mini-stat">
        <div class="mini-stat-label">Hari hadir</div>
        <div class="mini-stat-value mt-2">{{ $totalMasuk }}</div>
      </div>
      <div class="mini-stat">
        <div class="mini-stat-label">Absen pulang lengkap</div>
        <div class="mini-stat-value mt-2">{{ $totalPulang }}</div>
      </div>
      <div class="mini-stat">
        <div class="mini-stat-label">Task report masuk</div>
        <div class="mini-stat-value mt-2">{{ $totalLaporan }}</div>
      </div>
      <div class="mini-stat">
        <div class="mini-stat-label">Hari kosong</div>
        <div class="mini-stat-value mt-2">{{ $bolong }}</div>
      </div>
    </div>

    <div class="pins-card p-5">
      <div class="section-title">Ringkasan per Hari</div>
      <div class="section-subtitle mt-1">Lihat cepat mana hari yang lengkap, mana yang bolong.</div>

      <div class="history-list mt-4">
        @foreach($days as $row)
          @php
            $d = $row['date'];
            $a = $row['attendance'];
            $r = $row['report'];

            $masuk = $a?->check_in_at?->format('H:i');
            $pulang = $a?->check_out_at?->format('H:i');
            $hasReport = (bool) $r;
            $isEmpty = !$masuk && !$hasReport && !$pulang;
          @endphp

          <div class="history-item">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
              <div>
                <div class="font-semibold text-text-main">{{ $d->locale('id')->translatedFormat('l, d M Y') }}</div>
                <div class="helper-text mt-1">Status harian peserta</div>
              </div>

              <div class="action-stack">
                <span class="status-pill {{ $masuk ? 'status-pill-success' : 'status-pill-neutral' }}">Masuk: {{ $masuk ?? '-' }}</span>
                <span class="status-pill {{ $pulang ? 'status-pill-primary' : 'status-pill-neutral' }}">Pulang: {{ $pulang ?? '-' }}</span>
                <span class="status-pill {{ $hasReport ? 'status-pill-warning' : 'status-pill-neutral' }}">Report: {{ $hasReport ? 'Ada' : '-' }}</span>
                @if($isEmpty)
                  <span class="status-pill status-pill-danger">Kosong</span>
                @endif
              </div>
            </div>

            @if($r)
              <details class="mt-4">
                <summary class="cursor-pointer text-sm font-semibold text-primary">Lihat isi task report</summary>
                <div class="mt-3 text-sm text-text-main whitespace-pre-line">{{ $r->content }}</div>
              </details>
            @endif
          </div>
        @endforeach
      </div>
    </div>

    <div class="notice-box text-sm">
      Rekap mingguan ini otomatis narik data dari absensi dan task report harian. Kalau ada hari kosong, isi dari menu report task atau pastikan absennya ke-submit dengan benar.
    </div>
  </div>
@endsection
