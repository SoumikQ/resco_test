<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Review - {{ $config['restaurant_name'] ?? 'RESCO Restaurant' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>
<body class="bg-[#f8fafc] text-gray-800 min-h-screen flex flex-col justify-between antialiased selection:bg-orange-500 selection:text-white relative">

    <!-- Food & Restaurant Doodle Wallpaper Overlay -->
    <div 
        class="fixed inset-0 pointer-events-none z-0 bg-repeat"
        style="
            background-image: url('{{ asset('images/food-pattern-red.png') }}');
            background-size: 320px auto;
            opacity: 0.06;
        "
        aria-hidden="true"
    ></div>

    <!-- Top Brand Bar -->
    <header class="bg-white/95 backdrop-blur-xs border-b border-gray-200/80 sticky top-0 z-20 shadow-2xs">
        <div class="max-w-md mx-auto px-4 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#ea580c] to-[#f97316] flex items-center justify-center text-white shadow-sm shadow-orange-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-extrabold text-gray-900 leading-tight tracking-tight">
                        {{ $config['restaurant_name'] ?? 'RESCO Restaurant' }}
                    </h1>
                    <p class="text-[11px] text-gray-500 font-medium">
                        {{ $config['tagline'] ?? 'Authentic Food & Fine Dine' }}
                    </p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-md w-full mx-auto p-4 sm:p-5 flex-1 flex flex-col justify-center">

        @if(session('review_submitted'))
            <!-- Celebratory Success Card -->
            <div class="bg-white rounded-2xl border border-emerald-200/80 shadow-md p-6 sm:p-8 text-center space-y-4 my-auto">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto shadow-inner">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <div>
                    <h2 class="text-xl font-black text-gray-900 tracking-tight">Thank You So Much! ❤️</h2>
                    <p class="text-xs text-gray-600 mt-2 leading-relaxed">
                        Your valuable feedback has been submitted successfully to <strong>{{ $config['restaurant_name'] ?? 'our team' }}</strong>. It helps us serve you even better on your next visit!
                    </p>
                </div>

                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200/60 text-left text-xs space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Your Rating:</span>
                        <span class="font-black text-amber-500 text-sm">
                            @for($i = 0; $i < (session('rating') ?? 5); $i++) ★ @endfor
                        </span>
                    </div>
                    @if(session('customer_name') && session('customer_name') !== 'Valued Guest')
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Guest:</span>
                        <span class="font-bold text-gray-800">{{ session('customer_name') }}</span>
                    </div>
                    @endif
                    @if(session('phone'))
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Phone:</span>
                        <span class="font-bold text-gray-800">{{ session('phone') }}</span>
                    </div>
                    @endif
                </div>

                <div class="pt-2">
                    <a 
                        href="{{ route('restaurant.review') }}"
                        class="inline-flex items-center justify-center w-full bg-[#ea580c] hover:bg-[#c2410c] text-white text-xs font-bold py-3 px-4 rounded-xl shadow-md transition-all active:scale-[0.98]"
                    >
                        Submit Another Feedback
                    </a>
                </div>
            </div>
        @else

            <!-- Review Submission Form -->
            <div 
                x-data="{
                    rating: 5,
                    hoverRating: 0,
                    get currentRating() { return this.hoverRating || this.rating; },
                    get ratingLabel() {
                        const r = this.currentRating;
                        if (r === 5) return '★★★★★ Exceptional! Loved everything! 😍';
                        if (r === 4) return '★★★★☆ Very Good! Great taste & service 😊';
                        if (r === 3) return '★★★☆☆ Average / Satisfactory 🙂';
                        if (r === 2) return '★★☆☆☆ Below expectations 😕';
                        return '★☆☆☆☆ Poor / Disappointed 😞';
                    }
                }"
                class="bg-white rounded-2xl border border-gray-200/80 shadow-md p-5 sm:p-7 space-y-6"
            >
                <!-- Form Header -->
                <div class="text-center space-y-1.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        ⭐ Guest Dining Experience
                    </span>
                    <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">
                        How was your visit today?
                    </h2>
                    <p class="text-xs text-gray-500">
                        We value your genuine feedback to continually elevate our food & hospitality.
                    </p>
                </div>

                <form method="POST" action="{{ route('restaurant.review.store') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="rating" :value="rating">

                    <!-- Interactive Star Rating Selector -->
                    <div class="bg-gray-50/80 rounded-2xl border border-gray-200/70 p-4 text-center space-y-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Select Your Star Rating
                        </label>

                        <!-- 5 Big Interactive Stars -->
                        <div class="flex items-center justify-center gap-2 py-1">
                            <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                <button 
                                    type="button"
                                    @click="rating = star"
                                    @mouseenter="hoverRating = star"
                                    @mouseleave="hoverRating = 0"
                                    class="p-1 text-3xl sm:text-4xl transition-all duration-150 transform hover:scale-125 active:scale-95 focus:outline-none cursor-pointer"
                                    :class="currentRating >= star ? 'text-amber-400 drop-shadow-sm' : 'text-gray-300 hover:text-amber-200'"
                                    :title="star + ' Stars'"
                                >
                                    ★
                                </button>
                            </template>
                        </div>

                        <!-- Dynamic Rating Mood Badge -->
                        <div class="min-h-[22px]">
                            <span 
                                x-text="ratingLabel"
                                class="inline-block text-xs font-bold text-gray-700 transition-all duration-200 px-3 py-0.5 rounded-full"
                                :class="{
                                    'text-emerald-700 bg-emerald-50': currentRating >= 4,
                                    'text-amber-700 bg-amber-50': currentRating === 3,
                                    'text-rose-700 bg-rose-50': currentRating <= 2
                                }"
                            ></span>
                        </div>
                    </div>

                    <!-- Review Comment Box (Requested) -->
                    <div class="space-y-1.5">
                        <label for="comment" class="block text-xs font-bold text-gray-700 flex items-center justify-between">
                            <span>Your Feedback & Suggestions</span>
                            <span class="text-[10px] text-gray-400 font-normal">Optional</span>
                        </label>
                        <textarea 
                            id="comment"
                            name="comment" 
                            rows="4" 
                            class="w-full text-xs text-gray-900 bg-white border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-[#ea580c] focus:border-[#ea580c] transition-all resize-none shadow-2xs"
                            placeholder="Tell us what dish you liked most, your thoughts on the service, or any suggestions for us..."
                        ></textarea>
                    </div>

                    <!-- Phone Number Field (Replaced Your Name and Removed Table Number as requested) -->
                    <div class="pt-1">
                        <label for="phone" class="block text-xs font-bold text-gray-700 mb-1.5 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                Phone Number
                            </span>
                            <span class="text-[10px] text-gray-400 font-normal">Optional</span>
                        </label>
                        <input 
                            type="tel" 
                            id="phone"
                            name="phone" 
                            placeholder="e.g. 9876543210" 
                            class="w-full text-xs text-gray-900 bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-[#ea580c] focus:border-[#ea580c] transition-all shadow-2xs"
                        >
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button 
                            type="submit" 
                            class="w-full flex items-center justify-center gap-2 bg-[#ea580c] hover:bg-[#c2410c] text-white text-sm font-bold py-3.5 px-4 rounded-xl shadow-md shadow-orange-500/20 hover:shadow-lg transition-all duration-150 cursor-pointer active:scale-[0.98]"
                        >
                            <span>Submit Review</span>
                            <span class="text-base leading-none">⭐</span>
                        </button>
                    </div>

                    <p class="text-center text-[11px] text-gray-400">
                        Your honest feedback is directly reviewed by restaurant management.
                    </p>
                </form>
            </div>

        @endif

    </main>

    <!-- Footer -->
    <footer class="py-4 text-center text-xs text-gray-400 border-t border-gray-200/60 bg-white">
        <p>© {{ date('Y') }} {{ $config['restaurant_name'] ?? 'RESCO Restaurant' }}. All rights reserved.</p>
    </footer>

</body>
</html>
