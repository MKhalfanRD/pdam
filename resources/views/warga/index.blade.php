@extends('layout.app')

@section('content')
<div class="container mx-auto">
    <h2 class="text-2xl font-semibold mb-4">Hallo {{ $warga->nama ?? 'N/A' }}</h2>

    {{-- <!-- Debugging -->
    <div class="mb-6 bg-yellow-100 p-4 rounded">
        <h3 class="text-xl font-medium text-yellow-700">Debug Info</h3>
        <p>Role: {{ $role ?? 'Role not available' }}</p>
        @if(isset($tagihanBulanIni))
            <p>Tagihan Bulan Ini:</p>
            @dump($tagihanBulanIni)
        @else
            <p>Tagihan Bulan Ini tidak ditemukan.</p>
        @endif
        <p>Tunggakan: Rp {{ number_format($tunggakan, 2, ',', '.') }}</p>
    </div> --}}

    <!-- Ringkasan Tagihan Bulan Ini -->
    <div class="container mx-auto mt-8">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2"> <!-- Grid untuk card 2 buah sejajar -->

            <!-- Card 1 -->
            <div class="bg-gray-700 shadow-lg rounded-lg p-6">
                <h3 class="text-xl font-bold mb-4">Jumlah Tagihan</h3>
                <p class="text-white">Rp. {{ number_format($tagihanBulanIni->tagihanAir ?? 0, 2, ',', '.') }}</p>
            </div>

            <!-- Card 2 -->
            <div class="bg-gray-700 shadow-lg rounded-lg p-6">
                <h3 class="text-xl font-bold mb-4">Status Pembayaran</h3>
                <p class="text-white">{{ $tagihanBulanIni && $tagihanBulanIni->status ? 'Sudah Dibayar' : 'Belum Dibayar' }}</p>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <h3 class="text-xl font-medium">Belum ada Tagihan Bulan Ini</h3>
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
