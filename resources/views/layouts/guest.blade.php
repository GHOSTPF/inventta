<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50">
            <a href="/" class="flex items-center gap-3">
                <x-application-logo class="w-12 h-12" />
                <span class="text-3xl font-bold tracking-tight text-ink">Inventta</span>
            </a>
            <p class="mt-1 text-xs tracking-[0.25em] uppercase text-gray-400">Gestão de estoque inteligente</p>

            <div class="w-full sm:max-w-md mt-8 px-8 py-8 bg-white border border-gray-100 shadow-sm overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
