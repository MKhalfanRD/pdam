@extends('layout.app')

@section('content')
<div class="container mx-auto">
    <h2 class="text-2xl font-semibold mb-4">Hallo {{ $warga->nama }}</h2>

    <!-- Ringkasan Tagihan Bulan Ini -->
    <div class="mb-6">
        <h3 class="text-xl font-medium">Tagihan Bulan Ini</h3>
        <p>Jumlah Tagihan: Rp {{ number_format($tagihanBulanIni->tagihanAir ?? 0, 2, ',', '.') }}</p>
        <p>Status Pembayaran: {{ $tagihanBulanIni && $tagihanBulanIni->status ? 'Sudah Dibayar' : 'Belum Dibayar' }}</p>
        <a href="{{ route('pembayaran.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg mt-2 inline-block">Bayar Tagihan</a>
    </div>

    <!-- Tunggakan -->
    @if($tunggakan > 0)
        <div class="mb-6 bg-red-100 p-4 rounded">
            <h3 class="text-xl font-medium text-red-700">Tunggakan</h3>
            <p>Jumlah Tunggakan: Rp {{ number_format($tunggakan, 2, ',', '.') }}</p>
        </div>
    @endif
</div>
@endsection
