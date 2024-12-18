@extends('layout.app')

@section('content')
<!-- Notifikasi Berhasil -->
@if (session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
    {{-- <strong class="font-bold">Berhasil!</strong> --}}
    <span class="block sm:inline">{{ session('success') }}</span>
    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
        <span class="text-green-500">&times;</span>
    </button>
</div>
@endif

<div>
    <!-- Search Filter -->
    <form action="{{ route('operator.index') }}" method="GET" class="mb-4 gap-1 flex items-center justify-center">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama atau blok..." class="border rounded px-1 py-1 text-black">
        <button type="submit" class="bg-indigo-600 text-white px-1 py-1 rounded-lg">Cari</button>

        <!-- Tambahkan hidden input untuk menjaga urutan sorting -->
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="direction" value="{{ $direction }}">
    </form>

    <!-- Tombol Tambah -->
    <div class="mb-4 flex justify-center">
        <a href="{{ route('operator.create') }}">
            <button class="bg-green-600 text-white px-2 py-1 rounded-lg">Tambah</button>
        </a>
    </div>

    <!-- Cek apakah ada data warga -->
    @if ($warga->isEmpty())
        <div class="text-center text-gray-500">
            <p>Belum ada warga.</p>
        </div>
    @else
    <table class="container mx-auto table w-full max-w-4xl">
      <!-- head -->
      <thead>
        <tr class="text-gray-100">
          <th></th>
          <th>
              <a href="{{ route('operator.index', ['sort' => 'alamat', 'direction' => ($sort == 'alamat' && $direction == 'asc') ? 'desc' : 'asc']) }}">
                  Blok
                  @if ($sort == 'alamat')
                      @if ($direction == 'asc')
                          <span class="text-white">↑</span>
                      @else
                          <span class="text-white">↓</span>
                      @endif
                  @else
                      <span class="text-gray-400">↑↓</span> <!-- Panah default -->
                  @endif
              </a>
          </th>
          <th>
              <a href="{{ route('operator.index', ['sort' => 'nama', 'direction' => ($sort == 'nama' && $direction == 'asc') ? 'desc' : 'asc']) }}">
                  Nama
                  @if ($sort == 'nama')
                      @if ($direction == 'asc')
                          <span class="text-white">↑</span>
                      @else
                          <span class="text-white">↓</span>
                      @endif
                  @else
                      <span class="text-gray-400">↑↓</span> <!-- Panah default -->
                  @endif
              </a>
          </th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @php
            $counter = ($warga->currentPage() - 1) * $warga->perPage(); // Sesuaikan nomor halaman
        @endphp
        @foreach ($warga as $w)
            <tr class="text-gray-200">
                <th>
                    {{ ++$counter }}
                </th>
                <td>{{ $w->alamat }}</td>
                <td>{{ $w->nama }}</td>
                <td>
                    <div class="">
                        <a href="{{ route('operator.edit', $w->warga_id) }}">
                            <button type="submit" class="bg-indigo-600 text-white p-2 rounded-lg">Edit</button>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
      </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="">
        {{ $warga->links() }}
    </div>
    @endif
</div>
@endsection
