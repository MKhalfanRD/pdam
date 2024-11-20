@extends('layout.app')

@section('content')
    <div class="container mx-auto">
        <div class="container mx-auto bg-white p-6 rounded-lg shadow-lg w-fit">
            <h2 class="text-2xl font-bold text-center mb-6">Upload Bukti Pembayaran</h2>

            @if ($pembayaran)
                <!-- Jika pembayaran sudah ada, tampilkan bukti dan nonaktifkan form -->
                <div class="mb-6">
                    <p class="block text-sm font-medium text-gray-700 mb-2">Bukti Pembayaran</p>
                    <img src="{{ asset('storage/' . $pembayaran->buktiBayar) }}" alt="Bukti Pembayaran"
                        class="w-full h-auto rounded-lg shadow mb-4">
                    <p class="block text-sm font-medium text-gray-700">Komentar:</p>
                    <p class="text-gray-600 italic">{{ $pembayaran->komentar }}</p>
                </div>
                <a href="{{ route('warga.history') }}">
                    <button type="button"
                        class="bg-gray-400 px-5 py-2.5 text-white rounded-md hover:bg-gray-500 focus:outline-none">Kembali</button>
                </a>
            @endif
        </div>
    </div>
@endsection
