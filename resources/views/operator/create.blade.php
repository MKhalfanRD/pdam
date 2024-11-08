@extends('layout.app')

@section('content')
    <div class="container mx-auto">
        <h2 class="text-center font-bold mb-4">Daftar Warga Baru</h2>
        <form action="{{ route('operator.store') }}" method="POST" class="max-w-md mx-auto">
            @csrf
            <div class="flex items-center justify-center">
                <div class="bg-white p-6 rounded-lg shadow-lg w-full">
                    <div class="mb-4">
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                        <input type="text" name="nama" id="nama"
                            class="block w-full rounded-md bg-gray-200 text-gray-900 px-3.5 py-2 mt-2" value="{{old('nama')}}">
                        @error('nama')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" id="email"
                            class="block w-full rounded-md bg-gray-200 text-gray-900 px-3.5 py-2 mt-2" value="{{old('email')}}">
                        @error('email')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                        <input type="text" name="alamat" id="alamat"
                            class="block w-full rounded-md bg-gray-200 text-gray-900 px-3.5 py-2 mt-2" value="{{(old('alamat'))}}">
                        @error('alamat')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="telp" class="block text-sm font-medium text-gray-700 mb-2">Telpon</label>
                        <input type="number" name="telp" id="telp"
                            class="block w-full rounded-md bg-gray-200 text-gray-900 px-3.5 py-2 mt-2" placeholder="0813xxx" value="{{old('telp')}}">
                        @error('telp')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" id="password"
                            class="block w-full rounded-md bg-gray-200 text-gray-900 px-3.5 py-2 mt-2">
                        @error('password')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
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
    </div>
@endsection
