<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Restaurant Management') }}</title>

        <!-- Google Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles via Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Livewire Styles -->
        @livewireStyles        
    </head>
    @php
        $appSettings = \App\Models\RestaurantSetting::getAllSettings();
        $bgPattern = $appSettings['bg_pattern'] ?? 'red';
        $bgOpacity = $appSettings['bg_pattern_opacity'] ?? '0.06';

        $patternFile = match($bgPattern) {
            'orange' => 'images/food-pattern-orange.png',
            'slate' => 'images/food-pattern-slate.png',
            'original' => 'images/food-pattern.png',
            'none' => null,
            default => 'images/food-pattern-red.png',
        };
    @endphp
    <body
        class="font-inter antialiased bg-[#f8fafc] text-gray-700 min-h-screen"
        x-data="{ sidebarOpen: false }"
    >
        <!-- Application Wrapper -->
        <div class="flex h-[100dvh] overflow-hidden">

            <!-- Restaurant Sidebar -->
            <x-app.sidebar />

            <!-- Content area -->
            <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden bg-[#f8fafc]">

                @if($patternFile)
                <!-- Food & Restaurant Doodle Wallpaper Overlay (User's Food Pattern - 100% Full Screen Cover) -->
                <div 
                    class="fixed inset-0 pointer-events-none z-0 bg-repeat transition-opacity duration-300"
                    style="
                        background-image: url('{{ asset($patternFile) }}');
                        background-size: 380px auto;
                        opacity: {{ $bgOpacity }};
                    "
                    aria-hidden="true"
                ></div>
                @endif

                <!-- 2-Tier Restaurant Header (Highest z-index so dropdown floats above main content) -->
                <div class="relative z-40">
                    <x-app.header />
                </div>

                <!-- Main Content Slot -->
                <main class="grow p-3 sm:p-5 lg:p-8 max-w-[1600px] w-full mx-auto relative z-10">
                    {{ $slot }}
                </main>

            </div>

        </div>

        <x-thermal-invoice />
        @livewireScriptConfig
    </body>
</html>
