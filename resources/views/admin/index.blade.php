@extends('layout.app')

@section('content')
    <div class="container mx-auto max-w-7xl">
        <form method="GET" class="mb-4">
            {{-- <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." class="border p-2 rounded"> --}}
            <label for="bulan" class="mr-2">Pilih Bulan:</label>
            <select name="bulan" id="bulan" class="border rounded p-1">
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $i == request('bulan', now()->month) ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>

            <label for="tahun" class="ml-4 mr-2">Pilih Tahun:</label>
            <select name="tahun" id="tahun" class="border rounded p-1">
                @for ($i = 2020; $i <= now()->year; $i++)
                    <option value="{{ $i }}" {{ $i == request('tahun', now()->year) ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>

            <label for="status" class="ml-4 mr-2">Status:</label>
            <select name="status" id="status" class="border rounded p-1">
                <option value="" {{ request('status') == '' ? 'selected' : '' }}>Semua</option>
                <option value="Belum Bayar" {{ request('status') == 'Belum Bayar' ? 'selected' : '' }}>Belum Bayar</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Terverifikasi" {{ request('status') == 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi
                </option>
            </select>

            <button type="submit" class="ml-4 px-4 py-2 bg-blue-500 text-white rounded">Filter</button>

            <a href="{{ route('admin.summary', ['bulan' => request('bulan'), 'tahun' => request('tahun')]) }}"
                class="ml-4 px-4 py-2 bg-green-500 text-white rounded">Summary
            </a>
        </form>

        <table class="table-auto w-full">
            <thead>
                <tr class="bg-gray-700 text-white">
                    <th class="py-2 px-4">Blok</th>
                    <th class="py-2 px-4">Nama</th>
                    <th class="py-2 px-4">Bulan</th>
                    <th class="py-2 px-4">Tahun</th>
                    <th class="py-2 px-4">Awal</th>
                    <th class="py-2 px-4">Akhir</th>
                    <th class="py-2 px-4">Total Tagihan</th>
                    <th class="py-2 px-4">Flag</th>
                    <th class="py-2 px-4">Detail</th>
                    <th class="py-2 px-4">
                        <a href="{{ route('admin.index', ['sort' => 'status', 'direction' => $direction == 'asc' ? 'desc' : 'asc']) }}"
                            class="flex items-center">
                            Status
                            <span class="ml-1">
                                @if ($sort === 'status')
                                    @if ($direction == 'asc')
                                        ↑
                                    @else
                                        ↓
                                    @endif
                                @endif
                            </span>
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $d)
                    <tr class="bg-gray-800 text-gray-200">
                        <td class="py-2 px-4">{{ $d->warga->alamat }}</td>
                        <td class="py-2 px-4">{{ $d->warga->nama }}</td>
                        <td class="py-2 px-4">{{ $d->bulan }}</td>
                        <td class="py-2 px-4">{{ $d->tahun }}</td>
                        <td class="py-2 px-4">{{ $d->pemakaianLama }}</td>
                        <td class="py-2 px-4">{{ $d->pemakaianBaru }}</td>
                        <td class="py-2 px-4">Rp. {{ number_format($d->tagihanAir, 0, ',', '.') }}</td>
                        <td class="py-2 px-4">
                            <div class="">
                                <img src="{{ asset('icon/red_flag.png') }}" alt="toggle" class="flag-toggle w-5">

                            </div>

                        </td>
                        <td class="py-2 px-4">
                            @if($d->validasi_status === 'valid')
                            <a href="{{ route('admin.edit', $d->validasi_id) }}">
                                <img src="{{ asset('icon/detail.png') }}" alt="detail" class="flag-toggle w-5">
                            </a>
                            @elseif($d->validasi_status === 'invalid')
                            <a href="{{ route('admin.edit', $d->validasi_id) }}">
                                <img src="{{ asset('icon/detail.png') }}" alt="detail" class="flag-toggle w-5">
                            </a>
                            @else
                            <a href="{{ route('admin.show', $d->warga_id) }}">
                                <img src="{{ asset('icon/detail.png') }}" alt="detail" class="flag-toggle w-5">
                            </a>
                            @endif
                        </td>
                        <td class="py-2 px-4">
                            @if ($d->pembayaran->count() > 0)
                                @php $status = $d->pembayaran->first()->status; @endphp
                                <span
                                    class="
                                            @if ($status === 'Belum Bayar') text-red-500
                                            @elseif($status === 'Pending') text-yellow-500
                                            @elseif($status === 'Terverifikasi') text-green-500 @endif
                                        ">
                                    {{ $status }}
                                </span>
                            @else
                                <span class="text-red-500">Belum Bayar</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    @php
                        $carbonDate = \Carbon\Carbon::createFromDate($tahun, $bulan, 1);
                        $bulanHuruf = $carbonDate->format('F');
                    @endphp
                    <tr>
                        <td colspan="12" class="text-center text-red-300 py-4">Data tidak tersedia untuk bulan
                            {{ $bulanHuruf }} dan tahun {{ $tahun }}
                            yang dipilih.</td>
                    </tr>
                @endforelse
            </tbody>
            {{-- <div class="pagination">
                {{ $data->appends(['sort' => $sort, 'direction' => $direction, 'search' => $search])->links() }}
            </div> --}}
        </table>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const flagImages = document.querySelectorAll('.flag-toggle img');
            flagImages.forEach(function(flagImage) {
                flagImage.addEventListener('click', function() {
                    const currentSrc = flagImage.src;
                    const redFlag = "{!! asset('icon/red_flag.png') !!}";
                    const greenFlag = "{!! asset('icon/green_flag.png') !!}";

                    if (currentSrc.includes('red_flag.png')) {
                        flagImage.src = greenFlag;
                    } else {
                        flagImage.src = redFlag;
                    }
                });
            });
        });
    </script>
@endsection
