@extends('layout.app')

@section('content')
    <div class="container mx-auto max-w-7xl">
        <form method="GET" class="mb-4">
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

            <button type="submit" class="ml-4 px-4 py-2 bg-blue-500 text-white rounded">Filter</button>
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
                        <td class="py-2 px-4">{{ $d->tagihanAir }}</td>
                        <td class="py-2 px-4">
                            <button class="flag-toggle">
                                <img src="{{ asset('icon/red_flag.png') }}" alt="toggle" class="w-5">
                            </button>
                        </td>
                        <td class="py-2 px-4">
                            <a href="{{ route('admin.show', $d->warga_id) }}">
                                <img src="{{ asset('icon/detail.png') }}" alt="detail" class="w-5">
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-gray-400">Data tidak tersedia untuk bulan dan tahun yang dipilih.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const flagImages = document.querySelectorAll('.flag-toggle img');
        flagImages.forEach(function (flagImage) {
            flagImage.addEventListener('click', function () {
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
