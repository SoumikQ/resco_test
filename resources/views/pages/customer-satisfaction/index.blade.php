<x-app-layout>
    @section('page_title', 'Customer Satisfaction')

    <div class="space-y-6">

        <!-- Top Header & Action Buttons -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1 border-b border-gray-200/70">
            <div>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-[#ea580c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Customer Satisfaction (CSAT)
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">Live guest dining feedback, star ratings, review QR code and table standee templates.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Open Customer Review Portal -->
                <a 
                    href="{{ route('restaurant.review') }}" 
                    target="_blank"
                    class="inline-flex items-center justify-center bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold px-3.5 py-2 rounded-md shadow-2xs transition-colors cursor-pointer"
                    title="Open the customer-facing mobile review page"
                >
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Test Review Page
                </a>

                <!-- Download Standalone QR -->
                <a 
                    href="{{ route('restaurant.customer-satisfaction.qr-download') }}"
                    class="inline-flex items-center justify-center bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold px-3.5 py-2 rounded-md shadow-2xs transition-colors cursor-pointer"
                    title="Download Vector SVG QR Code"
                >
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download QR (SVG)
                </a>

                <!-- Print Table Tent Standee Card Template -->
                <a 
                    href="{{ route('restaurant.customer-satisfaction.table-tent') }}"
                    class="inline-flex items-center justify-center bg-[#ea580c] hover:bg-[#c2410c] text-white text-xs font-semibold px-4 py-2 rounded-md shadow-2xs transition-colors cursor-pointer"
                    title="Print Restaurant Table Tent Standee Card"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Table Tent Card Template
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium rounded-lg flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Prominent Review QR Code & Table Standee Banner Card -->
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-sm overflow-hidden p-4 sm:p-7 lg:p-9 relative">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-center">
                
                <!-- Left: Info & Call to Action (Left Aligned with Generous Breathing Spacing) -->
                <div class="lg:col-span-8 flex flex-col justify-between text-left space-y-6">
                    
                    <!-- 1. Top Status Badge, Heading, and Description -->
                    <div class="space-y-3">
                        <div>
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-orange-50 text-[#ea580c] border border-orange-200 shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-[#ea580c] animate-pulse"></span>
                                Table Review QR Code System Active
                            </div>
                        </div>
                        
                        <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight leading-snug pt-1">
                            Place QR Codes on Dining Tables to Collect Instant Reviews
                        </h2>
                        
                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed max-w-xl pt-1">
                            Customers simply open their mobile camera, scan the table QR code, and submit their <strong>1 to 5 star rating</strong> and feedback comment. All submitted reviews sync immediately to this dashboard in real time.
                        </p>
                    </div>

                    <!-- 2. Action Buttons Row (Print, Download, and URL actions with proper spacing) -->
                    <div class="flex flex-wrap items-center justify-start gap-3.5 pt-1">
                        <a 
                            href="{{ route('restaurant.customer-satisfaction.table-tent', ['qr_style' => $qrStyle ?? 'dots']) }}"
                            class="inline-flex items-center gap-2 bg-[#ea580c] hover:bg-[#c2410c] text-white text-xs font-bold px-4 py-2.5 rounded-lg shadow-sm transition-all hover:shadow"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Print Table Standee Card Template
                        </a>

                        <a 
                            href="{{ route('restaurant.customer-satisfaction.qr-download', ['qr_style' => $qrStyle ?? 'dots']) }}"
                            class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-semibold px-4 py-2.5 rounded-lg transition-all"
                        >
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download QR Image (.SVG)
                        </a>

                        <a 
                            href="{{ route('restaurant.review') }}" 
                            target="_blank"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#ea580c] hover:underline bg-orange-50/80 border border-orange-200 px-3.5 py-2.5 rounded-lg transition-colors"
                        >
                            <span>Open URL: <code class="font-mono text-[11px] font-bold text-gray-800">{{ route('restaurant.review') }}</code></span>
                            <span class="text-sm">↗</span>
                        </a>
                    </div>

                    <!-- 3. Modern QR Code Style Selector Bar (Generous Top Divider & High-Contrast Pills) -->
                    <div class="pt-5 mt-2 border-t border-gray-200/90 flex flex-wrap items-center gap-2.5">
                        <span class="text-[11px] font-extrabold text-gray-500 uppercase tracking-wider mr-1">
                            ✨ QR DESIGN STYLE:
                        </span>
                        
                        <!-- 1. Dots Gradient -->
                        @php $isDots = ($qrStyle ?? 'dots') === 'dots'; @endphp
                        <a 
                            href="{{ route('restaurant.customer-satisfaction', ['qr_style' => 'dots', 'rating' => $ratingFilter]) }}" 
                            class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all flex items-center gap-2 cursor-pointer shadow-2xs hover:opacity-90"
                            style="{{ $isDots ? 'background: #ea580c !important; color: #ffffff !important; border: 1.5px solid #ea580c !important; box-shadow: 0 2px 6px rgba(234, 88, 12, 0.35) !important;' : 'background: #f8fafc !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;' }}"
                            title="Modern circular dot-matrix with terracotta sunset gradient"
                        >
                            <span class="w-2.5 h-2.5 rounded-full inline-block shrink-0" style="background: {{ $isDots ? '#ffffff' : '#ea580c' }} !important;"></span>
                            <span>Dots Gradient</span>
                            @if($isDots)<span class="text-[11px] font-black">✓</span>@endif
                        </a>

                        <!-- 2. Fluid Curves -->
                        @php $isCurves = ($qrStyle ?? '') === 'curves'; @endphp
                        <a 
                            href="{{ route('restaurant.customer-satisfaction', ['qr_style' => 'curves', 'rating' => $ratingFilter]) }}" 
                            class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all flex items-center gap-2 cursor-pointer shadow-2xs hover:opacity-90"
                            style="{{ $isCurves ? 'background: #18181b !important; color: #ffffff !important; border: 1.5px solid #18181b !important; box-shadow: 0 2px 6px rgba(24, 24, 27, 0.35) !important;' : 'background: #f8fafc !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;' }}"
                            title="Fluid squircle rounded modules with organic leaf corners"
                        >
                            <span class="w-2.5 h-2.5 rounded-full inline-block shrink-0" style="background: {{ $isCurves ? '#ffffff' : '#18181b' }} !important;"></span>
                            <span>Fluid Curves</span>
                            @if($isCurves)<span class="text-[11px] font-black">✓</span>@endif
                        </a>

                        <!-- 3. Royal Gold -->
                        @php $isGold = ($qrStyle ?? '') === 'gold'; @endphp
                        <a 
                            href="{{ route('restaurant.customer-satisfaction', ['qr_style' => 'gold', 'rating' => $ratingFilter]) }}" 
                            class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all flex items-center gap-2 cursor-pointer shadow-2xs hover:opacity-90"
                            style="{{ $isGold ? 'background: #b45309 !important; color: #ffffff !important; border: 1.5px solid #b45309 !important; box-shadow: 0 2px 6px rgba(180, 83, 9, 0.35) !important;' : 'background: #f8fafc !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;' }}"
                            title="Royal Champagne Gold gradient with circular targets"
                        >
                            <span class="w-2.5 h-2.5 rounded-full inline-block shrink-0" style="background: {{ $isGold ? '#ffffff' : '#d97706' }} !important;"></span>
                            <span>Royal Gold</span>
                            @if($isGold)<span class="text-[11px] font-black">✓</span>@endif
                        </a>

                        <!-- 4. Fresh Emerald -->
                        @php $isEmerald = ($qrStyle ?? '') === 'emerald'; @endphp
                        <a 
                            href="{{ route('restaurant.customer-satisfaction', ['qr_style' => 'emerald', 'rating' => $ratingFilter]) }}" 
                            class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all flex items-center gap-2 cursor-pointer shadow-2xs hover:opacity-90"
                            style="{{ $isEmerald ? 'background: #047857 !important; color: #ffffff !important; border: 1.5px solid #047857 !important; box-shadow: 0 2px 6px rgba(4, 120, 87, 0.35) !important;' : 'background: #f8fafc !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;' }}"
                            title="Fresh mint and deep emerald gradient"
                        >
                            <span class="w-2.5 h-2.5 rounded-full inline-block shrink-0" style="background: {{ $isEmerald ? '#ffffff' : '#059669' }} !important;"></span>
                            <span>Fresh Emerald</span>
                            @if($isEmerald)<span class="text-[11px] font-black">✓</span>@endif
                        </a>
                    </div>
                </div>

                <!-- Right: Modern Styled QR Code Preview with Viewfinder Framing -->
                <div class="lg:col-span-4 flex flex-col items-center lg:items-end justify-center">
                    <div class="flex flex-col items-center">
                        <div class="relative bg-white p-4 rounded-2xl shadow-lg border-2 border-orange-200/80 group transition-transform hover:scale-[1.02]">
                            
                            <!-- Viewfinder Corner Brackets -->
                            <span class="absolute top-2 left-2 w-3.5 h-3.5 border-t-2 border-l-2 border-[#ea580c] rounded-tl pointer-events-none"></span>
                            <span class="absolute top-2 right-2 w-3.5 h-3.5 border-t-2 border-r-2 border-[#ea580c] rounded-tr pointer-events-none"></span>
                            <span class="absolute bottom-2 left-2 w-3.5 h-3.5 border-b-2 border-l-2 border-[#ea580c] rounded-bl pointer-events-none"></span>
                            <span class="absolute bottom-2 right-2 w-3.5 h-3.5 border-b-2 border-r-2 border-[#ea580c] rounded-br pointer-events-none"></span>

                            <!-- Floating SCAN ME Tag -->
                            <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 bg-gradient-to-r from-[#ea580c] to-amber-500 text-white text-[9px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full shadow-xs border border-white pointer-events-none flex items-center gap-1">
                                <span>★</span> SCAN ME
                            </div>

                            <!-- Modern Vector QR Code (Center logo embedded inside SVG) -->
                            <div class="w-44 h-44 flex items-center justify-center [&>svg]:w-full [&>svg]:h-full pt-1">
                                {!! $qrSvg !!}
                            </div>
                        </div>

                        <div class="text-center mt-2.5">
                            <span class="text-[11px] font-extrabold text-gray-800 flex items-center justify-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                Live Customer Review QR
                            </span>
                            <span class="text-[10px] text-gray-400 font-semibold block">
                                Style: {{ ucfirst($qrStyle ?? 'dots') }} (25% Error Correction)
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- 4 Live Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <!-- Overall Score -->
            <div class="bg-white rounded-lg border border-gray-200/90 shadow-2xs p-5">
                <div class="text-xs font-semibold text-gray-500">Overall CSAT Score</div>
                <div class="text-2xl font-black text-gray-900 mt-1 flex items-center">
                    {{ $stats['overall_score'] }} 
                    <span class="text-amber-500 text-xl ml-1.5">★</span>
                    <span class="text-xs text-gray-400 font-normal ml-2">/ 5.0</span>
                </div>
                <div class="mt-2 text-[11px] text-gray-500 flex items-center gap-1 text-amber-600 font-semibold">
                    <span>★★★★★</span>
                    <span class="text-gray-400 font-normal">Based on all guest reviews</span>
                </div>
            </div>

            <!-- Total Reviews -->
            <div class="bg-white rounded-lg border border-gray-200/90 shadow-2xs p-5">
                <div class="text-xs font-semibold text-gray-500">Total Reviews Submitted</div>
                <div class="text-2xl font-black text-gray-900 mt-1">{{ $stats['total_reviews'] }}</div>
                <div class="mt-2 text-[11px] text-gray-500">
                    Direct feedback from restaurant guests
                </div>
            </div>

            <!-- Positive Feedback Rate -->
            <div class="bg-white rounded-lg border border-gray-200/90 shadow-2xs p-5">
                <div class="text-xs font-semibold text-gray-500">Positive Feedback Rate</div>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['satisfaction_rate'] }}</div>
                <div class="mt-2 text-[11px] text-gray-500">
                    {{ $stats['positive_reviews'] }} reviews rated 4★ & 5★
                </div>
            </div>

            <!-- Average Star Distribution -->
            <div class="bg-white rounded-lg border border-gray-200/90 shadow-2xs p-5">
                <div class="text-xs font-semibold text-gray-500">Top Rating Tier</div>
                <div class="text-2xl font-black text-[#ea580c] mt-1">
                    {{ $stats['star_counts'][5] }} <span class="text-xs font-bold text-gray-500">5-Star Reviews</span>
                </div>
                <div class="mt-2 text-[11px] text-gray-500">
                    Highest tier guest experiences
                </div>
            </div>

        </div>

        <!-- Rating Distribution Bars & Filter -->
        <div class="bg-white rounded-xl border border-gray-200/90 shadow-2xs p-4 sm:p-5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                <span>Star Ratings Breakdown & Distribution</span>
                <span class="text-[11px] text-gray-400 font-normal">Click any rating to filter reviews below</span>
            </h3>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2.5 sm:gap-3">
                @foreach([5, 4, 3, 2, 1] as $star)
                @php
                    $count = $stats['star_counts'][$star] ?? 0;
                    $percent = $stats['total_reviews'] > 0 ? round(($count / $stats['total_reviews']) * 100) : 0;
                    $isActive = request('rating') == $star;
                @endphp
                <a 
                    href="{{ $isActive ? route('restaurant.customer-satisfaction') : route('restaurant.customer-satisfaction', ['rating' => $star]) }}"
                    class="p-3 rounded-xl border transition-all {{ $isActive ? 'border-[#ea580c] bg-orange-50/50 shadow-2xs' : 'border-gray-200/80 bg-gray-50/60 hover:bg-white hover:border-gray-300' }}"
                >
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-gray-800 flex items-center gap-1">
                            <span>{{ $star }}</span>
                            <span class="text-amber-500">★</span>
                        </span>
                        <span class="text-xs font-extrabold text-gray-900">{{ $count }}</span>
                    </div>

                    <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                        <div class="h-1.5 rounded-full {{ $star >= 4 ? 'bg-emerald-500' : ($star === 3 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ $percent }}%"></div>
                    </div>

                    <div class="mt-1.5 text-[10px] text-gray-500 flex items-center justify-between">
                        <span>{{ $percent }}% share</span>
                        @if($isActive)
                            <span class="font-bold text-[#ea580c]">Filtered</span>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Reviews Feed -->
        <div class="bg-white rounded-lg border border-gray-200/90 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-gray-900 tracking-tight flex items-center gap-2">
                        <span>Guest Reviews Feed</span>
                        @if(request('rating'))
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-orange-100 text-[#ea580c]">
                                Showing {{ request('rating') }}★ Only
                            </span>
                        @endif
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">Real feedback submitted by restaurant guests</p>
                </div>

                <div class="flex items-center gap-2">
                    @if(request('rating'))
                        <a 
                            href="{{ route('restaurant.customer-satisfaction') }}"
                            class="text-xs text-red-600 hover:underline font-semibold"
                        >
                            ✕ Clear Filter
                        </a>
                    @endif
                    <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2.5 py-1 rounded">
                        {{ $reviews->count() }} Reviews
                    </span>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($reviews as $r)
                <div class="p-5 hover:bg-gray-50/50 transition-colors">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span class="font-extrabold text-gray-900 text-sm">
                                {{ $r->customer_name ?: 'Valued Guest' }}
                            </span>

                            @if($r->phone)
                                <span class="text-xs px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-semibold border border-blue-200/70 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    {{ $r->phone }}
                                </span>
                            @endif

                            @if($r->attendant)
                                <span class="text-xs text-gray-500">
                                    • Attendant: <strong class="text-gray-700 font-semibold">{{ $r->attendant }}</strong>
                                </span>
                            @endif
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-400">
                                {{ $r->created_at ? $r->created_at->format('d M Y, h:i A') : 'Recent' }}
                            </span>

                            <!-- Delete Review Button -->
                            <form 
                                method="POST" 
                                action="{{ route('restaurant.customer-satisfaction.review.delete', $r->id) }}"
                                onsubmit="return confirm('Are you sure you want to delete this review?');"
                                class="inline"
                            >
                                @csrf
                                <button 
                                    type="submit" 
                                    class="text-gray-400 hover:text-red-600 p-1 transition-colors cursor-pointer"
                                    title="Delete Review"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Stars -->
                    <div class="flex items-center text-amber-400 text-sm mb-2">
                        @for($i = 0; $i < $r->rating; $i++)
                            <span>★</span>
                        @endfor
                        @for($i = $r->rating; $i < 5; $i++)
                            <span class="text-gray-200">★</span>
                        @endfor
                        <span class="ml-2 text-xs font-bold text-gray-700">
                            {{ $r->rating }}.0
                        </span>
                    </div>

                    <!-- Comment -->
                    @if($r->comment)
                        <p class="text-xs text-gray-700 leading-relaxed bg-gray-50/70 p-3 rounded-lg border border-gray-100">
                            "{{ $r->comment }}"
                        </p>
                    @else
                        <p class="text-xs text-gray-400 italic">
                            No written comment provided.
                        </p>
                    @endif
                </div>
                @empty
                <div class="py-12 text-center text-gray-400 text-xs">
                    No customer reviews found matching the selected filter.
                </div>
                @endforelse
            </div>
        </div>

    </div>
</x-app-layout>
