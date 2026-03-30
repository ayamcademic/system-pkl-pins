@extends('pembimbing.layout', ['pageTitle' => 'Data Peserta PKL'])

@section('content')
  <div class="space-y-6">
    <div class="hero-card p-6 form-surface">
      <div class="flex flex-col xl:flex-row gap-5 xl:items-end justify-between">
        <div>
          <div class="eyebrow">Data Peserta</div>
          <h1 class="mt-2 font-serif text-3xl text-text-main">Kelola Peserta PKL</h1>
          <p class="mt-2 text-sm text-text-secondary intro-copy">Bisa Tambah Peserta secara manual atau menggunakan Import Excel yang sudah menggunakan logika UpSert(Update if exsist, Insert if new).
          </p>
        </div>

        <div class="action-stack">
          <a href="{{ route('pembimbing.peserta.create') }}" class="pins-btn-primary">Tambah Peserta</a>
          <a href="{{ route('pembimbing.peserta.export.pdf') }}" target="_blank" rel="noopener" class="pins-btn-soft">Export PDF</a>
          <a href="{{ route('pembimbing.excel.export') }}" target="_blank" rel="noopener" class="pins-btn-soft">Export Excel</a>
        </div>
      </div>

      <div class="quick-grid two mt-5">
        <form method="GET" class="task-card flex flex-col gap-3">
          <div>
            <div class="section-title">Cari Peserta</div>
            <div class="helper-text mt-1">Cari nama, sekolah, atau kompetensi.</div>
          </div>

          <div class="flex flex-col sm:flex-row gap-2">
            <input
              type="text"
              name="search"
              value="{{ $search }}"
              placeholder="Cari nama / sekolah / kompetensi..."
              class="flex-1 px-4 py-3 text-sm"
            >
            <button class="pins-btn-primary">Cari</button>
          </div>
        </form>

        <form method="POST" action="{{ route('pembimbing.excel.import') }}" enctype="multipart/form-data" class="task-card flex flex-col gap-3">
          @csrf
          <div>
            <div class="section-title">Import Excel</div>
            <div class="helper-text mt-1">Upload file xlsx atau xls buat tambah atau update data.</div>
          </div>

          <div class="flex flex-col sm:flex-row gap-2 items-start sm:items-center">
            <input
              type="file"
              name="file"
              class="block w-full text-sm file:mr-4 file:rounded-xl file:border-0 file:bg-black/5 file:px-3 file:py-2 file:text-sm file:font-semibold hover:file:bg-black/10"
              required
            >
            <button class="pins-btn-soft">Import</button>
          </div>
        </form>
      </div>
    </div>

    <div class="pins-card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-black/2 border-b border-black/5">
            <tr class="text-left text-text-secondary">
              <th class="px-5 py-3 font-semibold">Profil</th>
              <th class="px-5 py-3 font-semibold">Nama</th>
              <th class="px-5 py-3 font-semibold">Asal Sekolah</th>
              <th class="px-5 py-3 font-semibold">Kompetensi</th>
              <th class="px-5 py-3 font-semibold">Durasi</th>
              <th class="px-5 py-3 font-semibold">Status</th>
              <th class="px-5 py-3 font-semibold">No HP</th>
              <th class="px-5 py-3 font-semibold">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-black/5">
            @forelse ($peserta as $p)
              @php
                $foto = $p->foto_path ?: $p->foto_peserta_pkl;
                $status = $p->status_pkl;

                $initials = collect(preg_split('/\s+/', trim($p->nama ?? 'P')))
                  ->filter()
                  ->map(fn($word) => strtoupper(mb_substr($word, 0, 1)))
                  ->take(2)
                  ->implode('');
              @endphp

              <tr class="hover:bg-black/2">
                <td class="px-5 py-4">
                  @if (!empty($foto))
                    <img
                      src="{{ asset('storage/' . $foto) }}"
                      alt="Foto {{ $p->nama }}"
                      class="h-10 w-10 rounded-full object-cover border border-black/10"
                      onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    >
                    <div
                      style="display:none"
                      class="h-10 w-10 items-center justify-center rounded-full bg-red-600 text-white font-semibold"
                    >
                      {{ $initials }}
                    </div>
                  @else
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-600 text-white font-semibold">
                      {{ $initials }}
                    </div>
                  @endif
                </td>

                <td class="px-5 py-4">
                  <a class="font-semibold text-text-main hover:underline" href="{{ route('pembimbing.peserta.show', $p) }}">
                    {{ $p->nama }}
                  </a>

                  <div class="text-xs text-text-secondary mt-0.5">
                    Masuk:
                    <span class="font-medium text-text-main">
                      {{ $p->tgl_masuk_pkl?->translatedFormat('d F Y') ?? '-' }}
                    </span>
                  </div>
                </td>

                <td class="px-5 py-4 text-text-main">
                  {{ $p->asal_sekolah ?: '-' }}
                </td>

                <td class="px-5 py-4">
                  <span class="inline-flex items-center rounded-full bg-pastel-beige/70 px-3 py-1 text-xs font-medium text-text-main border border-black/5">
                    {{ $p->kompetensi_keahlian ?: '-' }}
                  </span>
                </td>

                <td class="px-5 py-4 text-text-main">
                  {{ $p->durasi_pkl ?: '-' }}
                </td>

                <td class="px-5 py-4">
                  @if($status === 'ACTIVE')
                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                      ACTIVE
                    </span>
                  @elseif($status === 'ARCHIVE')
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 border border-slate-200">
                      ARCHIVE
                    </span>
                  @else
                    <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 border border-amber-200">
                      UNKNOWN
                    </span>
                  @endif
                </td>

                <td class="px-5 py-4 text-text-main">
                  {{ $p->no_hp ?: '-' }}
                </td>

                <td class="px-5 py-4">
                  <div class="flex flex-col items-end gap-2">
                    @if(is_null($p->user_id) && $p->status_pkl === 'ACTIVE')
                      <form method="POST" action="{{ route('pembimbing.peserta.buatAkun', $p) }}">
                        @csrf
                        <button
                          type="submit"
                          class="rounded-lg bg-red-600 px-3 py-1 text-xs font-semibold text-white hover:bg-red-700 transition"
                        >
                          Buat Akun
                        </button>
                      </form>
                    @endif

                    @if($p->user)
                      <div class="flex flex-col items-end gap-1">
                        <span class="text-xs font-medium text-blue-600">
                          {{ $p->user->email }}
                        </span>

                        <form
                          method="POST"
                          action="{{ route('pembimbing.peserta.resetPassword', $p) }}"
                          onsubmit="return confirm('Reset password untuk {{ $p->user->email }} ?')"
                        >
                          @csrf
                          <button
                            type="submit"
                            class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 hover:bg-blue-100 transition"
                          >
                            Reset PW
                          </button>
                        </form>
                      </div>
                    @endif

                    <div class="flex gap-2">
                      <a
                        href="{{ route('pembimbing.peserta.edit', $p) }}"
                        class="rounded-lg border border-gray-200 px-3 py-1 text-xs hover:bg-gray-50 transition"
                      >
                        Edit
                      </a>

                      <form
                        method="POST"
                        action="{{ route('pembimbing.peserta.destroy', $p) }}"
                        onsubmit="return confirm('Hapus peserta ini?')"
                      >
                        @csrf
                        @method('DELETE')
                        <button
                          class="rounded-lg border border-gray-200 px-3 py-1 text-xs text-red-600 hover:bg-red-50 transition"
                        >
                          Hapus
                        </button>
                      </form>
                    </div>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="px-6 py-10 text-center text-text-secondary">
                  Belum ada data peserta.
                  <a class="text-primary font-semibold hover:underline" href="{{ route('pembimbing.peserta.create') }}">
                    Tambah Peserta
                  </a>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-5">
      {{ $peserta->links() }}
    </div>
  </div>
@endsection