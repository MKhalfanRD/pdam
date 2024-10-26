@extends('layout.app')

@section('content')
<div>
    <!-- Search Filter -->
    <form action="{{ route('operator.index') }}" method="GET" class="mb-4 gap-4 flex items-center justify-center">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama atau blok..." class="border rounded px-1 py-1 text-white">
        <button type="submit" class="bg-indigo-600 text-white px-1 py-1 rounded-lg">Cari</button>

        <!-- Tambahkan hidden input untuk menjaga urutan sorting -->
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="direction" value="{{ $direction }}">
    </form>

    <table class="table">
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
            $counter = 0;
        @endphp
        @foreach ($warga as $w)
            <tr class="text-gray-200">
                <th>
                    @php
                        $counter++
                    @endphp
                    {{$counter}}
                </th>
                <td>{{$w->alamat}}</td>
                <td>{{$w->nama}}</td>
                <td>
                    <div class="">
                        <a href="{{route('operator.edit', $w->warga_id)}}">
                            <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg">Edit</button>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
      </tbody>
    </table>
</div>
@endsection
