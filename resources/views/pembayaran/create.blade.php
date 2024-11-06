@extends('layout.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-lg w-full">
    <h2 class="text-2xl font-bold text-center mb-4">Upload Bukti Pembayaran</h2>

    @if ($pembayaran) <!-- If payment already exists, display proof and disable form -->
        <div class="mb-4">
            <p class="block text-sm font-medium text-gray-700">Bukti Pembayaran</p>
            <img src="{{ asset('storage/' . $pembayaran->buktiBayar) }}" alt="Bukti Pembayaran" class="w-full h-auto mb-4">
            <p class="block text-sm font-medium text-gray-700">Komentar: {{ $pembayaran->komentar }}</p>
        </div>
        <a href="{{ route('warga.index') }}">
            <button type="button" class="bg-gray-400 px-4 py-2 text-white rounded-md">Kembali</button>
        </a>
    @else
        <form action="{{ route('pembayaran.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @if($pemakaianAir)
                <div class="mb-4">
                    <p class="block text-sm font-medium text-gray-700">Jumlah Tagihan:
                        @if (is_null($pemakaianAir->tagihanAir))
                            <span class="text-red-500">nilai belum diinput operator</span>
                        @else
                            Rp. {{ number_format($pemakaianAir->tagihanAir, 2, ',', '.') }}
                        @endif
                    </p>
                </div>

                <input type="hidden" name="pemakaianAir_id" value="{{ $pemakaianAir->pemakaianAir_id }}">

                <div class="mb-4">
                    <label for="buktiBayar" class="block text-sm font-medium text-gray-700">Upload Bukti Pembayaran</label>
                    <input id="buktiBayar" name="buktiBayar" type="file" accept="image/*" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-md bg-gray-50 focus:outline-none">
                    @error('buktiBayar')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="komentar" class="block text-sm font-medium text-gray-700 mb-2">Komentar</label>
                    <textarea name="komentar" id="komentar" class="block w-full rounded-md bg-gray-200 text-gray-900 px-3.5 py-2 mt-2" rows="3"></textarea>
                    @error('komentar')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mt-6 flex justify-between">
                    <a href="{{ route('warga.index') }}">
                        <button type="button" class="bg-gray-400 px-4 py-2 text-white rounded-md">Kembali</button>
                    </a>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none">Upload</button>
                </div>
            @else
                <div class="bg-yellow-200 text-yellow-800 p-3 rounded mb-4">
                    Belum ada data tagihan untuk bulan ini. Silakan tunggu hingga operator menginput data.
                </div>
            @endif
        </form>
    @endif
</div>
@endsection
