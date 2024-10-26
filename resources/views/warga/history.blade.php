@extends('layout.app')
@section('content')
<!-- Histori Pembayaran -->
<div class="mb-6">
    <h3 class="text-xl font-medium">Histori Pembayaran</h3>
    <table class="table-auto w-full bg-white shadow rounded">
        <thead>
            <tr class="bg-gray-200">
                <th class="px-4 py-2">Bulan</th>
                <th class="px-4 py-2">Jumlah Tagihan</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Bukti Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($historiPembayaran as $pembayaran)
                <tr>
                    <td class="border px-4 py-2">{{ $pembayaran->bulan }}</td>
                    <td class="border px-4 py-2">Rp {{ number_format($pembayaran->tagihanAir, 2, ',', '.') }}</td>
                    <td class="border px-4 py-2">{{ $pembayaran->status }}</td>
                    <td class="border px-4 py-2">
                        @if($pembayaran->buktiBayar)
                            <a href="{{ asset('storage/' . $pembayaran->buktiBayar) }}" target="_blank" class="text-blue-500 underline">Lihat Bukti</a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
