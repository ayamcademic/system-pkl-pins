@extends('peserta.layout')

@section('content')
<div class="pins-card p-5">
  <div class="text-xl font-semibold">Profil</div>
  <div class="text-sm text-text-secondary mt-1">Atur akun kamu.</div>

  {{-- Update profile --}}
  <form method="POST" action="{{ route('profile.update') }}" class="mt-4 space-y-3">
    @csrf
    @method('PATCH')

    <div>
      <label class="text-sm">Nama</label>
      <input name="name" value="{{ old('name', $user->name) }}"
             class="w-full mt-1 rounded-xl border border-black/10 px-3 py-2" />
    </div>

    <div>
      <label class="text-sm">Email</label>
      <input name="email" value="{{ old('email', $user->email) }}"
             class="w-full mt-1 rounded-xl border border-black/10 px-3 py-2" />
    </div>

    <button class="pins-btn-primary w-full">Simpan</button>
  </form>
</div>

<div class="pins-card p-5 mt-4">
  <div class="text-lg font-semibold">Ganti Password</div>

  <form method="POST" action="{{ route('password.update') }}" class="mt-4 space-y-3">
    @csrf
    @method('PUT')

    <div>
      <label class="text-sm">Password sekarang</label>
      <input type="password" name="current_password"
             class="w-full mt-1 rounded-xl border border-black/10 px-3 py-2" />
    </div>

    <div>
      <label class="text-sm">Password baru</label>
      <input type="password" name="password"
             class="w-full mt-1 rounded-xl border border-black/10 px-3 py-2" />
    </div>

    <div>
      <label class="text-sm">Konfirmasi password baru</label>
      <input type="password" name="password_confirmation"
             class="w-full mt-1 rounded-xl border border-black/10 px-3 py-2" />
    </div>

    <button class="pins-btn-primary w-full">Update Password</button>
  </form>
</div>

<div class="pins-card p-5 mt-4 border border-red-200">
  <div class="text-lg font-semibold text-red-600">Hapus Akun</div>
  <p class="text-sm text-text-secondary mt-1">Aksi ini permanen.</p>

  <form method="POST" action="{{ route('profile.destroy') }}" class="mt-3">
    @csrf
    @method('DELETE')

    <button class="w-full rounded-xl bg-red-600 text-white py-2 font-semibold">
      Hapus Akun
    </button>
  </form>
</div>
@endsection