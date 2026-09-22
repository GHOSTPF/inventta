<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div x-data="{ open: false }" class="min-h-screen">
            @include('layouts.navigation')

            <div class="lg:pl-64">
                {{-- Barra superior (mobile) --}}
                <div class="lg:hidden sticky top-0 z-30 flex items-center gap-3 bg-ink px-4 py-3">
                    <button @click="open = true" class="text-white" aria-label="Abrir menu">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <x-application-logo class="h-6 w-6" />
                    <span class="font-semibold text-white">Inventta</span>
                </div>

                <main class="mx-auto max-w-7xl px-4 py-8 sm:px-8 lg:py-12">
                    @isset($header)
                        <header class="mb-8 flex flex-wrap items-center justify-between gap-4">
                            {{ $header }}
                        </header>
                    @endisset

                    @if (session('status'))
                        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
