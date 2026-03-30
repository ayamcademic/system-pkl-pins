@extends('peserta.layout')

@php
  $pageTitle = 'Report Task';
  $currentContent = old('content', $todayReport?->content);
@endphp

@section('content')
  <div class="space-y-6">
    <div class="hero-card p-6">
      <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
          <div class="eyebrow">Task Report</div>
          <h1 class="mt-2 font-serif text-3xl text-text-main">Laporan Kegiatan {{ $today->locale('id')->translatedFormat('d F Y') }}</h1>
          <p class="mt-2 text-sm text-text-secondary">Tulis apa yang dikerjain, progress, kendala, atau hasil penting. Bukan diary, tapi juga jangan sekering notulen fotokopian.</p>
        </div>
        <span id="charCountBadge" class="status-pill {{ $todayReport ? 'status-pill-success' : 'status-pill-neutral' }}">
          {{ strlen($currentContent ?? '') }} / 5000 karakter
        </span>
      </div>
    </div>

    <div class="quick-grid two">
      <div class="pins-card p-5 form-surface">
        <div class="section-title">Isi Report Task</div>
        <div class="section-subtitle mt-1">Satu form buat bikin atau update laporan hari ini. Manusia suka hal sederhana, jadi ya sudah.</div>

        <form method="POST" action="{{ route('peserta.laporan.store') }}" class="mt-5 space-y-4">
          @csrf
          <textarea
            id="reportContent"
            name="content"
            rows="10"
            class="w-full"
            placeholder="Contoh: Membantu input data peserta baru, cek ulang file excel, follow up revisi dari pembimbing, dan merapikan dokumen arsip."
          >{{ $currentContent }}</textarea>

          <div class="helper-text">Tips cepat: tulis tugas utama, hasil, dan kendala kalau ada.</div>

          <div class="action-stack">
            <button class="pins-btn-primary">Simpan Report Task</button>
            @if($todayReport)
              <span class="status-pill status-pill-success">Terakhir update {{ $todayReport->updated_at?->format('d M Y H:i') }}</span>
            @endif
          </div>
        </form>
      </div>

      <div class="pins-card p-5">
        <div class="section-title">Contoh Format yang Aman</div>
        <div class="history-list mt-4">
          <div class="history-item">
            <div class="font-semibold text-text-main">Task utama</div>
            <div class="helper-text mt-1">Tulis kerjaan intinya. Misalnya input data peserta, rekap absensi, atau bantu administrasi.</div>
          </div>
          <div class="history-item">
            <div class="font-semibold text-text-main">Hasil / progress</div>
            <div class="helper-text mt-1">Jelaskan yang selesai atau yang sudah mendekati selesai.</div>
          </div>
          <div class="history-item">
            <div class="font-semibold text-text-main">Kendala</div>
            <div class="helper-text mt-1">Kalau ada masalah, tulis singkat. Biar pembimbing baca konteks, bukan baca pikiran.</div>
          </div>
        </div>
      </div>
    </div>

    <div class="pins-card p-5">
      <div class="section-title">Riwayat Report Task</div>
      <div class="section-subtitle mt-1">Kalau butuh lihat laporan lama, tinggal cek di sini.</div>

      <div class="history-list mt-4">
        @forelse($history as $r)
          <div class="history-item">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
              <div>
                <div class="font-semibold text-text-main">{{ $r->date->locale('id')->translatedFormat('d F Y') }}</div>
                <div class="helper-text mt-1">Update terakhir {{ $r->updated_at?->format('H:i') }}</div>
              </div>
              <span class="status-pill status-pill-dark">{{ strlen($r->content) }} karakter</span>
            </div>
            <div class="mt-3 text-sm text-text-main whitespace-pre-line">{{ $r->content }}</div>
          </div>
        @empty
          <div class="empty-state">Belum ada laporan yang tersimpan.</div>
        @endforelse
      </div>

      <div class="mt-4">{{ $history->links() }}</div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  (function () {
    const textarea = document.getElementById('reportContent');
    const badge = document.getElementById('charCountBadge');
    if (!textarea || !badge) return;

    const updateCount = () => {
      const total = textarea.value.length;
      badge.textContent = total + ' / 5000 karakter';
    };

    updateCount();
    textarea.addEventListener('input', updateCount);
  })();
</script>
@endpush
