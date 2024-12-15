@extends('layout.app')

@section('content')
    <div class="container mx-auto">
        @if (session('success'))
                <div class="bg-green-500 text-white p-4 rounded-md mb-4">
                    {{ session('success') }}
                </div>
            @endif
        <h2 class="text-2xl font-semibold mb-4">Hallo {{ $warga->nama ?? 'N/A' }}</h2>

        <!-- Ringkasan Tagihan Bulan Ini -->
        <div class="container mx-auto mt-8">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2"> <!-- Grid for two side-by-side cards -->

                <!-- Card 1 -->
                <div class="bg-gray-700 shadow-lg rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-4">Tagihan Bulan Ini</h3>
                    <p class="text-white">
                        @if ($pemakaianAir)
                            Rp. {{ number_format($pemakaianAir->tagihanAir, 0, ',', '.') }}
                        @else
                            <span class="text-red-500">Tagihan bulan ini belum diinput oleh operator.</span>
                        @endif
                    </p>

                </div>

                <!-- Card 2 -->
                <div class="bg-gray-700 shadow-lg rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-4">Status Pembayaran</h3>
                    @if ($statusPembayaran === 'Belum Bayar')
                    <p class="text-2xl text-red-400">Belum Bayar</p>
                    @elseif($statusPembayaran === 'Pending')
                    <p class="text-xl text-yellow-400">Pending</p>
                    @elseif($statusPembayaran === 'Terverifikasi')
                    <p class="text-xl text-green-400">Terverifikasi</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="mb-6">
            @if ($statusPembayaran === 'Belum Bayar')
                <a href="{{ route('pembayaran.create') }}"
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg mt-2 inline-block">Bayar Tagihan</a>
            @elseif ($statusPembayaran === 'Terverifikasi')
                <a href="{{ route('pembayaran.edit', $pembayaran->pembayaran_id) }}"
                    class="bg-green-500 text-white px-4 py-2 rounded-lg mt-2 inline-block">Lihat
                    Pembayaran</a>
            @elseif ($statusPembayaran === 'Pending')
                <a href="{{ route('pembayaran.edit', $pembayaran->pembayaran_id) }}"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg mt-2 inline-block">Edit
                    Pembayaran</a>
            @endif
        </div>

        {{-- <!-- Tunggakan -->
        @if ($tunggakan > 0)
            <div class="mb-6 bg-red-100 p-4 rounded">
                <h3 class="text-xl font-medium text-red-700">Tunggakan</h3>
                <p>Jumlah Tunggakan: Rp {{ number_format($tunggakan, 2, ',', '.') }}</p>
            </div>
        @endif --}}
    </div>
@endsection
