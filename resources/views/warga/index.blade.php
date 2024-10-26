@extends('layout.app')

@section('content')
<div class="container">
    <h1>Data Warga</h1>

    @if($warga)
        <ul>
            <li>Username: {{ $warga->nama }}</li>
            <li>Email: {{ $warga->alamat }}</li>
            <!-- Tambahkan field lain yang ingin ditampilkan -->
        </ul>
    @else
        <p>Data tidak ditemukan.</p>
    @endif
</div>
@endsection
