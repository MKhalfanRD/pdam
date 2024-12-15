@extends('layout.app')

@section('content')
    <div class="container mx-auto p-6">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-3xl mx-auto">
            <h2 class="text-2xl font-semibold text-center mb-6">Upload Bukti Pembayaran</h2>

            @if (session('success'))
                <div class="bg-green-500 text-white p-4 rounded-md mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form Validasi -->
                <form action="{{ route('admin.update', $validasi->validasi_id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="mb-6">
                        <p class="text-sm font-medium text-gray-700 mb-2">Bukti Pembayaran</p>
                        <img src="{{ asset('storage/' . $pembayaran->buktiBayar) }}" alt="Bukti Pembayaran"
                            class="w-full md:w-1/2 h-auto rounded-lg shadow mb-4 mx-auto">
                        <p class="text-sm font-medium text-gray-700">Komentar:</p>
                        <p class="text-gray-600 italic">{{ $pembayaran->komentar }}</p>
                    </div>

                    <div class="mb-4">
                        <label for="statusValidasi" class="block text-sm font-medium text-gray-700 mb-2">Status Validasi</label>
                        <select name="statusValidasi" id="statusValidasi" class="w-full px-4 py-2 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="valid" {{ old('statusValidasi', $validasi->statusValidasi) == 'valid' ? 'selected' : '' }} class="text-black bg-green-300">Valid</option>
                            <option value="invalid" {{ old('statusValidasi', $validasi->statusValidasi) == 'invalid' ? 'selected' : '' }} class="text-black bg-red-400">Invalid</option>
                        </select>
                        @error('statusValidasi')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="4" class="w-full px-4 py-2 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('keterangan', $validasi->keterangan) }}</textarea>
                        @error('keterangan')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-between items-center">
                        <a href="{{ route('admin.index') }}">
                            <button type="button" class="bg-gray-400 px-5 py-2.5 text-white rounded-md hover:bg-gray-500 focus:outline-none">Kembali</button>
                        </a>
                        <button type="submit" class="bg-green-500 px-5 py-2.5 text-white rounded-md hover:bg-green-600 focus:outline-none">
                            Simpan
                        </button>
                    </div>
                </form>
        </div>
    </div>
@endsection
