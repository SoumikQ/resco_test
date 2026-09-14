<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles        

        <script>
            if (localStorage.getItem('dark-mode') === 'false' || !('dark-mode' in localStorage)) {
                document.querySelector('html').classList.remove('dark');
                document.querySelector('html').style.colorScheme = 'light';
            } else {
                document.querySelector('html').classList.add('dark');
                document.querySelector('html').style.colorScheme = 'dark';
            }
        </script>
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
    <body class="font-inter antialiased bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400">

        <main class="bg-white dark:bg-gray-900">

            <div class="relative flex">

                <!-- Content (Left Half with Food Pattern Background) -->
                <div class="w-full md:w-1/2 relative bg-[#f8fafc] overflow-hidden">

                    @if($patternFile)
                    <!-- Food & Restaurant Doodle Wallpaper Overlay -->
                    <div 
                        class="absolute inset-0 pointer-events-none z-0 bg-repeat transition-opacity duration-300"
                        style="
                            background-image: url('{{ asset($patternFile) }}');
                            background-size: 360px auto;
                            opacity: {{ $bgOpacity }};
                        "
                        aria-hidden="true"
                    ></div>
                    @endif

                    <div class="min-h-[100dvh] h-full flex flex-col after:flex-1 relative z-10">

                        <!-- Header Spacer -->
                        <div class="flex-1"></div>

                        <div class="max-w-sm mx-auto w-full px-4 py-8">
                            {{ $slot }}
                        </div>

                    </div>

                </div>

                <!-- Image (Right Half) -->
                <div class="hidden md:block absolute top-0 bottom-0 right-0 md:w-1/2" aria-hidden="true">
                    <img class="object-cover object-center w-full h-full" src="{{ asset('images/auth-image.jpg') }}" width="760" height="1024" alt="Authentication image" />
                </div>

            </div>

        </main> 

        @livewireScriptConfig
    </body>
</html>
