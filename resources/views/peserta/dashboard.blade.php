@extends('peserta.layout')

@php
  $pageTitle = 'Dashboard';

  $in = $attendanceToday?->check_in_at;
  $out = $attendanceToday?->check_out_at;
  $reportFilled = (bool) $reportToday;

  $hadirCount = $recentAttendances->filter(fn ($item) => $item->check_in_at)->count();
  $laporanCount = $recentReports->count();
  $pulangCount = $recentAttendances->filter(fn ($item) => $item->check_out_at)->count();
@endphp

@section('content')
  <div class="space-y-6">
    <div class="hero-card p-6">
      <div class="flex flex-col lg:flex-row gap-5 lg:items-end lg:justify-between">
        <div>
          <div class="eyebrow">Dashboard Harian</div>
          <h1 class="mt-2 font-serif text-4xl text-text-main">{{ $greeting }}, {{ explode(' ', $userName)[0] }}.</h1>
          <p class="mt-2 text-sm text-text-secondary intro-copy">
          <p class="text-2xl  text-text-secondary">{{ $today->locale('id')->translatedFormat('l, d F Y') }} </p>
<p class="text-2xl">
  <br>
    @if (!$attendanceToday?->check_in_at)
        Kamu belum absen masuk hari ini.
    @elseif ($attendanceToday?->check_in_at && !$reportToday)
        Absen sudah aman, tinggal isi task report.
    @else
        Absen dan report hari ini sudah lengkap.
    @endif
