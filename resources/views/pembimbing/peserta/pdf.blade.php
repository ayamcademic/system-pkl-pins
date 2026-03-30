<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 6px; vertical-align: top; }
    th { background: #f3f4f6; }
    img { width: 60px; height: 60px; object-fit: cover; }
  </style>
</head>
<body>

@php
  $generatedAt = \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y');
@endphp

<div style="text-align:center; margin-bottom:15px;">
  <div style="font-size:18px; font-weight:bold;">Laporan Data Peserta PKL</div>
  <div style="font-size:11px; color:#555; margin-top:4px;">Website Sistem Informasi PKL PT PINS</div>
  <div style="font-size:11px; color:#555;">Per Tanggal {{ $generatedAt }}</div>
</div>

<table>
  <thead>
    <tr>
      <th>Foto</th>
      <th>Nama</th>
      <th>Sekolah</th>
      <th>Kompetensi</th>
      <th>Durasi</th>
      <th>Status</th>
    </tr>
  </thead>

  <tbody>
    @foreach($peserta as $p)
      @php
        $schoolKey = $p->asal_sekolah ?: '(Kosong)';
        $bg = $schoolColors[$schoolKey] ?? '#FFFFFF';
      @endphp

     <tr bgcolor="{{ $bg }}">
        <td>
          @if($p->foto_path)
            <img src="{{ public_path('storage/'.$p->foto_path) }}">
          @else
            -
          @endif
        </td>
        <td>{{ $p->nama }}</td>
        <td>{{ $p->asal_sekolah }}</td>
        <td>{{ $p->kompetensi_keahlian }}</td>
        <td>{{ $p->durasi_pkl }}</td>
        <td>{{ $p->status_pkl }}</td>
      </tr>
    @endforeach
  </tbody>
</table>

</body>
</html>
