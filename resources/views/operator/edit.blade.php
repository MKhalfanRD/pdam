@extends('layout.app')

@section('content')
    <form action="{{ route('operator.update', $warga->warga_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full">
                <h2 class="text-2xl font-bold text-center mb-4">Upload Foto</h2>

                <!-- Input untuk Bulan dan Tahun -->
                {{-- <div class="flex flex-row gap-6 mb-4">
                    <div>
                        <select name="bulan" id="bulan" class="bg-gray-200 rounded-md text-black">
                            @foreach ($bulan as $key => $namaBulan)
                                <option value="{{ $key }}"
                                    {{ $pemakaianAir && $key == date('m', strtotime($pemakaianAir->bulan)) ? 'selected' : '' }}>
                                    {{ $namaBulan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="tahun" id="tahun" class="bg-gray-200 rounded-md text-black">
                            @foreach ($tahun as $tahunItem)
                                <option value="{{ $tahunItem }}"
                                    {{ $pemakaianAir && $tahunItem == date('Y', strtotime($pemakaianAir->bulan)) ? 'selected' : '' }}>
                                    {{ $tahunItem }}</option>
                            @endforeach
                        </select>
                    </div>
                </div> --}}

                <!-- Input Pemakaian Lama -->
                <div class="mb-4">
                    <label for="pemakaianLama" class="block text-sm font-medium text-gray-700 mb-2">Pemakaian Lama</label>
                    <input type="number" name="pemakaianLama" id="pemakaianLama"
                        value="{{ $pemakaianAir->pemakaianLama ?? '' }}"
                        class="block w-full rounded-md bg-gray-200 text-gray-900 px-3.5 py-2 mt-2">
                    @error('pemakaianLama')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Input Pemakaian Baru -->
                <div class="mb-4">
                    <label for="pemakaianBaru" class="block text-sm font-medium text-gray-700 mb-2">Pemakaian Baru</label>
                    <input type="number" name="pemakaianBaru" id="pemakaianBaru"
                        value="{{ $pemakaianAir->pemakaianBaru ?? '' }}"
                        class="block w-full rounded-md bg-gray-200 text-gray-900 px-3.5 py-2 mt-2">
                    @error('pemakaianBaru')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Area Preview Foto -->
                <div class="mb-4">
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                    <div class="flex items-center justify-center bg-gray-200 h-40 w-full rounded-md">
                        @if ($pemakaianAir && $pemakaianAir->foto)
                            <img id="photoPreview" src="{{ $pemakaianAir->foto ? asset('storage/' . $pemakaianAir->foto) : '' }}" alt="Preview Foto"
                                class="{{ $pemakaianAir->foto ? 'h-full object-cover rounded-md' : 'hidden' }}">
                        @else
                            <span id="noPhoto" class="text-gray-500">Tidak ada foto</span>
                        @endif
                    </div>
                </div>


                <!-- Input untuk Unggah Foto -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Pilih Foto</label>
                    <input id="fotoMeteran" name="fotoMeteran" type="file" accept="image/*"
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-md bg-gray-50 focus:outline-none">
                </div>

                <!-- Tombol Simpan -->
                <div class="mt-6 flex justify-between">
                    <a href="{{ route('operator.index') }}">
                        <button type="button" class="bg-gray-400 px-4 py-2 text-white rounded-md">Kembali</button>
                    </a>
                    <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none">Simpan</button>
                </div>

            </div>
        </div>
    </form>

    <script>
        function previewPhoto() {
            const input = document.getElementById('fotoMeteran');
            const preview = document.getElementById('photoPreview');
            const noPhoto = document.getElementById('noPhoto');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    noPhoto.classList.add('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
