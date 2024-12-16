@extends('layout.app')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-lg w-full">
        <h2 class="text-2xl font-bold text-center mb-6">Upload Bukti Pembayaran</h2>

        @if ($pembayaran)
            @if ($pembayaran->status === 'Terverifikasi')
                <!-- Jika status Terverifikasi, hanya tampilkan tombol Kembali -->
                <div class="bg-green-200 text-green-800 p-4 rounded mb-6">
                    <p class="text-center">Pembayaran telah diverifikasi. Anda tidak dapat mengedit data ini.</p>
                </div>
                <div class="mb-6">
                    <p class="block text-sm font-medium text-gray-700">Jumlah Tagihan:</p>
                    <p class="text-lg font-semibold text-gray-800">
                        Rp. {{ number_format($pemakaianAir->tagihanAir, 0, ',', '.') }}
                    </p>
                </div>
                <div class="mb-6">
                    <label for="buktiBayar" class="block text-sm font-medium text-gray-700 mb-2">Bukti Pembayaran</label>
                    <img src="{{ asset('storage/' . $pembayaran->buktiBayar) }}" alt="Bukti Pembayaran"
                        class="mx-auto my-2">
                </div>

                <div class="mb-6">
                    <label for="komentar" class="block text-sm font-medium text-gray-700 mb-2">Komentar</label>
                    <textarea name="komentar" id="komentar"
                        class="block w-full rounded-md bg-gray-200 text-gray-900 px-3.5 py-2 mt-2 focus:outline-none" rows="3"
                        disabled>{{ old('komentar', $pembayaran->komentar ?? '') }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="statusValidasi" class="block text-sm font-medium text-gray-700 mb-2">Status Validasi</label>
                    <select name="statusValidasi" id="statusValidasi"
                        class="w-full px-4 py-2 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        disabled>
                        <option value="valid" {{ $validasi->statusValidasi }} class="text-black bg-green-300">Valid
                        </option>
                        <option value="invalid" {{ $validasi->statusValidasi }} class="text-black bg-red-400">Invalid
                        </option>
                    </select>
                    @error('statusValidasi')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="4"
                        class="w-full px-4 py-2 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        disabled>{{ $validasi->keterangan }}</textarea>
                    @error('keterangan')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-between mt-6">
                    <a href="{{ route('warga.index') }}">
                        <button type="button"
                            class="bg-gray-400 px-5 py-2.5 text-white rounded-md hover:bg-gray-500 focus:outline-none">Kembali</button>
                    </a>
                </div>
            @else
                <!-- Jika status belum Terverifikasi, tampilkan form -->
                <form action="{{ route('pembayaran.update', $pembayaran) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

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

                    <input type="hidden" name="pemakaianAir_id" value="{{ $pemakaianAir->pemakaianAir_id }}">

                    <div class="mb-6">
                        <label for="buktiBayar" class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti
                            Pembayaran</label>
                        <input id="buktiBayar" name="buktiBayar" type="file" accept="image/*"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-md bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500">
                        @if ($pembayaran->buktiBayar)
                            <img src="{{ asset('storage/' . $pembayaran->buktiBayar) }}" alt="Bukti Pembayaran"
                                class="mx-auto my-2">
                        @endif
                        @error('buktiBayar')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="komentar" class="block text-sm font-medium text-gray-700 mb-2">Komentar</label>
                        <textarea name="komentar" id="komentar"
                            class="block w-full rounded-md bg-gray-200 text-gray-900 px-3.5 py-2 mt-2 focus:outline-none" rows="3">{{ old('komentar', $pembayaran->komentar ?? '') }}</textarea>
                        @error('komentar')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    @if (isset($validasi))
                        <div class="mb-4">
                            <label for="statusValidasi" class="block text-sm font-medium text-gray-700 mb-2">Status
                                Validasi</label>
                            <select name="statusValidasi" id="statusValidasi"
                                class="w-full px-4 py-2 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                disabled>
                                <option value="valid"
                                    {{ old('statusValidasi', $validasi->statusValidasi) == 'valid' ? 'selected' : '' }}
                                    class="text-black bg-green-300">Valid</option>
                                <option value="invalid"
                                    {{ old('statusValidasi', $validasi->statusValidasi) == 'invalid' ? 'selected' : '' }}
                                    class="text-black bg-red-400">Invalid</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="4"
                                class="w-full px-4 py-2 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                disabled>{{ old('keterangan', $validasi->keterangan) }}</textarea>
                        </div>
                    @else
                        <p class="text-yellow-900 text-center bg-yellow-200 p-4 rounded">Admin belum memvalidasi.</p>
                    @endif

                    <div class="flex justify-between mt-6">
                        <a href="{{ route('warga.index') }}">
                            <button type="button"
                                class="bg-gray-400 px-5 py-2.5 text-white rounded-md hover:bg-gray-500 focus:outline-none">Kembali</button>
                        </a>
                        <button type="submit"
                            class="bg-indigo-600 text-white px-5 py-2.5 rounded-md hover:bg-indigo-700 focus:outline-none">Simpan</button>
                    </div>
                </form>
            @endif
        @else
            <!-- Jika belum ada data pembayaran -->
            <div class="bg-yellow-200 text-yellow-800 p-4 rounded mb-6">
                <p class="text-center">Belum ada data tagihan untuk bulan ini. Silakan tunggu hingga operator menginput
                    data.</p>
            </div>
            <a href="{{ route('warga.index') }}">
                <button type="button"
                    class="bg-gray-400 px-5 py-2.5 text-white rounded-md hover:bg-gray-500 focus:outline-none">Kembali</button>
            </a>
        @endif
    </div>
@endsection
