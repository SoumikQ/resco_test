<x-app-layout>
    @section('page_title', 'Basic Reports')

    <div class="space-y-6">

        <!-- Top Header & Action Buttons -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1 border-b border-gray-200/70">
            <div>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-[#ea580c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Basic Reports & Analytics
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">Real-time revenue, payment method breakdowns, and food sales analytics.</p>
            </div>
            
            <div class="flex items-center gap-2.5 print:hidden">
                <button 
                    onclick="window.print()"
                    type="button"
                    class="inline-flex items-center justify-center bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold px-3.5 py-2 rounded-md shadow-2xs transition-colors cursor-pointer"
                    title="Print this Report"
                >
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Report
                </button>
                <a 
                    href="{{ route('restaurant.reports', array_merge(request()->query(), ['export' => 'csv'])) }}"
                    class="inline-flex items-center justify-center bg-[#ef7d3b] hover:bg-[#d94e08] text-white text-xs font-semibold px-4 py-2 rounded-md shadow-2xs transition-colors cursor-pointer"
                    title="Download Report CSV Spreadsheet"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export CSV
                </a>
            </div>
        </div>

        <!-- Date Range Filter Toolbar -->
        <div class="bg-white rounded-xl border border-gray-200/90 shadow-2xs p-4 sm:p-5 print:hidden">
            <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
                
                <!-- Quick Preset Filter Pills -->
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                    <span class="text-xs font-bold text-gray-600 mr-1 sm:mr-2 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#ea580c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Filter:
                    </span>

                    <a 
                        href="{{ route('restaurant.reports', ['preset' => 'today']) }}"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $preset === 'today' ? 'bg-[#ea580c] text-white shadow-2xs' : 'bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 text-gray-700 shadow-2xs' }}"
                    >
                        Today
                    </a>

                    <a 
                        href="{{ route('restaurant.reports', ['preset' => 'yesterday']) }}"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $preset === 'yesterday' ? 'bg-[#ea580c] text-white shadow-2xs' : 'bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 text-gray-700 shadow-2xs' }}"
                    >
                        Yesterday
                    </a>

                    <a 
                        href="{{ route('restaurant.reports', ['preset' => 'this_week']) }}"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $preset === 'this_week' ? 'bg-[#ea580c] text-white shadow-2xs' : 'bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 text-gray-700 shadow-2xs' }}"
                    >
                        Last 7 Days
                    </a>

                    <a 
                        href="{{ route('restaurant.reports', ['preset' => 'this_month']) }}"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $preset === 'this_month' ? 'bg-[#ea580c] text-white shadow-2xs' : 'bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 text-gray-700 shadow-2xs' }}"
                    >
                        This Month
                    </a>

                    <a 
                        href="{{ route('restaurant.reports', ['preset' => 'all']) }}"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $preset === 'all' ? 'bg-[#ea580c] text-white shadow-2xs' : 'bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 text-gray-700 shadow-2xs' }}"
                    >
                        All Time
                    </a>
                </div>

                <!-- Custom Date Range Form -->
                <form method="GET" action="{{ route('restaurant.reports') }}" class="flex flex-wrap items-center gap-2 sm:gap-2.5">
                    <input type="hidden" name="preset" value="custom" />
                    <div class="flex items-center gap-1.5">
                        <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">From:</label>
                        <input 
                            type="date" 
                            name="from_date" 
                            value="{{ $fromDate }}" 
                            class="bg-white border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-1 focus:ring-[#ea580c] focus:border-[#ea580c] block px-2.5 py-1.5 shadow-2xs"
                            required
                        />
                    </div>
                    <div class="flex items-center gap-1.5">
                        <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">To:</label>
                        <input 
                            type="date" 
                            name="to_date" 
                            value="{{ $toDate }}" 
                            class="bg-white border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-1 focus:ring-[#ea580c] focus:border-[#ea580c] block px-2.5 py-1.5 shadow-2xs"
                            required
                        />
                    </div>
                    <button 
                        type="submit" 
                        class="bg-[#ea580c] hover:bg-[#c2410c] text-white text-xs font-semibold px-3.5 py-1.5 rounded-lg shadow-2xs transition-colors cursor-pointer"
                    >
                        Apply
                    </button>
                    @if(request()->has('preset') || request()->has('from_date'))
                        <a 
                            href="{{ route('restaurant.reports') }}" 
                            class="text-xs text-gray-500 hover:text-red-600 font-semibold px-2 py-1.5 transition-colors"
                            title="Reset filters"
                        >
                            ✕ Reset
                        </a>
                    @endif
                </form>

            </div>

            <!-- Active Range Indicator -->
            <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between text-xs text-gray-600 gap-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-50 text-[#ea580c] border border-orange-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#ea580c] animate-pulse"></span>
                        Range: {{ $label }}
                    </span>
                    <span class="text-gray-400">|</span>
                    <span class="font-medium text-gray-700">Total Orders: <strong>{{ $allOrders->count() }}</strong></span>
                    <span class="text-gray-400">|</span>
                    <span class="text-emerald-700 font-medium">Completed: <strong>{{ $completedCount }}</strong></span>
                    @if($unpaidCount > 0)
                        <span class="text-gray-400">|</span>
                        <span class="text-amber-700 font-medium">Unpaid: <strong>{{ $unpaidCount }}</strong> (₹{{ number_format($unpaidAmount, 2) }})</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3 Primary Payment Metrics (User Request: Total Cash, Total Online, Cash in Hand) -->
        <div>
            <div class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2.5 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#ea580c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Payment Collections & Cash Balance
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                
                <!-- 1. Total Cash -->
                <div class="bg-white rounded-lg border border-emerald-200 shadow-2xs p-5 relative overflow-hidden bg-gradient-to-br from-white via-white to-emerald-50/40">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">
                                Cash Collections
                            </span>
                            <div class="text-xs font-semibold text-gray-500 mt-2">Total Cash</div>
                            <div class="text-2xl font-black text-emerald-700 mt-1 tracking-tight">
                                ₹ {{ number_format($totalCash, 2) }}
                            </div>
                        </div>
                        <div class="w-11 h-11 rounded-lg bg-emerald-100/80 flex items-center justify-center text-emerald-600 shadow-2xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect width="20" height="12" x="2" y="6" rx="2" stroke-width="2"/>
                                <circle cx="12" cy="12" r="2.5" stroke-width="2"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h.01M18 12h.01"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-emerald-100 text-[11px] text-gray-500 flex items-center justify-between">
                        <span>Received via Cash & Split</span>
                        <span class="font-bold text-emerald-700">{{ $totalSales > 0 ? round(($totalCash / $totalSales) * 100, 1) : 0 }}% of Total</span>
                    </div>
                </div>

                <!-- 2. Total Online -->
                <div class="bg-white rounded-lg border border-blue-200 shadow-2xs p-5 relative overflow-hidden bg-gradient-to-br from-white via-white to-blue-50/40">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 uppercase tracking-wider">
                                Digital Collections
                            </span>
                            <div class="text-xs font-semibold text-gray-500 mt-2">Total Online</div>
                            <div class="text-2xl font-black text-blue-700 mt-1 tracking-tight">
                                ₹ {{ number_format($totalOnline, 2) }}
                            </div>
                        </div>
                        <div class="w-11 h-11 rounded-lg bg-blue-100/80 flex items-center justify-center text-blue-600 shadow-2xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect width="20" height="14" x="2" y="5" rx="2" stroke-width="2"/>
                                <line x1="2" x2="22" y1="10" y2="10" stroke-width="2"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 15h3"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-blue-100 text-[11px] text-gray-500 flex items-center justify-between">
                        <span>UPI, Cards, GPay & PhonePe</span>
                        <span class="font-bold text-blue-700">{{ $totalSales > 0 ? round(($totalOnline / $totalSales) * 100, 1) : 0 }}% of Total</span>
                    </div>
                </div>

                <!-- 3. Cash in Hand -->
                <div class="bg-white rounded-lg border border-amber-200 shadow-2xs p-5 relative overflow-hidden bg-gradient-to-br from-white via-white to-amber-50/50">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 uppercase tracking-wider">
                                Cash Register
                            </span>
                            <div class="text-xs font-semibold text-gray-500 mt-2">Cash in Hand</div>
                            <div class="text-2xl font-black text-amber-700 mt-1 tracking-tight">
                                ₹ {{ number_format($cashInHand, 2) }}
                            </div>
                        </div>
                        <div class="w-11 h-11 rounded-lg bg-amber-100/80 flex items-center justify-center text-amber-700 shadow-2xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-amber-100 text-[11px] text-gray-500 flex items-center justify-between">
                        <span>Available in Counter Drawer</span>
                        <span class="font-bold text-amber-800">Ready in Register</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- 3 Sales Summary KPI Cards -->
        <div>
            <div class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2.5 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                Revenue & Sales Performance
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                
                <!-- Gross Sales Revenue -->
                <div class="bg-white rounded-lg border border-gray-200/90 shadow-2xs p-5">
                    <div class="text-xs font-semibold text-gray-500">Gross Sales (Settled)</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">₹ {{ number_format($totalSales, 2) }}</div>
                    <div class="mt-2 text-[11px] text-gray-500">
                        Total collected from <span class="font-semibold text-gray-800">{{ $completedCount }} completed orders</span>
                    </div>
                </div>

                <!-- Orders Completed -->
                <div class="bg-white rounded-lg border border-gray-200/90 shadow-2xs p-5">
                    <div class="text-xs font-semibold text-gray-500">Orders Completed</div>
                    <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $completedCount }} Paid</div>
                    <div class="mt-2 text-[11px] text-gray-500">
                        @if($unpaidCount > 0)
                            <span class="text-amber-600 font-semibold">{{ $unpaidCount }} orders pending / unpaid</span>
                        @else
                            <span class="text-emerald-600 font-medium">✓ All orders in this range are paid</span>
                        @endif
                    </div>
                </div>

                <!-- Average Order Value -->
                <div class="bg-white rounded-lg border border-gray-200/90 shadow-2xs p-5">
                    <div class="text-xs font-semibold text-gray-500">Average Order Value (AOV)</div>
                    <div class="text-2xl font-bold text-sky-700 mt-1">₹ {{ number_format($avgOrderValue, 2) }}</div>
                    <div class="mt-2 text-[11px] text-gray-500">Average revenue per completed table</div>
                </div>

            </div>
        </div>

        <!-- Two Columns: Top Selling Items & Category-wise Sales Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Top Selling Food Items (Dynamic) -->
            <div class="bg-white rounded-lg border border-gray-200/90 shadow-2xs overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 tracking-tight flex items-center gap-2">
                            <span class="text-amber-500">★</span> Top Selling Food Items
                        </h2>
                        <p class="text-xs text-gray-400 mt-0.5">Most ordered dishes ranked by volume in this period</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[480px]">
                        <thead>
                            <tr class="bg-gray-50/70 text-[11px] font-semibold text-gray-600 border-b border-gray-200/70">
                                <th class="py-3 px-4 w-12 text-center">#</th>
                                <th class="py-3 px-4">Dish</th>
                                <th class="py-3 px-4">Category</th>
                                <th class="py-3 px-4 text-center">Units Sold</th>
                                <th class="py-3 px-5 text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                            @forelse($topDishes as $index => $d)
                            <tr class="hover:bg-gray-50/70">
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold {{ $index === 0 ? 'bg-amber-100 text-amber-800' : ($index === 1 ? 'bg-gray-200 text-gray-700' : ($index === 2 ? 'bg-orange-100 text-orange-800' : 'bg-gray-50 text-gray-500')) }}">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-bold text-gray-900">{{ $d->item_name }}</td>
                                <td class="py-3 px-4 text-gray-500">{{ $d->category_name }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-orange-50 text-[#ea580c] border border-orange-200/60">
                                        {{ $d->total_qty }}
                                    </span>
                                </td>
                                <td class="py-3 px-5 text-right font-bold text-gray-900">₹ {{ number_format($d->total_revenue, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400 text-xs">
                                    No dish sales found for the selected date range.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Category-wise Sales Breakdown (Dynamic) -->
            <div class="bg-white rounded-lg border border-gray-200/90 shadow-2xs overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 tracking-tight flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#ea580c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Category-wise Revenue Breakdown
                        </h2>
                        <p class="text-xs text-gray-400 mt-0.5">Sales contribution by food category</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[440px]">
                        <thead>
                            <tr class="bg-gray-50/70 text-[11px] font-semibold text-gray-600 border-b border-gray-200/70">
                                <th class="py-3 px-5">Category</th>
                                <th class="py-3 px-4 text-center">Items Sold</th>
                                <th class="py-3 px-4 text-right">Revenue</th>
                                <th class="py-3 px-5 text-right w-28">Share</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                            @forelse($categorySales as $c)
                            @php
                                $share = $totalSales > 0 ? round(($c->total_revenue / $totalSales) * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-gray-50/70">
                                <td class="py-3 px-5 font-bold text-gray-900 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[#ea580c]"></span>
                                    {{ $c->category_name }}
                                </td>
                                <td class="py-3 px-4 text-center font-semibold text-gray-800">{{ $c->total_qty }}</td>
                                <td class="py-3 px-4 text-right font-bold text-gray-900">₹ {{ number_format($c->total_revenue, 2) }}</td>
                                <td class="py-3 px-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <div class="w-12 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-[#ea580c] h-1.5 rounded-full" style="width: {{ $share }}%"></div>
                                        </div>
                                        <span class="font-bold text-gray-700 text-[11px] w-9">{{ $share }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-400 text-xs">
                                    No category sales found for the selected date range.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Orders & Settlements Ledger in Selected Range -->
        <div class="bg-white rounded-lg border border-gray-200/90 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-bold text-gray-900 tracking-tight flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        Settled Orders & Transactions ({{ $label }})
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">Itemized transaction log with cash vs online breakdown</p>
                </div>
                <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2.5 py-1 rounded">
                    {{ $allOrders->count() }} Orders Total
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[780px]">
                    <thead>
                        <tr class="bg-gray-50/70 text-[11px] font-semibold text-gray-600 border-b border-gray-200/70">
                            <th class="py-3 px-4">Order #</th>
                            <th class="py-3 px-4">Type / Table</th>
                            <th class="py-3 px-4">Attendant</th>
                            <th class="py-3 px-4">Order Time</th>
                            <th class="py-3 px-4 text-center">Payment Mode</th>
                            <th class="py-3 px-4 text-right">Cash (₹)</th>
                            <th class="py-3 px-4 text-right">Online (₹)</th>
                            <th class="py-3 px-4 text-right">Total Bill (₹)</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center print:hidden">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                        @forelse($allOrders as $order)
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="py-3 px-4 font-bold text-gray-900">
                                #{{ $order->order_number }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-medium text-gray-800">{{ $order->order_type }}</span>
                                @if($order->table_no)
                                    <span class="text-[10px] text-gray-500 block">{{ $order->table_no }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-600">
                                {{ $order->attendant ?: 'Staff' }}
                            </td>
                            <td class="py-3 px-4 text-gray-500 text-[11px]">
                                {{ $order->order_time ?: $order->created_at->format('d-M-Y H:i') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($order->payment_method === 'Cash')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Cash
                                    </span>
                                @elseif($order->payment_method === 'Online')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">
                                        Online
                                    </span>
                                @elseif($order->payment_method === 'Split')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">
                                        Split
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-500">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-medium {{ (float)$order->cash_amount > 0 ? 'text-emerald-700 font-bold' : 'text-gray-400' }}">
                                {{ (float)$order->cash_amount > 0 ? '₹ ' . number_format($order->cash_amount, 2) : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right font-medium {{ (float)$order->online_amount > 0 ? 'text-blue-700 font-bold' : 'text-gray-400' }}">
                                {{ (float)$order->online_amount > 0 ? '₹ ' . number_format($order->online_amount, 2) : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-gray-900">
                                ₹ {{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if(in_array($order->status, ['Complete', 'Paid']))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Paid
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Unpaid
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center print:hidden">
                                <button 
                                    onclick="window.printOrderInvoice({{ $order->toJson() }})"
                                    class="inline-flex items-center bg-gray-100 hover:bg-[#ea580c] hover:text-white text-gray-700 text-[11px] font-semibold px-2.5 py-1 rounded transition-colors cursor-pointer"
                                    title="Print Thermal Bill"
                                >
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                    Print Bill
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-gray-400 text-xs">
                                No orders found in this date range.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($allOrders->count() > 0)
                    <tfoot>
                        <tr class="bg-gray-100/80 font-bold text-xs text-gray-900 border-t-2 border-gray-300">
                            <td colspan="5" class="py-3.5 px-4 text-right uppercase tracking-wider">
                                Period Totals:
                            </td>
                            <td class="py-3.5 px-4 text-right text-emerald-800">
                                ₹ {{ number_format($totalCash, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right text-blue-800">
                                ₹ {{ number_format($totalOnline, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right text-gray-900">
                                ₹ {{ number_format($totalSales, 2) }}
                            </td>
                            <td colspan="2" class="py-3.5 px-4 text-center text-gray-500 font-normal text-[11px]">
                                {{ $completedCount }} Paid / {{ $unpaidCount }} Unpaid
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
