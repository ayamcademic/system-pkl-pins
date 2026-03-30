@extends('pembimbing.layout', ['pageTitle' => $mode === 'create' ? 'Tambah Peserta' : 'Edit Peserta'])

@section('content')
  <div class="pins-card p-6 max-w-3xl">
    <div class="flex flex-col sm:flex-row sm:items-end gap-3 justify-between mb-6">
      <div>
        <h1 class="font-serif text-2xl text-text-main">{{ $mode === 'create' ? 'Tambah Peserta' : 'Edit Peserta' }}</h1>
        <p class="text-sm text-text-secondary">Lengkapi data dengan benar. Field bertanda * wajib.</p>
      </div>
      <a href="{{ route('pembimbing.peserta.index') }}" class="pins-btn-ghost border border-black/10">Kembali</a>
    </div>

    <form method="POST" enctype="multipart/form-data" action="{{ $mode === 'create' ? route('pembimbing.peserta.store') : route('pembimbing.peserta.update', $peserta) }}">
      @csrf
      @if ($mode === 'edit')
        @method('PUT')
      @endif

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-text-main mb-1">Nama *</label>
          <input name="nama" value="{{ old('nama', $peserta->nama) }}" required
                 class="w-full rounded-xl border-black/10 bg-input-bg px-4 py-2.5 text-sm focus:border-primary focus:ring-primary" />
          @error('nama') <div class="text-sm text-primary mt-1">{{ $message }}</div> @enderror
        </div>

  <!--
    <div class="md:col-span-2">
  <label class="block text-sm font-medium text-text-main mb-1">Foto Peserta</label>

 <div class="flex items-center gap-4">
          @if($mode === 'edit' && $peserta->foto_path)
            <img src="{{ asset('storage/'.$peserta->foto_path) }}" 
                class="h-16 w-16 rounded-2xl object-cover border border-black/10">
          @else
            <div class="h-16 w-16 rounded-2xl bg-black/5 border border-black/10 flex items-center justify-center text-text-secondary">
              -
            </div>
          @endif

          <div class="flex flex-col gap-2">
            <input type="file" name="foto" accept="image/*"
              class="block text-sm file:mr-4 file:rounded-xl file:border-0 file:bg-black/5 file:px-3 file:py-2 file:text-sm file:font-semibold hover:file:bg-black/10" />

            @if($mode === 'edit' && $peserta->foto_path)
              <button
                type="submit"
                name="remove_foto"
                value="1"
                  class="pins-btn-ghost border border-black/10 text-primary hover:bg-primary/10 w-fit"
                onclick="return confirm('Hapus foto peserta ini?')"
              >
                Hapus Foto
              </button>
            @endif
    </div>
  </div>

  @error('foto') 
    <div class="text-sm text-primary mt-1">{{ $message }}</div> 
  @enderror
</div> -->

<div class="md:col-span-2">
  <label class="block text-sm font-medium text-text-main mb-1">Foto Peserta</label>

  <div class="flex items-center gap-4">
    @php
      $currentFoto = ($mode === 'edit' && $peserta->foto_path)
        ? asset('storage/'.$peserta->foto_path)
        : null;
    @endphp

    <img
      id="fotoPreview"
      src="{{ $currentFoto ?? '' }}"
      class="h-16 w-16 rounded-2xl object-cover border border-black/10 {{ $currentFoto ? '' : 'hidden' }}"
      alt="Preview Foto"
    >

    <div id="fotoFallback"
      class="h-16 w-16 rounded-2xl bg-black/5 border border-black/10 flex items-center justify-center text-text-secondary {{ $currentFoto ? 'hidden' : '' }}">
      -
    </div>

    <div class="flex flex-col gap-2">
      <input
        id="fotoInput"
        type="file"
        name="foto"
        accept="image/*"
        class="block text-sm file:mr-4 file:rounded-xl file:border-0 file:bg-black/5 file:px-3 file:py-2 file:text-sm file:font-semibold hover:file:bg-black/10"
      />

      <div class="flex gap-2 flex-wrap">
        @if($mode === 'edit' && $peserta->foto_path)
          <button
            type="submit"
            name="remove_foto"
            value="1"
            class="pins-btn-ghost border border-black/10 text-primary hover:bg-primary/10"
            onclick="return confirm('Hapus foto peserta ini?')"
          >
            Hapus Foto
          </button>
        @endif

        <button
          type="button"
          id="clearFotoBtn"
          class="pins-btn-ghost border border-black/10"
          style="display:none"
        >
          Batal Pilih Foto
        </button>
      </div>

      <p class="text-xs text-text-secondary">
        Upload JPG/PNG/WebP (max 2MB). Preview muncul sebelum disimpan.
      </p>
    </div>
  </div>

  @error('foto') <div class="text-sm text-primary mt-1">{{ $message }}</div> @enderror
