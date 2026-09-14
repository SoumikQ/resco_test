<x-app-layout>
    @section('page_title', 'Restaurant Dashboard')

    <div class="space-y-6 pb-8">

        <!-- =========================================================================
             1. HEADER: RESTAURANT OVERVIEW & QUICK POS ACTIONS
             ========================================================================= -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-zinc-900 rounded-2xl p-6 sm:p-7 text-white shadow-lg border border-slate-700/60 relative overflow-hidden">
            <!-- Decorative Ambient Glow -->
            <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-orange-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 -bottom-20 w-80 h-80 rounded-full bg-amber-500/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <!-- Left: Greeting & System Status -->
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-slate-400 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            {{ now()->format('l, d F Y') }}
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white flex items-center gap-2">
                        {{ $config['restaurant_name'] ?? 'Restaurant Management' }}
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                        {{ $config['restaurant_tagline'] ?? 'Real-time billing and restaurant operations command center.' }}
                    </p>
                </div>

                <!-- Right: Quick POS Action Buttons -->
                <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                    <a 
                        href="{{ route('restaurant.orders.new') }}" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white font-semibold text-xs sm:text-sm rounded-xl shadow-md hover:shadow-orange-500/25 transition-all transform active:scale-95"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>New Order</span>
                    </a>

                    <a 
                        href="{{ route('restaurant.orders') }}" 
                        class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-800/90 hover:bg-slate-700 text-slate-200 hover:text-white font-medium text-xs sm:text-sm rounded-xl border border-slate-700 transition-colors shadow-sm"
                    >
                        <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span>Orders List</span>
                    </a>

                    <a 
                        href="{{ route('restaurant.customer-satisfaction') }}" 
                        class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-800/90 hover:bg-slate-700 text-slate-200 hover:text-white font-medium text-xs sm:text-sm rounded-xl border border-slate-700 transition-colors shadow-sm"
                    >
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span>Review QR</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- =========================================================================
             2. TOP 5 DYNAMIC KPI CARDS RIBBON
             ========================================================================= -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-3.5 sm:gap-4 lg:gap-5">
            
            <!-- Card 1: Today's Revenue -->
            <div class="bg-white rounded-2xl border border-gray-200/80 p-4 sm:p-5 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Today's Revenue</span>
                        <div class="text-xl sm:text-2xl font-black text-gray-900 mt-1 tracking-tight">
                            ₹ {{ number_format($todayRevenue, 2) }}
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-500">
                    <span class="font-medium text-emerald-700">Cash: ₹{{ number_format($totalCash, 0) }}</span>
                    <span class="font-medium text-indigo-700">Online: ₹{{ number_format($totalOnline, 0) }}</span>
                </div>
            </div>

            <!-- Card 2: Today's Orders -->
            <div class="bg-white rounded-2xl border border-gray-200/80 p-4 sm:p-5 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Today's Orders</span>
                        <div class="text-xl sm:text-2xl font-black text-gray-900 mt-1 tracking-tight">
                            {{ $todayOrdersCount }} <span class="text-xs font-semibold text-gray-500">Orders</span>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center group-hover:scale-110 transition-transform shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-500">
                    <span>Lifetime: <strong class="text-gray-800">{{ $totalOrdersCount }}</strong></span>
                    <a href="{{ route('restaurant.orders') }}" class="text-sky-600 font-semibold hover:underline">View All &rarr;</a>
                </div>
            </div>

            <!-- Card 3: Unpaid & Kitchen Orders -->
            <a href="{{ route('restaurant.orders', ['status' => 'unpaid']) }}" class="bg-white rounded-2xl border {{ $unpaidOrdersCount > 0 ? 'border-amber-300 ring-2 ring-amber-400/20' : 'border-gray-200/80' }} p-4 sm:p-5 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between block">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Unpaid</span>
                            @if($unpaidOrdersCount > 0)
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                </span>
                            @endif
                        </div>
                        <div class="text-xl sm:text-2xl font-black {{ $unpaidOrdersCount > 0 ? 'text-amber-600' : 'text-gray-900' }} mt-1 tracking-tight">
                            {{ $unpaidOrdersCount }} <span class="text-xs font-semibold text-gray-500">Pending</span>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px]">
                    <span class="text-gray-500">Awaiting Bill / Food</span>
                    <span class="text-amber-600 font-semibold group-hover:underline">Settle Orders &rarr;</span>
                </div>
            </a>

            <!-- Card 4: Average Order Value (AOV) -->
            <div class="bg-white rounded-2xl border border-gray-200/80 p-4 sm:p-5 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Average Order Value</span>
                        <div class="text-xl sm:text-2xl font-black text-gray-900 mt-1 tracking-tight">
                            ₹ {{ number_format($avgOrderValue, 2) }}
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                            <polyline points="17 6 23 6 23 12"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-500">
                    <span>Settled: <strong class="text-gray-800">{{ $completedOrdersCount }}</strong></span>
                    <span class="text-purple-600 font-semibold">Per Bill</span>
                </div>
            </div>

            <!-- Card 5: Customer Satisfaction & Rating -->
            <a href="{{ route('restaurant.customer-satisfaction') }}" class="bg-white rounded-2xl border border-gray-200/80 p-4 sm:p-5 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between block sm:col-span-2 md:col-span-1 xl:col-span-1">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Customer Rating</span>
                        <div class="text-xl sm:text-2xl font-black text-gray-900 mt-1 tracking-tight flex items-center gap-1.5">
                            {{ $csatAvg }}
                            <span class="text-amber-400 text-lg sm:text-xl font-normal">★</span>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center group-hover:scale-110 transition-transform shrink-0">
                        <svg class="w-5 h-5 fill-amber-400 text-amber-400" viewBox="0 0 24 24">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px]">
                    <span class="text-gray-500">{{ $csatTotal }} Feedbacks</span>
                    <span class="text-orange-600 font-semibold group-hover:underline">View Reviews &rarr;</span>
                </div>
            </a>

        </div>

        <!-- =========================================================================
             3. MIDDLE SECTION: LIVE RECENT ORDERS (8 COLS) & TOP DISHES / SHORTCUTS (4 COLS)
             ========================================================================= -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Left: Recent Live Orders Stream (8 cols) -->
            <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 sm:px-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900 tracking-tight">Recent Orders Activity</h2>
                            <p class="text-xs text-gray-500">Live operational feed of incoming and completed dining orders</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a 
                            href="{{ route('restaurant.orders') }}" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-orange-600 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors"
                        >
                            <span>All Orders</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[620px]">
                        <thead>
                            <tr class="bg-gray-50/75 text-[11px] font-semibold text-gray-600 border-b border-gray-100 uppercase tracking-wider">
                                <th class="py-3 px-5">Order</th>
                                <th class="py-3 px-4">Dining / Table</th>
                                <th class="py-3 px-4">Dishes Ordered</th>
                                <th class="py-3 px-4">Attendant</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-5 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                            @forelse($recentOrders as $order)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <!-- Order ID & Type -->
                                <td class="py-3.5 px-5">
                                    <div class="font-bold text-gray-900 flex items-center gap-1.5">
                                        #{{ $order->order_number ?? $order->id }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-0.5 font-mono">
                                        {{ $order->created_at ? $order->created_at->format('h:i A') : ($order->order_time ?? '—') }}
                                    </div>
                                </td>

                                <!-- Dining / Table -->
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ ($order->order_type == 'Take-Away' || $order->order_type == 'Delivery') ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : 'bg-orange-50 text-orange-700 border border-orange-100' }}">
                                        {{ $order->order_type ?? 'Dine-In' }}
                                    </span>
                                    @if($order->table_no)
                                        <div class="text-[11px] text-gray-600 font-medium mt-1 flex items-center gap-1">
                                            <span>Table {{ $order->table_no }}</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Dishes Summary -->
                                <td class="py-3.5 px-4">
                                    @if($order->items && $order->items->count() > 0)
                                        <div class="text-gray-900 font-medium max-w-[200px] truncate" title="{{ $order->items->pluck('item_name')->join(', ') }}">
                                            {{ $order->items->first()->quantity }}x {{ $order->items->first()->item_name }}
                                        </div>
                                        @if($order->items->count() > 1)
                                            <div class="text-[11px] text-gray-500 font-normal mt-0.5">
                                                +{{ $order->items->count() - 1 }} other dish{{ $order->items->count() > 2 ? 'es' : '' }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400 italic">Quick Order</span>
                                    @endif
                                </td>

                                <!-- Attendant -->
                                <td class="py-3.5 px-4">
                                    <div class="text-gray-800 font-medium">
                                        {{ $order->attendant ?? 'Staff' }}
                                    </div>
                                    <div class="text-[10px] text-gray-500">
                                        {{ $order->payment_method ?? 'Pending' }}
                                    </div>
                                </td>

                                <!-- Status Badge -->
                                <td class="py-3.5 px-4 text-center">
                                    @if(in_array(strtolower($order->status), ['complete', 'paid']))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Paid
                                        </span>
                                    @elseif(in_array(strtolower($order->status), ['unpaid', 'pending']))
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Unpaid
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-700">
                                            {{ $order->status }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Price -->
                                <td class="py-3.5 px-5 text-right font-bold text-gray-900">
                                    ₹ {{ number_format($order->total_amount, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <p class="text-sm font-semibold text-gray-700">No orders placed today yet</p>
                                    <p class="text-xs text-gray-500 mt-1">Start taking orders from the POS entry screen</p>
                                    <a href="{{ route('restaurant.orders.new') }}" class="inline-flex items-center gap-1 mt-3 px-3 py-1.5 bg-orange-500 text-white rounded-lg text-xs font-semibold hover:bg-orange-600">
                                        + Place First Order
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($recentOrders->count() > 0)
                <div class="p-3 bg-gray-50/60 border-t border-gray-100 text-center">
                    <a href="{{ route('restaurant.orders') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700">
                        View All Orders & Billing Records &rarr;
                    </a>
                </div>
                @endif
            </div>

            <!-- Right: Top Selling Dishes & Operational Hub (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- Card A: Top Selling Dishes Ranking -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                            </div>
                            <h2 class="text-base font-bold text-gray-900 tracking-tight">Top Selling Dishes</h2>
                        </div>
                        <a href="{{ route('restaurant.menu') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700">
                            Menu
                        </a>
                    </div>

                    <div class="mt-4 space-y-4">
                        @forelse($topDishes as $index => $dish)
                            @php
                                $widthPct = min(100, max(15, round(($dish->total_qty / $maxDishQty) * 100)));
                            @endphp
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2 truncate pr-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 font-bold text-[10px] flex items-center justify-center shrink-0">
                                            #{{ $index + 1 }}
                                        </span>
                                        <span class="font-bold text-gray-800 truncate" title="{{ $dish->item_name }}">{{ $dish->item_name }}</span>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="font-black text-gray-900">{{ $dish->total_qty }} sold</span>
                                        <span class="text-[11px] text-gray-500 ml-1">(₹{{ number_format($dish->total_rev, 0) }})</span>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                    <div 
                                        class="h-2 rounded-full transition-all duration-500 {{ $index === 0 ? 'bg-gradient-to-r from-orange-500 to-amber-500' : 'bg-slate-700' }}" 
                                        style="width: {{ $widthPct }}%;"
                                    ></div>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-gray-500 text-xs">
                                <p>No dish sales recorded yet.</p>
                                <a href="{{ route('restaurant.orders.new') }}" class="text-orange-600 font-semibold underline mt-1 block">Place an order</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Card B: Quick Operations Shortcuts -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6">
                    <h2 class="text-base font-bold text-gray-900 tracking-tight pb-3 border-b border-gray-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                        </svg>
                        Quick Navigation Hub
                    </h2>

                    <div class="mt-4 grid grid-cols-2 gap-2.5">
                        <a 
                            href="{{ route('restaurant.orders.new') }}" 
                            class="p-3 rounded-xl border border-gray-100 bg-gray-50/70 hover:bg-orange-50/60 hover:border-orange-200 transition-all flex flex-col items-center text-center group"
                        >
                            <div class="w-9 h-9 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-gray-800 mt-2">New POS Order</span>
                            <span class="text-[10px] text-gray-500">Shortcut (F2)</span>
                        </a>

                        <a 
                            href="{{ route('restaurant.menu') }}" 
                            class="p-3 rounded-xl border border-gray-100 bg-gray-50/70 hover:bg-orange-50/60 hover:border-orange-200 transition-all flex flex-col items-center text-center group"
                        >
                            <div class="w-9 h-9 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-gray-800 mt-2">Food Menu</span>
                            <span class="text-[10px] text-gray-500">{{ $totalMenuItemsCount }} Dishes</span>
                        </a>

                        <a 
                            href="{{ route('restaurant.customer-satisfaction') }}" 
                            class="p-3 rounded-xl border border-gray-100 bg-gray-50/70 hover:bg-orange-50/60 hover:border-orange-200 transition-all flex flex-col items-center text-center group"
                        >
                            <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-gray-800 mt-2">Review Standee</span>
                            <span class="text-[10px] text-gray-500">QR System</span>
                        </a>

                        <a 
                            href="{{ route('restaurant.reports') }}" 
                            class="p-3 rounded-xl border border-gray-100 bg-gray-50/70 hover:bg-orange-50/60 hover:border-orange-200 transition-all flex flex-col items-center text-center group"
                        >
                            <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <line x1="18" y1="20" x2="18" y2="10"></line>
                                    <line x1="12" y1="20" x2="12" y2="4"></line>
                                    <line x1="6" y1="20" x2="6" y2="14"></line>
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-gray-800 mt-2">Sales Report</span>
                            <span class="text-[10px] text-gray-500">Export & Print</span>
                        </a>
                    </div>
                </div>

            </div>

        </div>

        <!-- =========================================================================
             4. BOTTOM SECTION: 7-DAY REVENUE BAR CHART & PAYMENT SPLIT
             ========================================================================= -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- 7-Day Revenue Trend (8 cols) -->
            <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 border-b border-gray-100 gap-3">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 tracking-tight">Last 7 Days Sales Trend</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Daily gross revenue & order volume analytics</p>
                    </div>

                    <div class="flex items-center gap-3 text-xs">
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded bg-orange-500"></span>
                            <span class="text-gray-600 font-medium">Today</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded bg-slate-700"></span>
                            <span class="text-gray-600 font-medium">Past Days</span>
                        </div>
                    </div>
                </div>

                <!-- Bar Chart Visualization -->
                <div class="pt-6">
                    <div class="h-64 flex items-end justify-between gap-2 sm:gap-4 px-2 sm:px-6 relative border-b border-gray-200">
                        <!-- Horizontal Grid Lines -->
                        <div class="absolute inset-x-0 inset-y-0 flex flex-col justify-between pointer-events-none opacity-30 pb-1">
                            <div class="border-b border-dashed border-gray-400 w-full"></div>
                            <div class="border-b border-dashed border-gray-400 w-full"></div>
                            <div class="border-b border-dashed border-gray-400 w-full"></div>
                            <div class="border-b border-transparent w-full"></div>
                        </div>

                        <!-- Daily Bars -->
                        @foreach($chartData as $bar)
                            @php
                                $heightPct = $maxChartRev > 0 ? round(($bar['revenue'] / $maxChartRev) * 100) : 0;
                                // Minimum height so ₹0 days still show an elegant baseline marker
                                $displayHeight = max(6, $heightPct);
                            @endphp
                            <div class="flex-1 flex flex-col items-center h-full justify-end group relative z-10">
                                <!-- Tooltip on Hover -->
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute -top-12 bg-gray-900 text-white text-[11px] font-semibold py-1.5 px-3 rounded-lg shadow-xl whitespace-nowrap pointer-events-none z-20 flex flex-col items-center">
                                    <span>{{ $bar['date'] }}: ₹ {{ number_format($bar['revenue'], 2) }}</span>
                                    <span class="text-[10px] text-gray-300 font-normal">{{ $bar['orders'] }} Orders</span>
                                    <div class="w-2 h-2 bg-gray-900 rotate-45 -mb-1 mt-0.5"></div>
                                </div>

                                <!-- Value Label Above Bar -->
                                <div class="text-[10px] font-bold text-gray-600 mb-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    ₹{{ number_format($bar['revenue'], 0) }}
                                </div>

                                <!-- Bar -->
                                <div 
                                    class="w-full max-w-[48px] rounded-t-lg transition-all duration-300 cursor-pointer {{ $bar['is_today'] ? 'bg-gradient-to-t from-orange-600 to-amber-500 shadow-md shadow-orange-500/20 group-hover:brightness-110' : 'bg-slate-700 hover:bg-slate-600' }}"
                                    style="height: {{ $displayHeight }}%;"
                                ></div>

                                <!-- Day Label Below Bar -->
                                <div class="mt-2 text-center select-none">
                                    <div class="text-[11px] font-bold {{ $bar['is_today'] ? 'text-orange-600' : 'text-gray-700' }}">
                                        {{ $bar['day'] }}
                                    </div>
                                    <div class="text-[10px] text-gray-400 font-medium">
                                        {{ $bar['date'] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                    <span>Highest Single-Day: <strong class="text-gray-900 font-semibold">₹ {{ number_format($maxChartRev, 2) }}</strong></span>
                    <span>7-Day Aggregated Revenue: <strong class="text-gray-900 font-semibold">₹ {{ number_format(collect($chartData)->sum('revenue'), 2) }}</strong></span>
                </div>
            </div>

            <!-- Payment Breakdown & Health Card (4 cols) -->
            <div class="lg:col-span-4 bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-6">
                <div>
                    <h2 class="text-base font-bold text-gray-900 tracking-tight pb-3 border-b border-gray-100 flex items-center justify-between">
                        <span>Payment Collections Split</span>
                        <span class="text-xs font-semibold text-emerald-600">Active</span>
                    </h2>

                    <!-- Dual Segment Progress Bar -->
                    <div class="mt-4 space-y-2">
                        <div class="w-full bg-gray-100 rounded-full h-3 flex overflow-hidden p-0.5">
                            <div 
                                class="bg-emerald-500 h-full rounded-l-full transition-all duration-500" 
                                style="width: {{ $cashPercent }}%;"
                                title="Cash: {{ $cashPercent }}%"
                            ></div>
                            <div 
                                class="bg-indigo-600 h-full rounded-r-full transition-all duration-500" 
                                style="width: {{ $onlinePercent }}%;"
                                title="Online: {{ $onlinePercent }}%"
                            ></div>
                        </div>

                        <div class="flex items-center justify-between text-xs font-bold pt-1">
                            <div class="flex items-center gap-1.5 text-emerald-700">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span>Cash: {{ $cashPercent }}%</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-indigo-700">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                                <span>Digital UPI: {{ $onlinePercent }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Details Table -->
                    <div class="mt-4 bg-gray-50 rounded-xl p-3 space-y-2 text-xs">
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Cash Total Collected:</span>
                            <strong class="text-gray-900 font-bold">₹ {{ number_format($totalCash, 2) }}</strong>
                        </div>
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Online / Card / UPI:</span>
                            <strong class="text-gray-900 font-bold">₹ {{ number_format($totalOnline, 2) }}</strong>
                        </div>
                        <div class="pt-2 border-t border-gray-200 flex justify-between items-center text-gray-800 font-bold">
                            <span>Total Settled:</span>
                            <span class="text-emerald-600 text-sm">₹ {{ number_format($totalCash + $totalOnline, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Operational Inventory Alert -->
                <div class="pt-2 border-t border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg {{ $lowStockItemsCount > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-gray-900">Inventory Status</h3>
                                <p class="text-[11px] text-gray-500">
                                    @if($lowStockItemsCount > 0)
                                        <span class="text-rose-600 font-semibold">{{ $lowStockItemsCount }} items</span> low on stock
                                    @else
                                        All items adequately stocked
                                    @endif
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('restaurant.menu') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700">
                            Check Menu &rarr;
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </div>
</x-app-layout>
