@extends('layout.app')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-lg w-full">
        <h2 class="text-2xl font-bold text-center mb-6">Upload Bukti Pembayaran</h2>

        @if ($pembayaran)
            <!-- Jika pembayaran sudah ada, tampilkan bukti dan nonaktifkan form -->
            <div class="flex flex-col mx-auto">
                <p class="block text-sm font-medium text-gray-700 mb-2">Bukti Pembayaran</p>
                <img src="{{ asset('storage/' . $pembayaran->buktiBayar) }}" alt="Bukti Pembayaran"
                    class="w-min mx-auto h-auto rounded-lg shadow mb-4">
                <p class="block text-sm font-medium text-gray-700">Komentar:</p>
                <p class="text-gray-600 italic mb-4">{{ $pembayaran->komentar }}</p>
                <div class="flex gap-4">
                    <a href="{{ route('warga.index') }}">
                        <button type="button"
                            class="bg-gray-400 px-5 py-2.5 text-white rounded-md hover:bg-gray-500 focus:outline-none">Kembali</button>
                    </a>
                    <a href="{{ route('pembayaran.edit', $pembayaran) }}">
                        <button type="button"
                            class="bg-blue-600 px-5 py-2.5 text-white rounded-md hover:bg-gray-500 focus:outline-none">Edit</button>
                    </a>
                </div>

            </div>
        @else
        {{-- jika pembayaran belum ada --}}
            <form action="{{ route('pembayaran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @if ($pemakaianAir)
                    <div class="mb-6">
                        <p class="block text-sm font-medium text-gray-700">Jumlah Tagihan:</p>
                        <p class="text-lg font-semibold text-gray-800">
                            @if (is_null($pemakaianAir->tagihanAir))
                                <span class="text-red-500">Nilai belum diinput operator</span>
                            @else
                                Rp. {{ number_format($pemakaianAir->tagihanAir, 2, ',', '.') }}
                            @endif
                        </p>
                    </div>

                    <input type="hidden" name="pemakaianAir_id" value="{{ $pemakaianAir->pemakaianAir_id }}">

                    <div class="mb-6">
                        <label for="buktiBayar" class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti
                            Pembayaran</label>
                        <input id="buktiBayar" name="buktiBayar" type="file" accept="image/*"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-md bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('buktiBayar')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="komentar" class="block text-sm font-medium text-gray-700 mb-2">Komentar</label>
                        <textarea name="komentar" id="komentar"
                            class="block w-full rounded-md bg-gray-200 text-gray-900 px-3.5 py-2 mt-2 focus:outline-none" rows="3"></textarea>
                        @error('komentar')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-between mt-6">
                        <a href="{{ route('warga.index') }}">
                            <button type="button"
                                class="bg-gray-400 px-5 py-2.5 text-white rounded-md hover:bg-gray-500 focus:outline-none">Kembali</button>
                        </a>
                        <a href="{{ route($pembayaran ? route('pembayaran.edit', $pembayaran) : 'warga.index') }}">
                            <button type="button"
                                class="bg-gray-400 px-5 py-2.5 text-white rounded-md hover:bg-gray-500 focus:outline-none">Edit</button>
                        </a>
                        <button type="submit"
                            class="bg-indigo-600 text-white px-5 py-2.5 rounded-md hover:bg-indigo-700 focus:outline-none">Upload</button>
                    </div>
                @else
                    <div class="bg-yellow-200 text-yellow-800 p-4 rounded mb-6">
                        <p class="text-center">Belum ada data tagihan untuk bulan ini. Silakan tunggu hingga operator
                            menginput data.</p>
                    </div>
                    <a href="{{ route('warga.index') }}">
                        <button type="button"
                            class="bg-gray-400 px-5 py-2.5 text-white rounded-md hover:bg-gray-500 focus:outline-none">Kembali</button>
                    </a>
                @endif
            </form>
        @endif
    </div>
@endsection