</div>




        <div>
          <label class="block text-sm font-medium text-text-main mb-1">Asal Sekolah</label>
          <input name="asal_sekolah" value="{{ old('asal_sekolah', $peserta->asal_sekolah) }}"
                 class="w-full rounded-xl border-black/10 bg-input-bg px-4 py-2.5 text-sm focus:border-primary focus:ring-primary" />
          @error('asal_sekolah') <div class="text-sm text-primary mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-text-main mb-1">Kompetensi Keahlian</label>
          <input name="kompetensi_keahlian" value="{{ old('kompetensi_keahlian', $peserta->kompetensi_keahlian) }}"
                 class="w-full rounded-xl border-black/10 bg-input-bg px-4 py-2.5 text-sm focus:border-primary focus:ring-primary" />
        </div>

        <div>
          <label class="block text-sm font-medium text-text-main mb-1">Tanggal Masuk PKL</label>
          <input type="date" name="tgl_masuk_pkl" value="{{ old('tgl_masuk_pkl', optional($peserta->tgl_masuk_pkl)->format('Y-m-d')) }}"
                 class="w-full rounded-xl border-black/10 bg-input-bg px-4 py-2.5 text-sm focus:border-primary focus:ring-primary" />
        </div>


        
        <div>
          <label class="block text-sm font-medium text-text-main mb-1">Tanggal Keluar PKL</label>
          <input type="date" name="tgl_keluar_pkl"
                value="{{ old('tgl_keluar_pkl', optional($peserta->tgl_keluar_pkl)->format('Y-m-d')) }}"
                class="w-full rounded-xl border-black/10 bg-input-bg px-4 py-2.5 text-sm focus:border-primary focus:ring-primary" />
          @error('tgl_keluar_pkl') <div class="text-sm text-primary mt-1">{{ $message }}</div> @enderror
</div>

<div>
          <label class="block text-sm font-medium text-text-main mb-1">Tanggal Masuk PKL 2 (Opsional)</label>
          <input type="date" name="tgl_masuk_pkl_2"
                value="{{ old('tgl_masuk_pkl_2', optional($peserta->tgl_masuk_pkl_2)->format('Y-m-d')) }}"
                class="w-full rounded-xl border-black/10 bg-input-bg px-4 py-2.5 text-sm focus:border-primary focus:ring-primary" />
          @error('tgl_masuk_pkl_2') <div class="text-sm text-primary mt-1">{{ $message }}</div> @enderror
</div>

<div>
          <label class="block text-sm font-medium text-text-main mb-1">Tanggal Keluar PKL 2 (Opsional)</label>
          <input type="date" name="tgl_keluar_pkl_2"
                value="{{ old('tgl_keluar_pkl_2', optional($peserta->tgl_keluar_pkl_2)->format('Y-m-d')) }}"
                class="w-full rounded-xl border-black/10 bg-input-bg px-4 py-2.5 text-sm focus:border-primary focus:ring-primary" />
          @error('tgl_keluar_pkl_2') <div class="text-sm text-primary mt-1">{{ $message }}</div> @enderror
</div>

        
        <!-- <div>
          <label class="block text-sm font-medium text-text-main mb-1">Durasi PKL</label>
          <input name="durasi_pkl" value="{{ old('durasi_pkl', $peserta->durasi_pkl) }}"
                 class="w-full rounded-xl border-black/10 bg-input-bg px-4 py-2.5 text-sm focus:border-primary focus:ring-primary" />
        </div> -->
        <div>
          <label class="block text-sm font-medium text-text-main mb-1">Durasi PKL</label>
          <input
            value="{{ $peserta->durasi_pkl ?? '-' }}"
            readonly
            class="w-full rounded-xl border-black/10 bg-black/5 px-4 py-2.5 text-sm text-text-secondary"
          />
          <p class="text-xs text-text-secondary mt-1">Durasi dihitung dari tanggal masuk/keluar.</p>
        </div>


        <div>
          <label class="block text-sm font-medium text-text-main mb-1">No HP Peserta</label>
          <input name="no_hp" value="{{ old('no_hp', $peserta->no_hp) }}"
                 class="w-full rounded-xl border-black/10 bg-input-bg px-4 py-2.5 text-sm focus:border-primary focus:ring-primary" />
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-text-main mb-1">Alamat</label>
          <textarea name="alamat_rumah" rows="3"
                    class="w-full rounded-xl border-black/10 bg-input-bg px-4 py-2.5 text-sm focus:border-primary focus:ring-primary">{{ old('alamat_rumah', $peserta->alamat_rumah) }}</textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-text-main mb-1">Nama Guru Pembimbing</label>
          <input name="nama_guru_pembimbing" value="{{ old('nama_guru_pembimbing', $peserta->nama_guru_pembimbing) }}"
                 class="w-full rounded-xl border-black/10 bg-input-bg px-4 py-2.5 text-sm focus:border-primary focus:ring-primary" />
        </div>

        <div>
          <label class="block text-sm font-medium text-text-main mb-1">No HP Guru Pembimbing</label>
          <input name="no_hp_guru_pembimbing" value="{{ old('no_hp_guru_pembimbing', $peserta->no_hp_guru_pembimbing) }}"
                 class="w-full rounded-xl border-black/10 bg-input-bg px-4 py-2.5 text-sm focus:border-primary focus:ring-primary" />
        </div>
      </div>

      <div class="mt-6 flex flex-wrap gap-2">
        <button class="pins-btn-primary">Simpan</button>
        <a href="{{ route('pembimbing.peserta.index') }}" class="pins-btn-ghost border border-black/10">Batal</a>
      </div>
    </form>
  </div>



  @push('scripts')
<script>
  (function () {
    const input = document.getElementById('fotoInput');
    const img = document.getElementById('fotoPreview');
    const fallback = document.getElementById('fotoFallback');
    const clearBtn = document.getElementById('clearFotoBtn');

    if (!input) return;

    const showPreview = (src) => {
      img.src = src;
      img.classList.remove('hidden');
      fallback.classList.add('hidden');
      clearBtn.style.display = 'inline-flex';
    };

    const clearPreview = () => {
      input.value = '';
      img.src = '';
      img.classList.add('hidden');
      fallback.classList.remove('hidden');
      clearBtn.style.display = 'none';
    };

    input.addEventListener('change', function () {
      const file = this.files && this.files[0];
      if (!file) return clearPreview();
      const url = URL.createObjectURL(file);
      showPreview(url);
    });

    clearBtn.addEventListener('click', function () {
      clearPreview();
    });
  })();
</script>
@endpush

@endsection
