@extends('layout.app')

@section('content')
<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-6">Riwayat Pembayaran</h2>

    <table class="table-auto w-full bg-white rounded-lg shadow-md">
        <thead>
            <tr>
                <th class="px-4 py-2">Tanggal Pembayaran</th>
                <th class="px-4 py-2">Tunggakan</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Bukti Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pembayaranList as $pembayaran)
                <tr>
                    <td class="border px-4 py-2">{{ $pembayaran->waktuBayar }}</td>
                    <td class="border px-4 py-2">Rp {{ number_format($pembayaran->tunggakan, 2, ',', '.') }}</td>
                    <td class="border px-4 py-2">{{ $pembayaran->status ?? 'Belum Terverifikasi' }}</td>
                    <td class="border px-4 py-2">
                        @if ($pembayaran->buktiBayar)
                            <a href="{{ asset('storage/' . $pembayaran->buktiBayar) }}" target="_blank" class="text-blue-500">Lihat Bukti</a>
                        @else
                            <span>Belum Upload</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $pembayaranList->links() }}
    </div>
</div>
@endsection
