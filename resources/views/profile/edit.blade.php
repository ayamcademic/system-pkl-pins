@extends(auth()->user()?->role === 'peserta' ? 'peserta.layout' : 'pembimbing.layout', ['pageTitle' => 'Profil'])

@section('content')
  <div class="pins-card p-6">
    <div>
      <h1 class="font-serif text-2xl text-text-main">Profil</h1>
      <p class="text-sm text-text-secondary mt-1">Atur informasi akun dan keamanan tanpa harus nyasar ke layout role lain. Hal sederhana yang ternyata perlu diperbaiki, rupanya.</p>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4">
      <div class="rounded-2xl border border-black/5 bg-background-light p-5">
        <div class="max-w-xl">
          @include('profile.partials.update-profile-information-form')
        </div>
      </div>

      <div class="rounded-2xl border border-black/5 bg-background-light p-5">
        <div class="max-w-xl">
          @include('profile.partials.update-password-form')
        </div>
      </div>

      <div class="rounded-2xl border border-black/5 bg-background-light p-5">
        <div class="max-w-xl">
          @include('profile.partials.delete-user-form')
        </div>
      </div>
    </div>
  </div>
@endsection
