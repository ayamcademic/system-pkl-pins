@extends('pembimbing.layout', ['pageTitle' => 'Excel'])

@section('content')
  <div class="pins-card p-6">
    <div class="flex flex-col sm:flex-row sm:items-end gap-3 justify-between">
      <div>
        <h1 class="font-serif text-2xl text-text-main">Import/Export Excel</h1>
        <p class="text-sm text-text-secondary">Kelola data peserta PKL lewat file Excel.</p>
      </div>

      <a href="{{ route('pembimbing.excel.export') }}" class="pins-btn-ghost border border-black/10">Export Excel</a>
    </div>

    @if (session('success'))
      <div class="mt-4 rounded-2xl border border-black/10 bg-pastel-beige/60 p-4 text-sm text-text-main">
        ✅ {{ session('success') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="mt-4 rounded-2xl border border-black/10 bg-primary/10 p-4 text-sm text-text-main">
        ❌ {{ $errors->first() }}
      </div>
    @endif

    <div class="mt-6 rounded-2xl border border-black/5 bg-background-light p-5">
      <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div>
          <h2 class="text-base font-semibold text-text-main">Import Excel</h2>
          <p class="mt-1 text-sm text-text-secondary">
            Upload file <span class="font-medium">.xlsx</span> (Sheet: <span class="font-medium">Sheet1</span>). Header dibaca otomatis.
          </p>
        </div>

        <div class="text-xs text-text-secondary">
          <div class="font-medium text-text-main">Kolom wajib:</div>
          <div>nama</div>
          <div class="mt-2 font-medium text-text-main">Kolom lain (opsional):</div>
          <div>asal_sekolah, kompetensi_keahlian, tgl_masuk_pkl, durasi_pkl, no_hp, alamat_rumah,</div>
          <div>nama_guru_pembimbing, no_hp_guru_pembimbing, foto_peserta_pkl</div>
        </div>
      </div>

      <form id="importForm"
            action="{{ route('pembimbing.excel.import') }}"
            method="POST"
            enctype="multipart/form-data"
            class="mt-5 space-y-4">
        @csrf

        <label id="dropZone"
               class="group block cursor-pointer rounded-2xl border border-dashed border-black/15 bg-surface-light p-6 text-center hover:bg-black/2 transition">
          <input id="fileInput"
                 type="file"
                 name="file"
                 accept=".xlsx,.xls"
                 class="hidden"
                 required>

          <div class="mx-auto flex max-w-xl flex-col items-center gap-2">
            <div class="rounded-2xl bg-primary/10 p-3 text-primary border border-black/5">
              📄
            </div>

            <div class="text-sm font-semibold text-text-main">
              Drag & drop file Excel di sini, atau <span class="text-primary">klik untuk pilih</span>
            </div>

            <div class="text-xs text-text-secondary">
              Format disarankan: <span class="font-medium">.xlsx</span> — Sheet: <span class="font-medium">Sheet1</span>
            </div>

            <div id="fileName"
                 class="mt-2 hidden rounded-xl border border-black/10 bg-background-light px-3 py-2 text-xs text-text-main">
            </div>
          </div>
        </label>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div class="text-xs text-text-secondary">
            Tips: kolom <span class="font-medium text-text-main">no_hp</span> sebaiknya format <span class="font-medium text-text-main">Text</span> di Excel biar digit nggak kepotong.
          </div>

          <button id="submitBtn" class="pins-btn-primary">
            Import sekarang
          </button>
        </div>

        <div id="uploadHint"
             class="hidden rounded-xl border border-black/10 bg-background-light px-4 py-3 text-xs text-text-secondary">
          ⏳ Lagi upload & proses… tunggu bentar ya.
        </div>
      </form>
    </div>
  </div>

  <script>
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileName = document.getElementById('fileName');
    const importForm = document.getElementById('importForm');
    const submitBtn = document.getElementById('submitBtn');
    const uploadHint = document.getElementById('uploadHint');

    function setFileLabel(file) {
      if (!file) return;
      fileName.textContent = `File dipilih: ${file.name}`;
      fileName.classList.remove('hidden');
    }

    fileInput.addEventListener('change', (e) => setFileLabel(e.target.files?.[0]));

    dropZone.addEventListener('dragover', (e) => {
      e.preventDefault();
      dropZone.classList.add('ring-2', 'ring-primary/30');
    });

    dropZone.addEventListener('dragleave', () => {
      dropZone.classList.remove('ring-2', 'ring-primary/30');
    });

    dropZone.addEventListener('drop', (e) => {
      e.preventDefault();
      dropZone.classList.remove('ring-2', 'ring-primary/30');

      const file = e.dataTransfer.files?.[0];
      if (!file) return;

      const ok = /\.(xlsx|xls)$/i.test(file.name);
      if (!ok) {
        alert('File harus .xlsx atau .xls ya!');
        return;
      }

      const dt = new DataTransfer();
      dt.items.add(file);
      fileInput.files = dt.files;

      setFileLabel(file);
    });

    importForm.addEventListener('submit', () => {
      submitBtn.disabled = true;
      uploadHint.classList.remove('hidden');
    });
  </script>
@endsection
