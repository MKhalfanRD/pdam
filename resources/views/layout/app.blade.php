<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">

    <script src="//unpkg.com/alpinejs" defer></script>

    @vite('resources/css/app.css')
</head>
<body >
    <nav class="px-4 py-4" x-data="{ navOpen: false }">
        <div class="container mx-auto">
            <!-- Small Screen Navbar -->
            <div class="flex items-center justify-between lg:hidden">
                <h1 class="text-xl text-white font-semibold">PDAM</h1>
                <button @click="navOpen = !navOpen">
                    <img src="{{ asset('icon/logo_pdam.png') }}" alt="toggle" class="w-7">
                </button>
            </div>
            <!-- Large Screen Navbar -->
            <div class="hidden lg:flex lg:items-center lg:justify-between">
                <div class="flex items-center gap-4">
                    <button @click="navOpen = !navOpen">
                        <img src="{{ asset('icon/logo_pdam.png') }}" alt="toggle" class="w-7">
                    </button>
                    <h1 class="text-xl text-white font-semibold">PDAM</h1>
                </div>
                <ul class="flex gap-10">
                    {{-- admin --}}
                    @if($role == 'admin')
                    <li class="font-bold text-sm text-gray-400"><a href="#">Home</a></li>
                    <li class="font-bold text-sm text-gray-400"><a href="#">About</a></li>
                    <li class="font-bold text-sm text-gray-400"><a href="#">Business</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="flex flex-col items-center gap-1"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                <span class="font-bold text-sm text-gray-400">Keluar</span>
                            </button>
                        </form>
                    </li>
                    {{-- operator --}}
                    @elseif ($role == 'operator')
                    <li class="font-bold text-sm text-gray-400"><a href="{{route('operator.index')}}">Home</a></li>
                    <li class="font-bold text-sm text-gray-400"><a href="{{route('operator.history')}}">History</a></li>
                    <li class="font-bold text-sm text-gray-400"><a href="#">Business</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="flex flex-col items-center gap-1"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                <span class="font-bold text-sm text-gray-400">Keluar</span>
                            </button>
                        </form>
                    </li>
                    {{-- warga --}}
                    @elseif($role == 'warga')
                    <li class="font-bold text-sm text-gray-400"><a href="{{route('warga.index')}}">Home</a></li>
                    <li class="font-bold text-sm text-gray-400"><a href="{{route('warga.history')}}">Riwayat</a></li>
                    <li class="font-bold text-sm text-gray-400"><a href="#">Profil</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="flex flex-col items-center gap-1"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                <span class="font-bold text-sm text-gray-400">Keluar</span>
                            </button>
                        </form>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div class="fixed bottom-0 right-0 left-0 p-4 lg:hidden bg-indigo-600 z-50" x-show="navOpen"
            x-transition.duration.500ms>
            <ul class="flex justify-between">
                {{-- admin --}}
                @if(Auth::user()->role == 'admin')
                <li>
                    <button class="flex flex-col items-center gap-1">
                        <span class="font-normaltext-base text-white">Home</span>
                    </button>
                </li>
                <li>
                    <button class="flex flex-col items-center gap-1">
                        <span class="font-normal text-base text-white">About</span>
                    </button>
                </li>
                <li>
                    <button class="flex flex-col items-center gap-1">
                        <span class="font-normal text-base text-white">Business</span>
                    </button>
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="flex flex-col items-center gap-1"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            <span class="font-normal text-base text-white">Keluar</span>
                        </button>
                    </form>
                </li>
                {{-- operator --}}
                @elseif(Auth::user()->role == 'operator')
                <li>
                    <button class="flex flex-col items-center gap-1">
                        <a href="{{route('operator.index')}}">
                            <span class="font-normal text-base text-white">Home</span>
                        </a>
                    </button>
                </li>
                <li>
                    <button class="flex flex-col items-center gap-1">
                        <a href="{{route('operator.history')}}">
                            <span class="font-normal text-base text-white">History</span>
                        </a>
                    </button>
                </li>
                <li>
                    <button class="flex flex-col items-center gap-1">
                        <span class="font-normal text-base text-white">Business</span>
                    </button>
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="flex flex-col items-center gap-1"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            <span class="font-normal text-base text-white">Keluar</span>
                        </button>
                    </form>
                </li>
                {{-- warga --}}
                @elseif(Auth::user()->role == 'warga')
                <li>
                    <button class="flex flex-col items-center gap-1">
                        <a href="{{route('warga.index')}}">
                            <span class="font-normal text-base text-white">Home</span>
                        </a>
                    </button>
                </li>
                <li>
                    <button class="flex flex-col items-center gap-1">
                        <a href="{{route('warga.history')}}">
                            <span class="font-normal text-base text-white">Riwayat</span>
                        </a>
                    </button>
                </li>
                <li>
                    <button class="flex flex-col items-center gap-1">
                        <span class="font-normal text-base text-white">Profil</span>
                    </button>
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="flex flex-col items-center gap-1"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            <span class="font-normal text-base text-white">Keluar</span>
                        </button>
                    </form>
                </li>
                @endif
            </ul>
        </div>
    </nav>

    <section>
        <div class="col-span-12 p-4">
            @yield('content')
        </div>
    </section>
</body>
</html>
