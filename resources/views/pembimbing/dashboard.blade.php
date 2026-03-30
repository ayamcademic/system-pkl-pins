@extends('pembimbing.layout', ['pageTitle' => 'Dashboard'])

@php
  $chartKompLabels = $kompetensiStats->pluck('kompetensi_keahlian')->map(fn($x) => $x ?: '(Kosong)')->values();
  $chartKompValues = $kompetensiStats->pluck('total')->values();

  $chartSekolahLabels = $sekolahTop->pluck('asal_sekolah')->map(fn($x) => $x ?: '(Kosong)')->values();
  $chartSekolahValues = $sekolahTop->pluck('total')->values();

  $chartTahunLabels = $masukPerTahun->pluck('y')->values();
  $chartTahunValues = $masukPerTahun->pluck('total')->values();
@endphp

@section('content')
  <div class="space-y-6">
    <div class="hero-card p-6">
      <div class="flex flex-col xl:flex-row xl:items-end gap-5 justify-between">
        <div>
          <div class="eyebrow">Dashboard Pembimbing</div>
<!-- <h1>{{ $greeting }}, {{ explode(' ', $userName)[0] }}.</h1>
<p>{{ $greetingMessage }}</p> -->
         <h1 class="mt-2 font-serif text-2xl text-text-main">{{ $greeting }}, {{ explode(' ', $userName)[0] }}.</h1>
          <h1 class="mt-2 font-serif text-3xl text-text-main">Ringkasan Peserta PKL</h1>
          <!-- <p class="mt-2 text-sm text-text-secondary intro-copy">Filter, statistik, dan monitoring aktivitas peserta sekarang ngumpul di satu tempat. Birokrasi tetap birokrasi, tapi minimal tampilannya sudah tidak bikin emosi.</p> -->
        </div>

        <form class="form-surface flex flex-wrap gap-3 items-end" method="GET">
          <div>
            <label class="block text-xs font-medium text-text-secondary mb-1">Tahun</label>
            <input name="tahun" value="{{ $tahun }}" class="w-28 px-3 py-2 text-sm" placeholder="2026" />
          </div>

          <div>
            <label class="block text-xs font-medium text-text-secondary mb-1">Asal Sekolah</label>
            <select name="asal_sekolah" class="w-60 px-3 py-2 text-sm">
              <option value="">Semua SEKOLAH</option>
              @foreach ($filterSekolah as $s)
                <option value="{{ $s }}" @selected($asalSekolah==$s)>{{ $s }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-xs font-medium text-text-secondary mb-1">Kompetensi</label>
            <select name="kompetensi" class="w-60 px-3 py-2 text-sm">
              <option value="">Semua KOMPETENSI</option>
              @foreach ($filterKompetensi as $k)
                <option value="{{ $k }}" @selected($kompetensi==$k)>{{ $k }}</option>
              @endforeach
            </select>
          </div>

          <button class="pins-btn-primary">Terapkan</button>
          <a href="{{ route('pembimbing.dashboard') }}" class="pins-btn-soft">Reset</a>
        </form>
      </div>
    </div>

    <div class="quick-grid four">
      <div class="mini-stat">
        <div class="mini-stat-label">Total peserta</div>
        <div class="mini-stat-value mt-2">{{ $totalPeserta }}</div>
        <div class="helper-text mt-2">Semua data peserta PKL terdata di database</div>
      </div>
      <div class="mini-stat">
        <div class="mini-stat-label">Peserta aktif</div>
        <div class="mini-stat-value mt-2">{{ $activeCount }}</div>
        <div class="helper-text mt-2">Sedang PKL</div>
      </div>
      <div class="mini-stat">
        <div class="mini-stat-label">Peserta archive</div>
        <div class="mini-stat-value mt-2">{{ $archiveCount }}</div>
        <div class="helper-text mt-2">Selesai periode PKL.</div>
      </div>
      <div class="mini-stat">
        <div class="mini-stat-label">Variasi kompetensi</div>
        <div class="mini-stat-value mt-2">{{ $jumlahKompetensi }}</div>
        <div class="helper-text mt-2">Bidang keahlian yang ada</div>
      </div>
    </div>

    <div class="quick-grid two">
      <div class="pins-card p-5">
        <div class="flex items-center justify-between mb-3">
          <div>
            <div class="section-title">Variasi Kompetensi</div>
            <div class="section-subtitle mt-1">Jurusan-jurusan yang paling sering mampir</div>
          </div>
          <span class="status-pill status-pill-neutral">Bar chart</span>
        </div>
        <div class="chart-frame">
          <canvas id="chartKompetensi" height="150"></canvas>
        </div>
      </div>

      <div class="pins-card p-5">
        <div class="flex items-center justify-between mb-3">
          <div>
            <div class="section-title">Top Asal Sekolah</div>
            <div class="section-subtitle mt-1">Sekolah yang paling banyak kirim anak magang.</div>
          </div>
          <span class="status-pill status-pill-neutral">Bar chart</span>
        </div>
        <div class="chart-frame">
          <canvas id="chartSekolah" height="150"></canvas>
        </div>
      </div>
    </div>

    <div class="pins-card p-5">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-3">
        <div>
          <div class="section-title">Peserta Masuk per Tahun</div>
          <div class="section-subtitle mt-1">Banyak peserta PKL dari tahun ke tahun.</div>
        </div>
        <a class="pins-btn-primary" href="{{ route('pembimbing.peserta.create') }}">Tambah Peserta Baru</a>
      </div>
      <div class="chart-frame">
        <canvas id="chartTahun" height="90"></canvas>
      </div>
    </div>

    <div class="pins-card p-5 form-surface">
      <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
        <div>

          <div class="section-title">Monitoring Aktivitas Harian Peserta</div>
          <div class="section-subtitle mt-1">Pantau siapa yang sudah absen dan siapa yang sudah isi report task!</div>
        </div>

        <form method="GET" class="flex flex-wrap items-end gap-3">
          <input type="hidden" name="tahun" value="{{ $tahun }}">
          <input type="hidden" name="asal_sekolah" value="{{ $asalSekolah }}">
          <input type="hidden" name="kompetensi" value="{{ $kompetensi }}">
          <div>
            <label class="block text-xs font-medium text-text-secondary mb-1">Tanggal monitoring</label>
            <input type="date" name="date" value="{{ $date }}" class="px-3 py-2 text-sm">
          </div>
          <!-- <button class="pins-btn-soft">Lihat Aktivitas</button> -->
        </form>
      </div>

      <div class="table-shell mt-5">
        <div class="overflow-x-auto">
          <table class="table-modern text-sm">
            <thead>
              <tr>
                <th>Peserta</th>
                <th>Akun</th>
                <th>Absensi</th>
                <th>Task Report</th>
              </tr>
            </thead>
            <tbody>
              @forelse($rows as $row)
                @php
                  $peserta = $row['peserta'];
                  $report = $row['laporan'];
                  $jamMasuk = $row['jam_masuk'];
                  $jamPulang = $row['jam_pulang'];
                @endphp
                <tr>
                  <td>
                    <div class="name-meta">
                      <strong>{{ $peserta->nama }}</strong>
                      <span class="sub-copy">{{ $peserta->asal_sekolah ?: '-' }} · {{ $peserta->kompetensi_keahlian ?: '-' }}</span>
                    </div>
                  </td>
                  <td>
                    <span class="account-pill">{{ $row['email'] ?: 'Belum punya akun' }}</span>
                  </td>
                  <td>
                    <div class="action-stack">
                      <span class="status-pill {{ $jamMasuk ? 'status-pill-success' : 'status-pill-neutral' }}">Masuk: {{ $jamMasuk ?: '-' }}</span>
                      <span class="status-pill {{ $jamPulang ? 'status-pill-primary' : 'status-pill-neutral' }}">Pulang: {{ $jamPulang ?: '-' }}</span>
                    </div>
                  </td>
                  <td>
                    @if($report)
                      <div class="history-item history-item-compact">
                        <div class="status-pill status-pill-warning inline-flex mb-2">Sudah isi report</div>
                        <div class="text-sm text-text-main whitespace-pre-line">{{ \Illuminate\Support\Str::limit($report, 150) }}</div>
                      </div>
                    @else
                      <span class="status-pill status-pill-neutral">Belum ada report task</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4">
                    <div class="empty-state">Belum ada peserta aktif yang punya akun untuk dimonitor.</div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script id="chart-data" type="application/json">
{!! json_encode([
  'kompLabels' => $chartKompLabels,
  'kompValues' => $chartKompValues,
  'sekolahLabels' => $chartSekolahLabels,
  'sekolahValues' => $chartSekolahValues,
  'tahunLabels' => $chartTahunLabels,
  'tahunValues' => $chartTahunValues,
], JSON_UNESCAPED_UNICODE) !!}
</script>

<script>
  const data = JSON.parse(document.getElementById('chart-data').textContent);

  const base = {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { display: false } },
      y: { grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { precision: 0 } },
    }
  };

  new Chart(document.getElementById('chartKompetensi'), {
    type: 'bar',
    data: { labels: data.kompLabels, datasets: [{ label: 'Jumlah', data: data.kompValues, backgroundColor: 'rgba(182,31,36,0.75)', borderRadius: 8 }] },
    options: base
  });

  new Chart(document.getElementById('chartSekolah'), {
    type: 'bar',
    data: { labels: data.sekolahLabels, datasets: [{ label: 'Jumlah', data: data.sekolahValues, backgroundColor: 'rgba(17,17,17,0.72)', borderRadius: 8 }] },
    options: base
  });

  new Chart(document.getElementById('chartTahun'), {
    type: 'line',
    data: {
      labels: data.tahunLabels,
      datasets: [{
        label: 'Jumlah',
        data: data.tahunValues,
        tension: 0.3,
        borderColor: 'rgba(182,31,36,0.85)',
        backgroundColor: 'rgba(182,31,36,0.12)',
        fill: true
      }]
    },
    options: base
  });
</script>
@endpush
