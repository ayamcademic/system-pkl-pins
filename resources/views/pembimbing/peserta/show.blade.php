@extends('pembimbing.layout', ['pageTitle' => 'Detail Peserta'])

@section('content')
  <div class="space-y-6">
    <div class="hero-card p-6">
      <div class="flex flex-col lg:flex-row lg:items-center gap-5">
        <div class="flex items-center gap-4">
          @if($peserta->foto_path)
            <img src="{{ asset('storage/'.$peserta->foto_path) }}" alt="Foto {{ $peserta->nama }}" class="h-20 w-20 rounded-3xl object-cover border border-black/10">
          @else
            <div class="profile-dot" style="width:5rem;height:5rem;font-size:1.25rem;">{{ $peserta->initials }}</div>
          @endif
          <div>
            <div class="font-serif text-3xl text-text-main">{{ $peserta->nama }}</div>
            <div class="text-sm text-text-secondary mt-1">{{ $peserta->asal_sekolah ?: '-' }} · {{ $peserta->kompetensi_keahlian ?: '-' }}</div>
            <div class="action-stack mt-3">
              <span class="status-pill {{ $peserta->status_pkl === 'ACTIVE' ? 'status-pill-success' : 'status-pill-neutral' }}">{{ $peserta->status_pkl }}</span>
              <span class="account-pill">Durasi {{ $peserta->durasi_pkl ?: '-' }}</span>
            </div>
          </div>
        </div>

        <div class="lg:ml-auto action-stack">
          <a href="{{ route('pembimbing.peserta.edit', $peserta) }}" class="pins-btn-primary">Edit Peserta</a>
          <a href="{{ route('pembimbing.peserta.index') }}" class="pins-btn-soft">Kembali</a>
        </div>
      </div>
    </div>

    <div class="quick-grid two">
      <div class="pins-card p-5">
        <div class="section-title">Informasi Peserta</div>
        <div class="history-list mt-4">
          <div class="history-item"><div class="helper-text">No HP</div><div class="mt-1 text-text-main">{{ $peserta->no_hp ?: '-' }}</div></div>
          <div class="history-item"><div class="helper-text">Alamat</div><div class="mt-1 text-text-main">{{ $peserta->alamat_rumah ?: '-' }}</div></div>
          <div class="history-item"><div class="helper-text">Akun</div><div class="mt-1 text-text-main">{{ $peserta->user?->email ?: 'Belum punya akun' }}</div></div>
        </div>
      </div>

      <div class="pins-card p-5">
        <div class="section-title">Informasi PKL</div>
        <div class="history-list mt-4">
          <div class="history-item"><div class="helper-text">Tanggal Masuk</div><div class="mt-1 text-text-main">{{ optional($peserta->tgl_masuk_pkl)->translatedFormat('d F Y') ?: '-' }}</div></div>
          <div class="history-item"><div class="helper-text">Tanggal Keluar</div><div class="mt-1 text-text-main">{{ optional($peserta->tgl_keluar_pkl)->translatedFormat('d F Y') ?: '-' }}</div></div>
          <div class="history-item"><div class="helper-text">Durasi</div><div class="mt-1 text-text-main">{{ $peserta->durasi_pkl ?: '-' }}</div></div>
        </div>
      </div>

      <div class="pins-card p-5 md:col-span-2">
        <div class="section-title">Pembimbing Sekolah</div>
        <div class="quick-grid two mt-4">
          <div class="history-item">
            <div class="helper-text">Nama Guru Pembimbing</div>
            <div class="mt-1 text-text-main">{{ $peserta->nama_guru_pembimbing ?: '-' }}</div>
          </div>
          <div class="history-item">
            <div class="helper-text">No HP Guru Pembimbing</div>
            <div class="mt-1 text-text-main">{{ $peserta->no_hp_guru_pembimbing ?: '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
