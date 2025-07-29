<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'NgajiTracker') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <div class="flex">
                @if(in_array(Auth::user()->role, ['admin', 'guru']))
                    <aside class="w-64 bg-white border-r border-gray-200 flex-shrink-0 hidden md:flex md:flex-col">
                        <div class="h-16 flex items-center justify-center flex-shrink-0 px-4 border-b">
                            <a href="{{ route('dashboard') }}">
                                <x-application-logo class="block h-10 w-auto" />
                            </a>
                        </div>
                        <div class="flex-1 overflow-y-auto">
                            <nav class="mt-5 px-2 space-y-1">
                                <x-nav-link-side :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                    <x-icons.home class="w-5 h-5 mr-3"/> Dashboard
                                </x-nav-link-side>

                                {{-- [DIPERBAIKI] Menu KHUSUS Admin --}}
                                @if(Auth::user()->role == 'admin')
                                    <x-nav-link-side :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                        <x-icons.users class="w-5 h-5 mr-3"/> Manajemen User
                                    </x-nav-link-side>
                                    
                                    {{-- Link Keuangan dengan gaya yang sama --}}
                                    <x-nav-link-side :href="route('admin.finance.index')" :active="request()->routeIs('admin.finance.index*')">
                                        {{-- Ganti x-icons.money dengan nama komponen ikon-mu --}}
                                        <x-icons.money class="w-5 h-5 mr-3"/> Keuangan
                                    </x-nav-link-side>
                                @endif

                                {{-- Menu Bersama Admin & Guru --}}
                                <x-nav-link-side :href="route('progress.create')" :active="request()->routeIs('progress.create')">
                                    <x-icons.pencil-alt class="w-5 h-5 mr-3"/> Progress Umum
                                </x--nav-link-side>
                                <x-nav-link-side :href="route('tahfizh.create')" :active="request()->routeIs('tahfizh.create')">
                                    <x-icons.book-open class="w-5 h-5 mr-3"/> Setoran Tahfizh
                                </x-nav-link-side>
                            </nav>
                        </div>
                    </aside>
                @endif

                <div class="flex-1 flex flex-col w-full">
                    @include('layouts.navigation')

                    @if (isset($header))
                        <header class="bg-white shadow-sm">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endif

                    <main class="flex-1 p-6">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
        
        @stack('scripts')
    </body>
</html>