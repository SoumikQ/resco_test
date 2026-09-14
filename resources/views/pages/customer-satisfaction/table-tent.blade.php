<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print Table Tent Card - {{ $config['restaurant_name'] ?? 'RESCO' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-serif-title {
            font-family: 'Cinzel', serif;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
            }
            .print-card-container {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                page-break-inside: avoid;
            }
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen p-4 sm:p-8 flex flex-col items-center justify-start antialiased">

    <!-- Top Toolbar (Hidden when printing) -->
    <div class="no-print max-w-4xl w-full bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('restaurant.customer-satisfaction') }}"
                class="inline-flex items-center text-xs font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-lg transition-colors"
            >
                ← Back to CSAT
            </a>
            <span class="text-sm font-bold text-gray-800 hidden sm:inline">
                Table Tent Standee Card
            </span>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- QR Style Switcher -->
            <form method="GET" action="{{ route('restaurant.customer-satisfaction.table-tent') }}" class="flex items-center gap-2">
                <label class="text-xs font-bold text-gray-600">QR Style:</label>
                <select 
                    name="qr_style" 
                    onchange="this.form.submit()" 
                    class="text-xs bg-gray-50 border border-gray-300 rounded-lg px-2.5 py-1.5 font-semibold text-gray-800 focus:ring-1 focus:ring-[#ea580c]"
                >
                    <option value="dots" {{ ($qrStyle ?? 'dots') === 'dots' ? 'selected' : '' }}>🟠 Dots Gradient</option>
                    <option value="curves" {{ ($qrStyle ?? '') === 'curves' ? 'selected' : '' }}>⚫ Fluid Curves</option>
                    <option value="gold" {{ ($qrStyle ?? '') === 'gold' ? 'selected' : '' }}>🟡 Royal Gold</option>
                    <option value="emerald" {{ ($qrStyle ?? '') === 'emerald' ? 'selected' : '' }}>🟢 Fresh Emerald</option>
                </select>
            </form>

            <!-- Download SVG QR -->
            <a 
                href="{{ route('restaurant.customer-satisfaction.qr-download', ['qr_style' => $qrStyle ?? 'dots']) }}"
                class="inline-flex items-center justify-center bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold px-3 py-1.5 rounded-lg shadow-2xs transition-colors"
                title="Download Vector SVG QR"
            >
                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download QR (SVG)
            </a>

            <!-- Print Card -->
            <button 
                onclick="window.print()"
                class="inline-flex items-center justify-center bg-[#ea580c] hover:bg-[#c2410c] text-white text-xs font-bold px-4 py-1.5 rounded-lg shadow-sm transition-colors cursor-pointer"
            >
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Standee (Ctrl + P)
            </button>
        </div>
    </div>

    <!-- Cards Preview Area (Formatted for standard Acrylic Table Tent Stand 4x6" / A6) -->
    <div class="flex flex-wrap items-center justify-center gap-8 w-full max-w-4xl">
        
        <!-- ================= CARD 1: LUXURY GOURMET DARK & GOLD (MODERN RESTAURANT STYLE) ================= -->
        <div class="print-card-container w-[310px] sm:w-[360px] h-[520px] bg-gradient-to-b from-[#18181b] via-[#09090b] to-[#18181b] text-white rounded-2xl shadow-xl overflow-hidden relative flex flex-col justify-between p-5 sm:p-6 border-2 border-amber-500/30">
            
            <!-- Gold Corner Borders -->
            <div class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-amber-400"></div>
            <div class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-amber-400"></div>
            <div class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-amber-400"></div>
            <div class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-amber-400"></div>

            <!-- Top Restaurant Brand -->
            <div class="text-center pt-2 space-y-1">
                <div class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gradient-to-tr from-amber-500 to-amber-300 text-black font-black mb-1 shadow-md shadow-amber-500/20">
                    <svg class="w-5 h-5 text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="font-serif-title text-xl font-bold tracking-widest text-amber-300 uppercase">
                    {{ $config['restaurant_name'] ?? 'RESCO RESTAURANT' }}
                </h3>
                <p class="text-[10px] text-zinc-400 uppercase tracking-widest font-medium">
                    {{ $config['tagline'] ?? 'Fine Dine & Hospitality' }}
                </p>
            </div>

            <!-- Golden 5 Stars -->
            <div class="text-center">
                <div class="text-amber-400 text-lg tracking-widest">
                    ★★★★★
                </div>
                <h4 class="text-base font-extrabold text-white tracking-tight mt-0.5">
                    How was your experience?
                </h4>
                <p class="text-[11px] text-zinc-300 mt-0.5">
                    Scan below & tell us what you loved!
                </p>
            </div>

            <!-- Center Modern QR Code Container -->
            <div class="flex flex-col items-center justify-center my-auto">
                <div class="relative bg-white p-3.5 rounded-2xl shadow-2xl border-4 border-amber-400/40">
                    <!-- QR Code SVG with embedded vector center badge -->
                    <div class="w-40 h-40 flex items-center justify-center [&>svg]:w-full [&>svg]:h-full">
                        {!! $qrSvg !!}
                    </div>
                </div>

                <div class="mt-2.5 text-[10px] font-semibold text-zinc-400 uppercase tracking-widest">
                    Scan to Rate & Review
                </div>
            </div>

            <!-- Bottom Instruction & Footer -->
            <div class="text-center pt-2 border-t border-zinc-800 text-[10px] text-zinc-400 space-y-0.5">
                <p class="font-medium text-zinc-300">
                    📸 Open phone camera &rarr; Scan QR &rarr; Leave a Review
                </p>
                <p class="text-[9px] text-zinc-500">
                    Your feedback helps us continuously improve our service.
                </p>
            </div>

        </div>

        <!-- ================= CARD 2: PRISTINE ELEGANT WHITE & ORANGE (CONTRAST ALTERNATIVE) ================= -->
        <div class="print-card-container w-[310px] sm:w-[360px] h-[520px] bg-white text-gray-900 rounded-2xl shadow-xl overflow-hidden relative flex flex-col justify-between p-5 sm:p-6 border-2 border-orange-500/30">
            
            <!-- Orange Corner Accents -->
            <div class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-[#ea580c]"></div>
            <div class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-[#ea580c]"></div>
            <div class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-[#ea580c]"></div>
            <div class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-[#ea580c]"></div>

            <!-- Top Restaurant Brand -->
            <div class="text-center pt-2 space-y-1">
                <div class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-[#ea580c] text-white font-black mb-1 shadow-md shadow-orange-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="text-lg font-black tracking-tight text-gray-900 uppercase">
                    {{ $config['restaurant_name'] ?? 'RESCO RESTAURANT' }}
                </h3>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold">
                    {{ $config['tagline'] ?? 'Authentic Food & Fine Dine' }}
                </p>
            </div>

            <!-- Golden 5 Stars -->
            <div class="text-center">
                <div class="text-amber-400 text-lg tracking-widest">
                    ★★★★★
                </div>
                <h4 class="text-base font-extrabold text-gray-900 tracking-tight mt-0.5">
                    Rate Your Dining Experience!
                </h4>
                <p class="text-[11px] text-gray-500 mt-0.5">
                    We strive to make every meal memorable.
                </p>
            </div>

            <!-- Center Modern QR Code Container -->
            <div class="flex flex-col items-center justify-center my-auto">
                <div class="relative bg-orange-50/50 p-3.5 rounded-2xl shadow-inner border-2 border-orange-200">
                    <!-- QR Code SVG with embedded vector center badge -->
                    <div class="w-40 h-40 flex items-center justify-center [&>svg]:w-full [&>svg]:h-full">
                        {!! $qrSvg !!}
                    </div>
                </div>

                <div class="mt-2.5 text-[10px] font-bold text-[#ea580c] uppercase tracking-wider">
                    Scan to Review
                </div>
            </div>

            <!-- Bottom Instruction & Footer -->
            <div class="text-center pt-2 border-t border-gray-100 text-[10px] text-gray-500 space-y-0.5">
                <p class="font-bold text-gray-700">
                    📱 Open Camera &rarr; Scan QR &rarr; Leave Rating
                </p>
                <p class="text-[9px] text-gray-400">
                    Thank you for dining with us today!
                </p>
            </div>

        </div>

    </div>

</body>
</html>
