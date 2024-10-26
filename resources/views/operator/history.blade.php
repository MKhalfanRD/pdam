@extends('layout.app')

@section('content')
<div class="flex flex-col justify-between items-center mb-4">
    <h1 class="text-2xl font-bold mb-4">Riwayat Air Per Bulan</h1>
    <form action="{{ route('operator.history') }}" method="GET" class="mb-4 gap-4 flex items-center justify-center">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama atau blok..." class="border rounded px-3 py-2 text-white">
        <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg">Cari</button>

        <!-- Hidden input untuk mempertahankan bulan dan tahun yang dipilih -->
        <input type="hidden" name="bulan" value="{{ $selectedBulan }}">
        <input type="hidden" name="tahun" value="{{ $selectedTahun }}">
    </form>
    <form action="{{ route('operator.history') }}" method="GET" class="flex items-center gap-2">
        <!-- Pilih Bulan -->
        <select name="bulan" id="bulan" class="bg-gray-200 rounded-md text-black">
            @foreach ($bulanList as $key => $namaBulan)
                <option value="{{ $key }}" {{ $key == $selectedBulan ? 'selected' : '' }}>
                    {{ $namaBulan }}
                </option>
            @endforeach
        </select>

        <!-- Pilih Tahun -->
        <select name="tahun" id="tahun" class="bg-gray-200 rounded-md text-black">
            @foreach ($tahunList as $tahun)
                <option value="{{ $tahun }}" {{ $tahun == $selectedTahun ? 'selected' : '' }}>
                    {{ $tahun }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="bg-indigo-600 text-white px-2 py-1.5 rounded-md">Cari</button>
    </form>
</div>

<!-- Tabel History Pemakaian -->
@if ($pemakaianAir->isEmpty())
    <div class="text-center py-4">
        <p class="text-gray-500">Belum ada riwayat untuk bulan {{ $bulanList[$selectedBulan] }} {{ $selectedTahun }}.</p>
    </div>
@else
    <table class="table">
        <!-- head -->
        <thead>
            <tr class="text-gray-100">
                <th></th>
                <th>Blok</th>
                <th>Nama</th>
            </tr>
        </thead>
        <tbody>
            @php
                $counter = 0;
            @endphp
            @foreach ($pemakaianAir as $index => $pemakaian)
                <tr class="text-gray-200">
                    <th>
                        @php
                            $counter++
                        @endphp
                        {{$counter}}
                    </th>
                    <td>{{$pemakaian->warga->alamat}}</td>
                    <td>{{$pemakaian->warga->nama}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
@endsection
