@extends('layout.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-lg w-full">
    <h2 class="text-2xl font-bold text-center mb-4">Upload Bukti Pembayaran</h2>

    @if (session('warning'))
    <div class="bg-yellow-200 text-yellow-800 p-3 rounded mb-4">
        {{ session('warning') }}
    </div>
    @endif

    @if(session('success'))
        <div class="bg-green-500 text-white p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h3 class="text-lg font-bold mb-1 text-black">
        {{ \Carbon\Carbon::createFromFormat('Y-m', now()->format('Y-m'))->locale('id')->translatedFormat('F Y') }}
    </h3>

    @if (!$pemakaianAir->isEmpty())
    @foreach ($pemakaianAir as $item)
        <div class="mb-4">
            <p class="block text-sm font-medium text-gray-700">Jumlah Tagihan:
                @if (is_null($item->tagihanAir))
                    <span class="text-red-500">nilai belum diinput operator</span>
                @else
                    {{ $item->tagihanAir }}
                @endif
            </p>
            <p class="block text-sm font-medium text-gray-700">Pemakaian Lama: {{ $item->pemakaianLama }}</p>
            <p class="block text-sm font-medium text-gray-700">Pemakaian Baru: {{ $item->pemakaianBaru }}</p>
        </div>

        <input type="hidden" name="pemakaianAir_id" value="{{ $item->pemakaianAir_id }}">
    @endforeach
    @else
    @endif


    <form action="{{ route('pembayaran.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label for="buktiBayar" class="block text-sm font-medium text-gray-700">Upload Bukti Pembayaran</label>
            <input id="buktiBayar" name="buktiBayar" type="file" accept="image/*" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-md bg-gray-50 focus:outline-none">
                @error('buktiBayar')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="komentar" class="block text-sm font-medium text-gray-700 mb-2">Komentar</label>
            <textarea name="komentar" id="komentar" class="block w-full rounded-md bg-gray-200 text-gray-900 px-3.5 py-2 mt-2" rows="3"></textarea>
            @error('komentar')
            <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mt-6 gap-2 flex justify-between">
            <a href="{{ route('warga.index') }}">
                <button type="button" class="bg-gray-400 px-4 py-2 text-white rounded-md">Kembali</button>
            </a>
            <button type="submit"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none">Upload</button>
        </div>
    </form>
</div>
@endsection
