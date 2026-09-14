<x-app-layout>
    @section('page_title', 'Billing')

    <div class="space-y-6" x-data="{ selectedInvoice: null }">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">Billing &amp; Invoices</h1>
                <p class="text-xs text-gray-500 mt-0.5">Manage customer bills, check payment statuses, and print final thermal invoices.</p>
            </div>

            <div class="text-xs text-gray-500 font-medium">
                Total Invoices: <span class="font-bold text-gray-800 font-mono">{{ $invoices->count() }}</span>
            </div>
        </div>

        <!-- Invoices Table (Tax column removed as requested) -->
        <div class="bg-white rounded-xl border border-gray-200/90 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[720px]">
                    <thead>
                        <tr class="bg-gray-50/70 text-[11px] font-semibold text-gray-600 border-b border-gray-200/70">
                            <th class="py-3 px-5">Invoice #</th>
                            <th class="py-3 px-4">Order ID</th>
                            <th class="py-3 px-4">Customer / Table</th>
                            <th class="py-3 px-4">Date &amp; Time</th>
                            <th class="py-3 px-4 text-right">Subtotal</th>
                            <th class="py-3 px-5 text-right">Total</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-5 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                        @forelse($invoices as $inv)
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="py-3.5 px-5 font-bold text-gray-900 font-mono">
                                INV-{{ $inv->order_number }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-gray-700">
                                #{{ $inv->order_number }}
                            </td>
                            <td class="py-3.5 px-4 font-medium text-gray-800">
                                {{ $inv->table_no ?: ($inv->order_type === 'Dine In' ? 'Dine In' : 'Take Away') }}
                            </td>
                            <td class="py-3.5 px-4 text-gray-500 font-mono text-[11px]">
                                {{ $inv->order_time }}
                            </td>
                            <td class="py-3.5 px-4 text-right text-gray-600 font-mono">
                                ₹ {{ number_format($inv->subtotal, 2) }}
                            </td>
                            <td class="py-3.5 px-5 text-right font-bold text-gray-900 font-mono">
                                ₹ {{ number_format($inv->total_amount, 2) }}
                            </td>
                            <!-- Payment Status Column -->
                            <td class="py-3.5 px-4 text-center">
                                @if($inv->status === 'Complete' || $inv->status === 'Paid')
                                    <div class="inline-flex flex-col items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <svg class="w-3 h-3 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Paid
                                        </span>
                                        @if($inv->payment_method)
                                            <span class="text-[10px] text-gray-500 font-medium mt-0.5">
                                                {{ $inv->payment_method }}
                                                @if($inv->payment_method === 'Split')
                                                    (₹{{ number_format($inv->cash_amount, 0) }}+₹{{ number_format($inv->online_amount, 0) }})
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                @elseif($inv->status === 'Unpaid')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        Unpaid
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-700">
                                        {{ $inv->status }}
                                    </span>
                                @endif
                            </td>
                            <!-- Action Column (View & Print buttons) -->
                            <td class="py-3.5 px-5 text-center">
                                <div class="inline-flex items-center justify-center space-x-2">
                                    <button 
                                        type="button"
                                        @click="selectedInvoice = {{ $inv->toJson() }}"
                                        class="inline-flex items-center space-x-1 text-xs font-semibold text-gray-700 hover:text-orange-600 bg-gray-100 hover:bg-orange-50 border border-gray-200 hover:border-orange-200 px-2.5 py-1 rounded transition-colors cursor-pointer shadow-2xs"
                                        title="View Invoice Details"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <span>View</span>
                                    </button>

                                    <button 
                                        type="button"
                                        @click="printOrderInvoice({{ $inv->toJson() }})"
                                        class="inline-flex items-center space-x-1 text-xs font-bold text-orange-600 hover:text-orange-700 bg-orange-50 hover:bg-orange-100 border border-orange-200 px-2.5 py-1 rounded transition-colors cursor-pointer shadow-2xs"
                                        title="Print Final Thermal Bill Invoice"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        <span>Print</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400 text-xs">
                                No billing records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Invoice View Modal -->
        <div 
            x-show="selectedInvoice" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
        >
            <div 
                @click.outside="selectedInvoice = null"
                class="bg-white rounded-xl shadow-2xl max-w-lg w-full border border-gray-100 overflow-hidden transform transition-all animate-in fade-in zoom-in-95 duration-150 max-h-[92vh] flex flex-col"
            >
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-orange-600 to-[#ef7d3b] px-5 py-4 text-white flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm tracking-tight">
                                Invoice #INV-<span x-text="selectedInvoice?.order_number"></span>
                            </h3>
                            <p class="text-[11px] text-orange-100 font-medium">
                                <span x-text="selectedInvoice?.order_type"></span> &bull; 
                                <span x-text="selectedInvoice?.table_no || 'Take Away'"></span> &bull; 
                                <span x-text="selectedInvoice?.order_time"></span>
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="selectedInvoice = null" class="text-white/80 hover:text-white text-lg font-bold cursor-pointer">&times;</button>
                </div>

                <div class="p-5 space-y-4">
                    <!-- Bill Meta Information -->
                    <div class="grid grid-cols-2 gap-2 bg-gray-50 p-3 rounded-lg text-xs border border-gray-200/70">
                        <div>
                            <span class="text-gray-500 block text-[10px] uppercase font-bold">Location / Table:</span>
                            <span class="font-semibold text-gray-900" x-text="selectedInvoice?.table_no || (selectedInvoice?.order_type === 'Dine In' ? 'Dine In' : 'Take Away')"></span>
                        </div>
                        <div>
                            <span class="text-gray-500 block text-[10px] uppercase font-bold">Staff / Billed By:</span>
                            <span class="font-semibold text-gray-900" x-text="selectedInvoice?.attendant || 'Staff'"></span>
                        </div>
                        <div class="pt-1 border-t border-gray-200/60">
                            <span class="text-gray-500 block text-[10px] uppercase font-bold">Payment Status:</span>
                            <span class="font-bold text-xs" :class="selectedInvoice?.status === 'Complete' || selectedInvoice?.status === 'Paid' ? 'text-emerald-700' : 'text-amber-700'" x-text="selectedInvoice?.status === 'Complete' || selectedInvoice?.status === 'Paid' ? 'Paid' : 'Unpaid'"></span>
                        </div>
                        <div class="pt-1 border-t border-gray-200/60">
                            <span class="text-gray-500 block text-[10px] uppercase font-bold">Payment Mode:</span>
                            <span class="font-semibold text-gray-900 uppercase" x-text="selectedInvoice?.payment_method || '-'"></span>
                        </div>
                    </div>

                    <!-- Items Breakdown -->
                    <div>
                        <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center justify-between">
                            <span>Ordered Items</span>
                            <span class="text-gray-400 font-normal" x-text="(selectedInvoice?.items?.length || 0) + ' items'"></span>
                        </div>
                        <div class="space-y-1.5 max-h-44 overflow-y-auto divide-y divide-gray-100 bg-gray-50/70 p-3 rounded-lg border border-gray-200/80">
                            <template x-for="item in selectedInvoice?.items" :key="item.id">
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

                    <!-- Total Amount Banner -->
                    <div class="bg-orange-50/80 border border-orange-200 rounded-lg p-3 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold text-gray-700">Net Amount Paid:</span>
                            <div class="text-[10px] text-gray-400">Tax inclusive &bull; No GST</div>
                        </div>
                        <div class="text-xl font-black text-orange-600 font-mono">
                            ₹ <span x-text="Number(selectedInvoice?.total_amount).toFixed(2)"></span>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 flex items-center justify-between">
                    <button 
                        type="button" 
                        @click="printOrderInvoice(selectedInvoice)" 
                        class="inline-flex items-center space-x-1.5 text-xs font-bold text-orange-600 hover:text-orange-700 bg-orange-50 hover:bg-orange-100 border border-orange-200 px-3 py-1.5 rounded-md transition-colors cursor-pointer"
                        title="Print Final Bill Invoice on Thermal Printer"
                    >
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Print Bill Invoice</span>
                    </button>
                    <button 
                        type="button" 
                        @click="selectedInvoice = null" 
                        class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-md transition-colors cursor-pointer"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
