@extends('layout.app')

@section('content')

    <div class="bg-white p-6 rounded-lg shadow-lg w-full">
        <h2 class="text-2xl font-bold text-center mb-6">Upload Bukti Pembayaran</h2>
        {{-- Menampilkan pesan jika ada --}}
        @if (isset($message))
            <div class="bg-yellow-200 text-yellow-800 p-4 rounded mb-6">
                <p class="text-center">{{ $message }}</p>
            </div>
        @endif
        @if (session('success'))
                <div class="bg-green-500 text-white p-4 rounded-md mb-4">
                    {{ session('success') }}
                </div>
        @endif

        {{-- @if (isset($pembayaran) && $pembayaran)
            <!-- Jika pembayaran sudah ada, tampilkan bukti pembayaran -->
            <div class="flex flex-col mx-auto">
                @if($pembayaran->buktiBayar == '')
                <p class="bg-yellow-200 text-gray-600 p-4 mb-3 rounded-lg">Anda belum mengunggah bukti pembayaran. Silakan unggah bukti bayar.</p>
                @elseif($pembayaran->buktiBayar)
                <p class="block text-sm font-medium text-gray-700 mb-2">Bukti Pembayaran</p>
                <img src="{{ asset('storage/' . $pembayaran->buktiBayar) }}" alt="Bukti Pembayaran"
                    class="w-min mx-auto h-auto rounded-lg shadow mb-4">
                <p class="block text-sm font-medium text-gray-700">Komentar:</p>
                <p class="text-gray-600 italic mb-4">{{ $pembayaran->komentar }}</p>
                @endif
                <div class="flex gap-4">
                    <a href="{{ route('warga.index') }}">
                        <button type="button"
                            class="bg-gray-400 px-5 py-2.5 text-white rounded-md hover:bg-gray-500 focus:outline-none">Kembali</button>
                    </a>
                    <a href="{{ route('pembayaran.create', $pembayaran) }}">
                        <button type="button"
                            class="bg-blue-600 px-5 py-2.5 text-white rounded-md hover:bg-gray-500 focus:outline-none">Bayar</button>
                    </a>
                </div>
            </div>
        @else --}}
            <!-- Jika pembayaran belum ada -->
            <form action="{{ route('pembayaran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @if(isset($pemakaianAir))
                    <div class="mb-6">
                        <p class="block text-sm font-medium text-gray-700">Jumlah Tagihan:</p>
                        <p class="text-lg font-semibold text-gray-800">
                            @if (is_null($pemakaianAir->tagihanAir))
                                <span class="text-red-500">Nilai belum diinput operator</span>
                            @else
                                Rp. {{ number_format($pemakaianAir->tagihanAir, 0, ',', '.') }}
                            @endif
                        </p>
                    </div>

                    {{-- jika tagihan air ada --}}
                    @if (!is_null($pemakaianAir->tagihanAir))
                        <input type="hidden" name="pemakaianAir_id" value="{{ $pemakaianAir->pemakaianAir_id }}">

                        <div class="mb-6">
                            <label for="buktiBayar" class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Pembayaran</label>
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
                            <button type="submit"
                                class="bg-indigo-600 text-white px-5 py-2.5 rounded-md hover:bg-indigo-700 focus:outline-none">Upload</button>
                        </div>
                    @else
                        <!-- Jika tagihan air belum ada -->
                        <div class="bg-yellow-200 text-yellow-800 p-4 rounded mb-6">
                            <p class="text-center">Belum ada data tagihan untuk bulan ini. Silakan tunggu hingga operator menginput data.</p>
                        </div>
                        <a href="{{ route('warga.index') }}">
                            <button type="button"
                                class="bg-gray-400 px-5 py-2.5 text-white rounded-md hover:bg-gray-500 focus:outline-none">Kembali</button>
                        </a>
                    @endif
                @else
                    <!-- Jika tidak ada data pemakaian air -->
                    {{-- <div class="bg-yellow-200 text-yellow-800 p-4 rounded mb-6">
                        <p class="text-center">Belum ada data tagihan untuk bulan ini. Silakan tunggu hingga operator menginput data.</p>
                    </div> --}}
                    <a href="{{ route('warga.index') }}">
                        <button type="button"
                            class="bg-gray-400 px-5 py-2.5 text-white rounded-md hover:bg-gray-500 focus:outline-none">Kembali</button>
                    </a>
                @endif
            </form>
        {{-- @endif --}}
    </div>
@endsection
