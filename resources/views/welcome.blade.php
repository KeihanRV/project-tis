<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'TrashTrack API') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif

        <style>
            body {
                font-family: 'Instrument Sans', sans-serif;
            }
        </style>
    </head>
    <body class="bg-[#F8FAFC] text-slate-800 antialiased min-h-screen flex flex-col items-center justify-center selection:bg-blue-500 selection:text-white">
        
        <div class="max-w-lg w-full px-8 py-12 bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 text-center mx-4 transform transition-all hover:-translate-y-1 hover:shadow-[0_8px_40px_rgb(0,0,0,0.08)]">
            
            <!-- <div class="mb-8 flex justify-center">
                <img src="{{ asset('storage/tr.png') }}" alt="TrashTrack Logo" class="h-28 object-contain drop-shadow-sm">
            </div> -->

            <h1 class="text-3xl font-bold text-slate-900 tracking-tight mb-4">
                Welcome to TrashTrack API
            </h1>
            
            <p class="text-base text-slate-500 mb-10 leading-relaxed px-4">
                Sistem REST API terpadu untuk melaporkan, melacak, dan mengelola lokasi sampah secara cepat, efisien, dan real-time.
            </p>

            <a href="/api/documentation" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-full transition-all duration-200 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 w-full sm:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Read Documentation
            </a>

        </div>

        <div class="mt-8 text-sm text-slate-400">
            &copy; {{ date('Y') }} TrashTrack. All rights reserved.
        </div>

    </body>
</html>