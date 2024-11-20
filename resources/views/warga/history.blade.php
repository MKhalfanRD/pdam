@extends('layout.app')

@section('content')

    <div class="container mx-auto mt-4">
        <h1 class="text-2xl text-center font-bold mb-4">Riwayat Pembayaran</h1>

        <!-- Cek apakah ada histori pembayaran -->
        @if ($historiPembayaran->isEmpty())
            <div class="text-center text-gray-500">
                <p>Belum ada riwayat pembayaran.</p>
            </div>
        @else
            <div class="container mx-auto">
                    <table class="container mx-auto table w-full max-w-4xl">
                        <!-- Head -->
                        <thead>
                            <tr class="text-gray-100">
                                <th class="px-4 py-2">Bulan</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <!-- Body -->
                        <tbody>
                            @foreach ($historiPembayaran as $pembayaran)
                                <tr class="text-gray-200">
                                    <td class="">
                                        {{ \Carbon\Carbon::parse($pembayaran->waktuBayar)->format('F Y') }}
                                    </td>
                                    <td class="">
                                        <span
                                            class="{{ $pembayaran->status === 'sudah bayar' ? 'text-green-500' : 'text-red-500' }}">
                                            {{ ucfirst($pembayaran->status) }}
                                        </span>
                                    </td>
                                    <td class="">
                                        <a href="{{ route('warga.detailHistory', $pembayaran->id) }}"
                                            class="bg-indigo-600 text-white p-2 rounded-lg">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
            </div>



            <!-- Pagination -->
            <div class="mt-4">
                {{ $historiPembayaran->links() }}
            </div>
        @endif
    </div>
@endsection
