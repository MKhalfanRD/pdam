@extends('layout.app')

@section('content')
    <div class="container mx-auto">
        <h2 class="text-2xl font-semibold mb-4">Hallo {{ $warga->nama ?? 'N/A' }}</h2>

        <!-- Ringkasan Tagihan Bulan Ini -->
        <div class="container mx-auto mt-8">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2"> <!-- Grid for two side-by-side cards -->

                <!-- Card 1 -->
                <div class="bg-gray-700 shadow-lg rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-4">Jumlah Tagihan</h3>
                    <p class="text-white">
                        @if ($tagihanBulanIni)
                            Rp. {{ number_format($tagihanBulanIni->tagihanAir, 2, ',', '.') }}
                        @else
                            <span class="text-red-500">Tagihan bulan ini belum diinput oleh operator.</span>
                        @endif
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="bg-gray-700 shadow-lg rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-4">Status Pembayaran</h3>
                    @if ($tagihanBulanIni && $pembayaran)
                        <p class="text-white">
                            @if ($pembayaran->status === 'pending')
                                Sudah Dibayar, menunggu acc admin
                            @elseif($pembayaran->status === 'terverifikasi')
                                Sudah Dibayar
                            @endif
                        </p>
                    @else
                        <p class="text-white">Belum Dibayar</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="mb-6">
            @if ($pembayaran)
                <!-- Button to view payment details if already paid -->
                <a href="{{ route('pembayaran.create', ['pemakaianAir_id' => $tagihanBulanIni->pemakaianAir_id]) }}"
                    class="bg-green-500 text-white px-4 py-2 rounded-lg mt-2 inline-block">Lihat</a>
            @else
                <!-- Button to make a payment if not yet paid -->
                <a href="{{ route('pembayaran.create') }}"
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg mt-2 inline-block">Bayar Tagihan</a>
            @endif
        </div>

        <!-- Tunggakan -->
        @if ($tunggakan > 0)
            <div class="mb-6 bg-red-100 p-4 rounded">
                <h3 class="text-xl font-medium text-red-700">Tunggakan</h3>
                <p>Jumlah Tunggakan: Rp {{ number_format($tunggakan, 2, ',', '.') }}</p>
            </div>
        @endif
    </div>
@endsection
