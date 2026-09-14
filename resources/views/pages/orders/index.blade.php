<x-app-layout>
    @section('page_title', 'Orders')

    <div 
        class="space-y-6" 
        x-data="{ 
            selectedOrder: null,
            payingOrder: null,
            payMethod: 'Cash',
            splitCash: '',
            splitOnline: '',
            openPayModal(order) {
                this.payingOrder = order;
                this.payMethod = 'Cash';
                this.splitCash = '';
                this.splitOnline = '';
            },
            submitSettlePayment(e) {
                if (this.payMethod === 'Split') {
                    const cash = parseFloat(this.splitCash) || 0;
                    const online = parseFloat(this.splitOnline) || 0;
                    const sum = Math.round((cash + online) * 100) / 100;
                    const total = Math.round(Number(this.payingOrder?.total_amount || 0) * 100) / 100;
                    if (cash <= 0 && online <= 0) {
                        alert('Please enter Cash and Online split amounts.');
                        e.preventDefault();
                        return;
                    }
                    if (Math.abs(sum - total) > 0.01) {
                        alert('Split amounts (Cash: ₹' + cash.toFixed(2) + ' + Online: ₹' + online.toFixed(2) + ' = ₹' + sum.toFixed(2) + ') must equal Total Amount ₹' + total.toFixed(2));
                        e.preventDefault();
                        return;
                    }
                }

                // Trigger instant thermal invoice print
                if (this.payingOrder && typeof window.printOrderInvoice === 'function') {
                    const orderData = { ...this.payingOrder };
                    orderData.payment_method = this.payMethod;
                    orderData.cash_amount = this.payMethod === 'Split' ? parseFloat(this.splitCash || 0) : (this.payMethod === 'Cash' ? orderData.total_amount : 0);
                    orderData.online_amount = this.payMethod === 'Split' ? parseFloat(this.splitOnline || 0) : (this.payMethod === 'Online' ? orderData.total_amount : 0);
                    window.printOrderInvoice(orderData);
                }
            }
        }"
    >

        <!-- Flash Success Notification -->
        @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-semibold flex items-center justify-between shadow-2xs">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.closest('.p-4').remove()" class="text-emerald-500 hover:text-emerald-700 font-bold text-sm px-1 cursor-pointer">✕</button>
        </div>
        @endif

        <!-- Top Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">Order Management</h1>
                <p class="text-xs text-gray-500 mt-0.5">Track real-time orders, manage bills, and settle payments.</p>
            </div>
            
            <a 
                href="{{ route('restaurant.orders.new') }}" 
                class="inline-flex items-center justify-center bg-[#ef7d3b] hover:bg-[#d94e08] text-white text-xs font-semibold px-4 py-2.5 rounded-md shadow-2xs transition-colors cursor-pointer"
            >
                <span class="text-base mr-1.5 leading-none">+</span> New Order
            </a>
        </div>

        <!-- Orders Table Card -->
        <div class="bg-white rounded-lg border border-gray-200/90 shadow-2xs overflow-hidden">
            
            <!-- Filters Bar -->
            <div class="p-3.5 sm:p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50/40">
                <div class="flex items-center space-x-2 text-xs overflow-x-auto pb-1 sm:pb-0 max-w-full no-scrollbar flex-nowrap">
                    <a href="{{ route('restaurant.orders') }}" class="px-3 py-1.5 rounded-md font-semibold shrink-0 whitespace-nowrap {{ $statusFilter === 'all' ? 'bg-orange-100 text-orange-800' : 'text-gray-600 hover:bg-gray-100' }}">
                        All ({{ $totalCount }})
                    </a>
                    <a href="{{ route('restaurant.orders', ['status' => 'Dine In']) }}" class="px-3 py-1.5 rounded-md font-medium shrink-0 whitespace-nowrap {{ $statusFilter === 'Dine In' ? 'bg-orange-100 text-orange-800' : 'text-gray-600 hover:bg-gray-100' }}">
                        Dine In ({{ $dineInCount }})
                    </a>
                    <a href="{{ route('restaurant.orders', ['status' => 'Take Away']) }}" class="px-3 py-1.5 rounded-md font-medium shrink-0 whitespace-nowrap {{ $statusFilter === 'Take Away' ? 'bg-orange-100 text-orange-800' : 'text-gray-600 hover:bg-gray-100' }}">
                        Take Away ({{ $takeAwayCount }})
                    </a>
                    <a href="{{ route('restaurant.orders', ['status' => 'Unpaid']) }}" class="px-3 py-1.5 rounded-md font-medium shrink-0 whitespace-nowrap {{ $statusFilter === 'Unpaid' ? 'bg-amber-100 text-amber-800 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                        Unpaid ({{ $unpaidCount }})
                    </a>
                    <a href="{{ route('restaurant.orders', ['status' => 'Complete']) }}" class="px-3 py-1.5 rounded-md font-medium shrink-0 whitespace-nowrap {{ $statusFilter === 'Complete' || $statusFilter === 'Paid' ? 'bg-emerald-100 text-emerald-800 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                        Complete ({{ $completeCount }})
                    </a>
                </div>

                <div class="text-[11px] sm:text-xs text-gray-500">
                    Showing <span class="font-semibold text-gray-800">{{ $orders->count() }}</span> orders
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[720px]">
                    <thead>
                        <tr class="bg-gray-50/70 text-[11px] font-semibold text-gray-600 border-b border-gray-200/70">
                            <th class="py-3 px-4 sm:px-5">Order ID</th>
                            <th class="py-3 px-3 sm:px-4">Order Type</th>
                            <th class="py-3 px-3 sm:px-4">Table</th>
                            <th class="py-3 px-3 sm:px-4">Attendant</th>
                            <th class="py-3 px-3 sm:px-4">Time</th>
                            <th class="py-3 px-3 sm:px-4 text-center">Items</th>
                            <th class="py-3 px-4 sm:px-5 text-right">Price</th>
                            <th class="py-3 px-4 sm:px-5 text-center">Bill</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                        @forelse($orders as $order)
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="py-3.5 px-5 font-bold text-gray-900 font-mono">
                                #{{ $order->order_number }}
                            </td>
                            <td class="py-3.5 px-4 text-gray-600 font-medium">
                                {{ $order->order_type }}
                            </td>
                            <td class="py-3.5 px-4 text-gray-700 font-semibold">
                                {{ $order->table_no ?: 'Take Away' }}
                            </td>
                            <td class="py-3.5 px-4 text-gray-900 font-medium">
                                {{ $order->attendant }}
                            </td>
                            <td class="py-3.5 px-4 text-gray-500 font-mono text-[11px]">
                                {{ $order->order_time }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <button 
                                    @click="selectedOrder = {{ $order->toJson() }}"
                                    class="bg-gray-100 hover:bg-orange-50 text-gray-700 hover:text-orange-700 px-2.5 py-1 rounded text-[11px] font-semibold transition-colors cursor-pointer"
                                    title="View items"
                                >
                                    {{ $order->items->count() }} items &bull; View
                                </button>
                            </td>
                            <td class="py-3.5 px-5 text-right font-bold text-gray-900 font-mono">
                                ₹ {{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                @if($order->status === 'Complete' || $order->status === 'Paid')
                                    <div class="inline-flex items-center justify-center gap-2">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <svg class="w-2.5 h-2.5 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Paid
                                            </span>
                                            @if($order->payment_method)
                                                <span class="text-[10px] text-gray-500 font-medium mt-0.5">
                                                    {{ $order->payment_method }}
                                                    @if($order->payment_method === 'Split')
                                                        (₹{{ number_format($order->cash_amount, 0) }}+₹{{ number_format($order->online_amount, 0) }})
                                                    @endif
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Print Bill Button -->
                                        <button 
                                            type="button"
                                            @click="printOrderInvoice({{ $order->toJson() }})"
                                            class="inline-flex items-center space-x-1 text-xs font-bold text-orange-600 hover:text-white bg-orange-50 hover:bg-[#ea580c] border border-orange-200 hover:border-orange-600 px-2.5 py-1.5 rounded-md transition-all cursor-pointer shadow-2xs group"
                                            title="Print Thermal Bill Invoice"
                                        >
                                            <svg class="w-3.5 h-3.5 text-orange-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                            <span>Print Bill</span>
                                        </button>
                                    </div>
                                @else
                                    <div class="inline-flex items-center justify-center gap-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            Unpaid
                                        </span>
                                        <button 
                                            type="button"
                                            @click="openPayModal({{ $order->toJson() }})"
                                            class="inline-flex items-center bg-[#ef7d3b] hover:bg-[#d94e08] text-white text-xs font-bold px-2.5 py-1.5 rounded-md shadow-2xs hover:shadow transition-all cursor-pointer"
                                            title="Settle & Pay Bill"
                                        >
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <span>Pay</span>
                                        </button>
                                        <button 
                                            type="button"
                                            @click="printOrderInvoice({{ $order->toJson() }})"
                                            class="inline-flex items-center space-x-1 text-xs font-bold text-gray-700 hover:text-white bg-gray-100 hover:bg-[#ea580c] border border-gray-200 hover:border-orange-600 px-2.5 py-1.5 rounded-md transition-all cursor-pointer shadow-2xs group"
                                            title="Print Pre-Bill / Invoice"
                                        >
                                            <svg class="w-3.5 h-3.5 text-gray-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                            <span>Print Bill</span>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400 text-xs">
                                No orders found for this filter. Click "+ New Order" to create one.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        <!-- Order Items Detail Modal (View Only) -->
        <div 
            x-show="selectedOrder" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4"
        >
            <div 
                @click.outside="selectedOrder = null"
                class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 border border-gray-100"
            >
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">
                            Order #<span x-text="selectedOrder?.order_number"></span> Details
                        </h3>
                        <p class="text-xs text-gray-500">
                            <span x-text="selectedOrder?.order_type"></span> &bull; 
                            <span x-text="selectedOrder?.table_no || 'Take Away'"></span> &bull; 
                            Attendant: <span x-text="selectedOrder?.attendant"></span>
                        </p>
                    </div>
                    <button @click="selectedOrder = null" class="text-gray-400 hover:text-gray-600 font-bold text-lg cursor-pointer">&times;</button>
                </div>

                <!-- Items Breakdown -->
                <div class="space-y-2.5 max-h-60 overflow-y-auto divide-y divide-gray-100">
                    <template x-for="item in selectedOrder?.items" :key="item.id">
                        <div class="pt-2 first:pt-0 flex items-center justify-between text-xs">
                            <div>
                                <div class="flex items-center space-x-1.5">
                                    <span class="font-semibold text-gray-800" x-text="item.item_name"></span>
                                    <template x-if="item.portion && item.portion !== 'Regular'">
                                        <span class="bg-orange-100 text-orange-700 text-[10px] font-bold px-1.5 py-0.5 rounded" x-text="item.portion + ' Plate'"></span>
                                    </template>
                                </div>
                                <div class="text-[11px] text-gray-400" x-text="item.quantity + ' × ₹' + Number(item.unit_price).toFixed(2)"></div>
                                <template x-if="item.notes">
                                    <div class="text-[10px] text-orange-600 italic" x-text="'Note: ' + item.notes"></div>
                                </template>
                            </div>
                            <div class="font-bold text-gray-900 font-mono">
                                ₹ <span x-text="Number(item.total_price).toFixed(2)"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 text-xs space-y-1">
                    <div class="flex justify-between text-gray-600">
                        <span>Total Items:</span>
                        <span class="font-mono font-semibold text-gray-800" x-text="selectedOrder?.items?.length || 0"></span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-gray-900 pt-2 border-t border-dashed border-gray-200">
                        <span>Total Amount:</span>
                        <span class="text-orange-600 font-mono text-base">₹ <span x-text="Number(selectedOrder?.total_amount).toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 pt-1">
                        <span>Bill Status:</span>
                        <span class="font-semibold" :class="selectedOrder?.status === 'Complete' || selectedOrder?.status === 'Paid' ? 'text-emerald-700' : 'text-amber-700'" x-text="selectedOrder?.status"></span>
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center space-x-2">
                        <button 
                            type="button" 
                            @click="printOrderSlip(selectedOrder)" 
                            class="inline-flex items-center space-x-1.5 px-3 py-2 bg-orange-50 hover:bg-orange-100 text-orange-700 text-xs font-bold rounded-md border border-orange-200 transition-colors cursor-pointer"
                            title="Print KOT / Kitchen slip"
                        >
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Print KOT Slip</span>
                        </button>

                        <button 
                            type="button" 
                            @click="printOrderInvoice(selectedOrder)" 
                            class="inline-flex items-center space-x-1.5 px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold rounded-md border border-emerald-200 transition-colors cursor-pointer"
                            title="Print Thermal Bill Invoice"
                        >
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Print Bill Invoice</span>
                        </button>
                    </div>

                    <button 
                        @click="selectedOrder = null" 
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-md transition-colors cursor-pointer"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Settle Bill Modal (Triggered when Pay button clicked on Unpaid order) -->
        <div 
            x-show="payingOrder" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4"
        >
            <div 
                @click.outside="payingOrder = null"
                class="bg-white rounded-xl shadow-2xl max-w-lg w-full border border-gray-100 overflow-hidden transform transition-all animate-in fade-in zoom-in-95 duration-150"
            >
                <!-- Form wrapping payment submission -->
                <form :action="'{{ url('/orders') }}/' + payingOrder?.id + '/pay'" method="POST" @submit="submitSettlePayment($event)">
                    @csrf
                    <input type="hidden" name="payment_method" :value="payMethod" />
                    <input type="hidden" name="cash_amount" :value="splitCash" />
                    <input type="hidden" name="online_amount" :value="splitOnline" />

                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-orange-600 to-[#ef7d3b] px-5 py-4 text-white flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm tracking-tight">
                                    Settle Bill &bull; Order #<span x-text="payingOrder?.order_number"></span>
                                </h3>
                                <p class="text-[11px] text-orange-100 font-medium">
                                    <span x-text="payingOrder?.order_type"></span> &bull; 
                                    <span x-text="payingOrder?.table_no || 'Take Away'"></span> &bull; 
                                    <span x-text="payingOrder?.attendant"></span>
                                </p>
                            </div>
                        </div>
                        <button type="button" @click="payingOrder = null" class="text-white/80 hover:text-white text-lg font-bold cursor-pointer">&times;</button>
                    </div>

                    <div class="p-5 space-y-4">
                        <!-- Order Items Breakdown -->
                        <div>
                            <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center justify-between">
                                <span>Order Items Breakdown</span>
                                <span class="text-gray-400 font-normal" x-text="payingOrder?.items?.length + ' items'"></span>
                            </div>
                            <div class="space-y-1.5 max-h-44 overflow-y-auto divide-y divide-gray-100 bg-gray-50/70 p-3 rounded-lg border border-gray-200/80">
                                <template x-for="item in payingOrder?.items" :key="item.id">
                                    <div class="pt-1.5 first:pt-0 flex items-center justify-between text-xs">
                                        <div>
                                            <div class="flex items-center space-x-1.5">
                                                <span class="font-semibold text-gray-800" x-text="item.item_name"></span>
                                                <template x-if="item.portion && item.portion !== 'Regular'">
                                                    <span class="bg-orange-100 text-orange-700 text-[10px] font-bold px-1.5 py-0.5 rounded" x-text="item.portion + ' Plate'"></span>
                                                </template>
                                            </div>
                                            <div class="text-[11px] text-gray-400" x-text="item.quantity + ' × ₹' + Number(item.unit_price).toFixed(2)"></div>
                                            <template x-if="item.notes">
                                                <div class="text-[10px] text-orange-600 italic" x-text="'Note: ' + item.notes"></div>
                                            </template>
                                        </div>
                                        <div class="font-bold text-gray-900 font-mono">
                                            ₹ <span x-text="Number(item.total_price).toFixed(2)"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Total Bill Amount Banner -->
                        <div class="bg-orange-50/80 border border-orange-200 rounded-lg p-3 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold text-gray-700">Total Bill Amount:</span>
                                <div class="text-[11px] text-gray-400">Tax inclusive &bull; No GST</div>
                            </div>
                            <div class="text-xl font-black text-orange-600 font-mono">
                                ₹ <span x-text="Number(payingOrder?.total_amount).toFixed(2)"></span>
                            </div>
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="space-y-2.5">
                            <label class="text-xs font-bold text-gray-700 block">Select Payment Method:</label>
                            
                            <div class="grid grid-cols-3 gap-2">
                                <!-- Cash -->
                                <button 
                                    type="button" 
                                    @click="payMethod = 'Cash'"
                                    :class="payMethod === 'Cash' ? 'border-orange-500 bg-orange-50 text-orange-700 ring-2 ring-orange-200' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'"
                                    class="p-2.5 rounded-lg border text-center font-bold text-xs flex flex-col items-center justify-center space-y-1 transition-all cursor-pointer"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <rect x="2" y="6" width="20" height="12" rx="2" stroke-width="2"/>
                                        <circle cx="12" cy="12" r="3" stroke-width="2"/>
                                    </svg>
                                    <span>Cash</span>
                                </button>

                                <!-- Online -->
                                <button 
                                    type="button" 
                                    @click="payMethod = 'Online'"
                                    :class="payMethod === 'Online' ? 'border-orange-500 bg-orange-50 text-orange-700 ring-2 ring-orange-200' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'"
                                    class="p-2.5 rounded-lg border text-center font-bold text-xs flex flex-col items-center justify-center space-y-1 transition-all cursor-pointer"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Online</span>
                                </button>

                                <!-- Split -->
                                <button 
                                    type="button" 
                                    @click="payMethod = 'Split'"
                                    :class="payMethod === 'Split' ? 'border-orange-500 bg-orange-50 text-orange-700 ring-2 ring-orange-200' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'"
                                    class="p-2.5 rounded-lg border text-center font-bold text-xs flex flex-col items-center justify-center space-y-1 transition-all cursor-pointer"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                    <span>Split</span>
                                </button>
                            </div>

                            <!-- Split Breakdown Inputs -->
                            <div x-show="payMethod === 'Split'" class="bg-orange-50/60 rounded-lg p-3 border border-orange-200 space-y-2.5">
                                <div class="text-[11px] font-bold text-orange-800 uppercase tracking-wide">
                                    Split Payment (Cash &amp; Online)
                                </div>
                                <div class="grid grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="text-[11px] font-semibold text-gray-700 block mb-1">Cash Amount (₹)</label>
                                        <div class="relative">
                                            <span class="absolute left-2.5 top-2 text-xs font-bold text-gray-400">₹</span>
                                            <input 
                                                type="number" 
                                                step="0.01" 
                                                x-model="splitCash" 
                                                placeholder="0.00"
                                                class="w-full text-xs font-mono font-bold bg-white border border-gray-300 rounded-md py-1.5 pl-6 pr-2 text-gray-800 focus:ring-1 focus:ring-orange-500"
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-[11px] font-semibold text-gray-700 block mb-1">Online Amount (₹)</label>
                                        <div class="relative">
                                            <span class="absolute left-2.5 top-2 text-xs font-bold text-gray-400">₹</span>
                                            <input 
                                                type="number" 
                                                step="0.01" 
                                                x-model="splitOnline" 
                                                placeholder="0.00"
                                                class="w-full text-xs font-mono font-bold bg-white border border-gray-300 rounded-md py-1.5 pl-6 pr-2 text-gray-800 focus:ring-1 focus:ring-orange-500"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-[11px] pt-1">
                                    <div>
                                        <template x-if="Math.abs((Number(splitCash || 0) + Number(splitOnline || 0)) - Number(payingOrder?.total_amount || 0)) < 0.01 && (Number(splitCash || 0) > 0 || Number(splitOnline || 0) > 0)">
                                            <span class="text-emerald-700 font-bold flex items-center space-x-1">
                                                <span>✓</span> <span>Amounts match total</span>
                                            </span>
                                        </template>
                                        <template x-if="(Number(splitCash || 0) + Number(splitOnline || 0)) < Number(payingOrder?.total_amount || 0)">
                                            <span class="text-amber-700 font-medium">
                                                Remaining: ₹ <span x-text="(Number(payingOrder?.total_amount || 0) - (Number(splitCash || 0) + Number(splitOnline || 0))).toFixed(2)"></span>
                                            </span>
                                        </template>
                                        <template x-if="(Number(splitCash || 0) + Number(splitOnline || 0)) > Number(payingOrder?.total_amount || 0)">
                                            <span class="text-rose-600 font-bold">
                                                Exceeds total by ₹ <span x-text="((Number(splitCash || 0) + Number(splitOnline || 0)) - Number(payingOrder?.total_amount || 0)).toFixed(2)"></span>
                                            </span>
                                        </template>
                                    </div>
                                    <div class="text-gray-500 text-right">
                                        Sum: <span class="font-mono font-bold text-gray-800">₹ <span x-text="(Number(splitCash || 0) + Number(splitOnline || 0)).toFixed(2)"></span></span> / Total ₹ <span x-text="Number(payingOrder?.total_amount || 0).toFixed(2)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button 
                                type="submit" 
                                class="w-full bg-[#ef7d3b] hover:bg-[#d94e08] text-white text-xs font-bold py-2.5 px-4 rounded-lg shadow-sm hover:shadow transition-all cursor-pointer flex items-center justify-center space-x-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Confirm Payment & Pay</span>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 flex items-center justify-between">
                        <button 
                            type="button" 
                            @click="printOrderSlip(payingOrder)" 
                            class="inline-flex items-center space-x-1.5 text-xs font-bold text-orange-600 hover:text-orange-700 hover:underline cursor-pointer"
                            title="Print KOT / Customer receipt slip"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Print Slip</span>
                        </button>
                        <button 
                            type="button" 
                            @click="payingOrder = null" 
                            class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-md transition-colors cursor-pointer"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Print Order Slip Script -->
    <script>
        function printOrderSlip(order) {
            if (!order || !order.items || order.items.length === 0) {
                alert('No items found for this order.');
                return;
            }

            const orderNo = order.order_number;
            const orderType = order.order_type;
            const tableInfo = order.table_no || (order.order_type === 'Dine In' ? 'Dine In' : 'Take Away');
            const attendant = order.attendant;
            const timeStr = order.order_time;
            const totalAmount = Number(order.total_amount).toFixed(2);

            const itemsRows = order.items.map(item => `
                <tr style="border-bottom: 1px dashed #ccc;">
                    <td style="padding: 6px 2px; text-align: center; vertical-align: top; font-weight: bold; font-size: 13px;">${item.quantity}</td>
                    <td style="padding: 6px 4px; vertical-align: top;">
                        <div style="font-weight: 700; font-size: 12px; color: #111;">${item.item_name}</div>
                        ${item.portion && item.portion !== 'Regular' ? `<span style="display: inline-block; font-size: 10px; font-weight: 800; background: #eee; border: 1px solid #ccc; padding: 0 4px; border-radius: 2px; margin-top: 2px;">[${item.portion} Plate]</span>` : ''}
                        ${item.notes ? `<div style="font-size: 10px; color: #555; font-style: italic; margin-top: 2px;">* ${item.notes}</div>` : ''}
                    </td>
                    <td style="padding: 6px 2px; text-align: right; vertical-align: top; font-family: monospace; font-weight: 700; font-size: 12px;">₹${Number(item.total_price).toFixed(2)}</td>
                </tr>
            `).join('');

            const slipHtml = `
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <title>Order Slip #${orderNo}</title>
                    <style>
                        @page { size: 80mm auto; margin: 4mm; }
                        body {
                            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                            width: 72mm;
                            margin: 0 auto;
                            color: #000;
                            background: #fff;
                            font-size: 12px;
                            line-height: 1.35;
                        }
                        .text-center { text-align: center; }
                        .text-right { text-align: right; }
                        .bold { font-weight: bold; }
                        .dashed { border-top: 1px dashed #000; margin: 7px 0; }
                        .double { border-top: 2px dashed #000; margin: 7px 0; }
                        table { width: 100%; border-collapse: collapse; }
                        .badge {
                            display: inline-block;
                            border: 1.5px solid #000;
                            padding: 2px 8px;
                            font-weight: 900;
                            font-size: 11px;
                            border-radius: 3px;
                            text-transform: uppercase;
                            margin: 4px 0;
                        }
                    </style>
                </head>
                <body>
                    <div class="text-center">
                        <div style="font-size: 16px; font-weight: 900; letter-spacing: 0.5px; text-transform: uppercase;">THE GRAND ROYAL</div>
                        <div style="font-size: 10px; color: #333;">12 Park Street, Kolkata &bull; Ph: +91 98765 43210</div>
                        
                        <div class="double"></div>
                        <div class="badge">ORDER SLIP &bull; KOT</div>
                        <div style="font-size: 17px; font-weight: 900; margin-top: 2px;">ORDER #${orderNo}</div>
                    </div>

                    <div class="dashed"></div>

                    <table style="font-size: 11px;">
                        <tr>
                            <td><strong>Type:</strong> ${orderType}</td>
                            <td class="text-right"><strong>Location:</strong> ${tableInfo}</td>
                        </tr>
                        <tr>
                            <td><strong>Staff:</strong> ${attendant}</td>
                            <td class="text-right"><strong>Time:</strong> ${timeStr}</td>
                        </tr>
                    </table>

                    <div class="dashed"></div>

                    <table>
                        <thead>
                            <tr style="border-bottom: 1.5px solid #000; font-size: 11px; text-transform: uppercase;">
                                <th style="width: 15%; text-align: center; padding-bottom: 4px;">Qty</th>
                                <th style="width: 55%; text-align: left; padding-bottom: 4px;">Item Description</th>
                                <th style="width: 30%; text-align: right; padding-bottom: 4px;">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsRows}
                        </tbody>
                    </table>

                    <div class="dashed"></div>

                    <table style="font-size: 12px;">
                        <tr>
                            <td>Total Items:</td>
                            <td class="text-right bold">${order.items.length} items</td>
                        </tr>
                        <tr style="font-size: 15px;">
                            <td style="font-weight: 900; padding-top: 4px;">GRAND TOTAL:</td>
                            <td class="text-right" style="font-weight: 900; padding-top: 4px; font-family: monospace;">₹ ${totalAmount}</td>
                        </tr>
                        <tr>
                            <td>Bill Status:</td>
                            <td class="text-right bold">${order.status} ${order.payment_method ? ('(' + order.payment_method + ')') : ''}</td>
                        </tr>
                    </table>

                    <div class="double"></div>

                    <div class="text-center" style="font-size: 10px; color: #444; margin-top: 6px;">
                        <div style="font-weight: bold; letter-spacing: 0.5px;">*** KITCHEN &amp; GUEST COPY ***</div>
                        <div style="margin-top: 2px;">Hand this slip to Kitchen or Customer</div>
                        <div style="margin-top: 4px; font-size: 9px; color: #777;">Powered by RESCO Restaurant POS</div>
                    </div>
                </body>
                </html>
            `;

            const printFrame = document.createElement('iframe');
            printFrame.style.position = 'fixed';
            printFrame.style.right = '0';
            printFrame.style.bottom = '0';
            printFrame.style.width = '0';
            printFrame.style.height = '0';
            printFrame.style.border = '0';
            document.body.appendChild(printFrame);

            const frameDoc = printFrame.contentWindow.document;
            frameDoc.open();
            frameDoc.write(slipHtml);
            frameDoc.close();

            setTimeout(() => {
                printFrame.contentWindow.focus();
                printFrame.contentWindow.print();
                setTimeout(() => {
                    if (document.body.contains(printFrame)) {
                        document.body.removeChild(printFrame);
                    }
                }, 1500);
            }, 300);
        }
    </script>
</x-app-layout>