</b>
<br>
<br>
        </div>

        <div class="flex flex-wrap gap-2">
          @if(!$in)
            <span class="status-pill status-pill-warning">Belum absen masuk</span>
          @elseif($in && !$out)
            <span class="status-pill status-pill-primary">Sudah masuk, belum pulang</span>
          @else
            <span class="status-pill status-pill-success">Absensi hari ini lengkap</span>
          @endif

          <span class="status-pill {{ $reportFilled ? 'status-pill-success' : 'status-pill-neutral' }}">
            {{ $reportFilled ? 'Report task sudah diisi' : 'Report task belum diisi' }}
          </span>
        </div>
      </div>
    </div>

    <div class="quick-grid four">
      <div class="mini-stat">
        <div class="mini-stat-label">Absen masuk hari ini</div>
        <div class="mini-stat-value mt-2">{{ $in?->format('H:i') ?? '--:--' }}</div>
        <div class="helper-text mt-2">Klik sekali aja. Sistem bukan tombol lift.</div>
      </div>
      <div class="mini-stat">
        <div class="mini-stat-label">Absen pulang hari ini</div>
        <div class="mini-stat-value mt-2">{{ $out?->format('H:i') ?? '--:--' }}</div>
        <div class="helper-text mt-2">Isi pas kerjaan hari ini beneran selesai.</div>
      </div>
      <div class="mini-stat">
        <div class="mini-stat-label">Absensi 7 hari terakhir</div>
        <div class="mini-stat-value mt-2">{{ $hadirCount }}</div>
        <div class="helper-text mt-2">Hari dengan check-in tercatat.</div>
      </div>
      <div class="mini-stat">
        <div class="mini-stat-label">Task report 7 hari terakhir</div>
        <div class="mini-stat-value mt-2">{{ $laporanCount }}</div>
        <div class="helper-text mt-2">Laporan yang sudah kamu isi.</div>
      </div>
    </div>

    <div class="quick-grid two">
      <div class="pins-card p-5">
        <div class="flex items-start justify-between gap-4">
          <div>
            <div class="section-title">Absensi Hari Ini</div>
            <div class="section-subtitle mt-1">Tombol dibikin jelas biar tidak ada drama salah klik. Luar biasa, ya.</div>
          </div>
          <span class="status-pill {{ !$in ? 'status-pill-warning' : ($out ? 'status-pill-success' : 'status-pill-primary') }}">
            {{ !$in ? 'Menunggu masuk' : ($out ? 'Lengkap' : 'Sedang berjalan') }}
          </span>
        </div>

        <div class="mt-5 action-stack">
          <form method="POST" action="{{ route('peserta.absensi.masuk') }}">
            @csrf
            <button class="pins-btn-primary" {{ $attendanceToday?->check_in_at ? 'disabled' : '' }}>Absen Datang</button>
          </form>

          <form method="POST" action="{{ route('peserta.absensi.pulang') }}">
            @csrf
            <button class="pins-btn-ghost" {{ (!$attendanceToday?->check_in_at || $attendanceToday?->check_out_at) ? 'disabled' : '' }}>Absen Pulang</button>
          </form>

          <a href="{{ route('peserta.absensi') }}" class="pins-btn-soft">Lihat Riwayat</a>
        </div>

        <div class="mt-5 quick-grid two">
          <div class="task-card">
            <div class="helper-text">Jam datang</div>
            <div class="mt-1 text-2xl font-semibold text-text-main">{{ $in?->format('H:i') ?? '--:--' }}</div>
            <div class="helper-text mt-2">IP: {{ $attendanceToday?->check_in_ip ?? '-' }}</div>
          </div>
          <div class="task-card">
            <div class="helper-text">Jam pulang</div>
            <div class="mt-1 text-2xl font-semibold text-text-main">{{ $out?->format('H:i') ?? '--:--' }}</div>
            <div class="helper-text mt-2">IP: {{ $attendanceToday?->check_out_ip ?? '-' }}</div>
          </div>
        </div>
      </div>

      <div class="pins-card p-5">
        <div class="flex items-start justify-between gap-4">
          <div>
            <div class="section-title">Task Report Hari Ini</div>
            <div class="section-subtitle mt-1">Tulis tugas, progress, atau hal penting yang kamu kerjain. Jangan kasih laporan level "ngerjain sesuatu" doang.</div>
          </div>
          <span class="status-pill {{ $reportFilled ? 'status-pill-success' : 'status-pill-neutral' }}">
            {{ $reportFilled ? 'Sudah terisi' : 'Belum terisi' }}
          </span>
        </div>

        <div class="mt-5 task-card">
          @if($reportToday)
            <div class="text-sm text-text-main whitespace-pre-line">{{ $reportToday->content }}</div>
            <div class="helper-text mt-3">Terakhir update {{ $reportToday->updated_at?->format('d M Y H:i') }}</div>
          @else
            <div class="empty-state">
              Belum ada task report hari ini. Isi setelah kerjaan mulai jalan, biar pembimbing tidak mengandalkan telepati.
            </div>
          @endif
        </div>

        <div class="mt-5 action-stack">
          <a href="{{ route('peserta.laporan') }}" class="pins-btn-primary">Isi / Edit Report Task</a>
          <a href="{{ route('peserta.rekap') }}" class="pins-btn-soft">Lihat Rekap Mingguan</a>
        </div>
      </div>
    </div>

    <div class="quick-grid two">
      <div class="pins-card p-5">
        <div class="section-title">Riwayat Absensi Terbaru</div>
        <div class="section-subtitle mt-1">Tujuh entri terakhir. Cukup buat lihat pola hidupmu di kantor.</div>

        <div class="history-list mt-4">
          @forelse($recentAttendances as $a)
            <div class="history-item">
              <div class="flex items-center justify-between gap-3">
                <div>
                  <div class="font-semibold text-text-main">{{ $a->date->locale('id')->translatedFormat('d M Y') }}</div>
                  <div class="helper-text mt-1">Masuk {{ $a->check_in_at?->format('H:i') ?? '--:--' }} · Pulang {{ $a->check_out_at?->format('H:i') ?? '--:--' }}</div>
                </div>
                <span class="status-pill {{ $a->check_out_at ? 'status-pill-success' : 'status-pill-neutral' }}">
                  {{ $a->check_out_at ? 'Lengkap' : 'Belum lengkap' }}
                </span>
              </div>
            </div>
          @empty
            <div class="empty-state">Belum ada data absensi.</div>
          @endforelse
        </div>
      </div>

      <div class="pins-card p-5">
        <div class="section-title">Riwayat Task Report</div>
        <div class="section-subtitle mt-1">Supaya kamu bisa cek lagi apa aja yang udah ditulis, kalau lupa karena otak dipakai multitasking.</div>

        <div class="history-list mt-4">
          @forelse($recentReports as $r)
            <div class="history-item">
              <div class="flex items-center justify-between gap-3">
                <div class="font-semibold text-text-main">{{ $r->date->locale('id')->translatedFormat('d M Y') }}</div>
                <span class="status-pill status-pill-dark">{{ strlen($r->content) }} karakter</span>
              </div>
              <div class="mt-3 text-sm text-text-main whitespace-pre-line">{{ \Illuminate\Support\Str::limit($r->content, 180) }}</div>
            </div>
          @empty
            <div class="empty-state">Belum ada task report yang tersimpan.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
@endsection
