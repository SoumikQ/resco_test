<x-app-layout>
    @section('page_title', 'New Order')

    <div class="bg-white rounded-lg border border-gray-200/90 shadow-2xs p-4 sm:p-6 max-w-5xl mx-auto" 
         x-data="{
            orderType: 'Dine In',
            table: '',
            time: '{{ now()->format('d-M-Y H:i:s') }}',
            attendant: 'ChrisThomas - 226',
            categories: {{ Js::from($categories) }},
            catalog: {{ Js::from($foodItems) }},
            items: [
                { category: '', name: '', search: '', portion: 'Full', hasHalf: false, halfPrice: null, fullPrice: 0, notes: '', quantity: 1, price: 0.00, dropdownOpen: false }
            ],
            init() {
                this.items = [
                    { category: '', name: '', search: '', portion: 'Full', hasHalf: false, halfPrice: null, fullPrice: 0, notes: '', quantity: 1, price: 0.00, dropdownOpen: false }
                ];
            },
            getFilteredDishes(category, search) {
                let list = this.catalog;
                if (category && category.trim() !== '') {
                    list = list.filter(c => c.category === category);
                }
                if (search && search.trim() !== '') {
                    const q = search.toLowerCase().trim();
                    const filtered = list.filter(c => c.name.toLowerCase().includes(q));
                    if (filtered.length > 0) {
                        return filtered;
                    }
                    return this.catalog.filter(c => c.name.toLowerCase().includes(q));
                }
                return list;
            },
            selectDish(item, dish) {
                item.name = dish.name;
                item.category = dish.category;
                item.search = dish.name;
                item.hasHalf = Boolean(dish.has_half_portion);
                item.fullPrice = Number(dish.price);
                item.halfPrice = dish.half_price ? Number(dish.half_price) : null;
                
                if (item.hasHalf) {
                    item.portion = 'Full';
                    item.price = item.fullPrice;
                } else {
                    item.portion = 'Regular';
                    item.price = item.fullPrice;
                }
                
                item.dropdownOpen = false;
            },
            onPortionChange(item) {
                if (item.hasHalf) {
                    if (item.portion === 'Half' && item.halfPrice) {
                        item.price = item.halfPrice;
                    } else {
                        item.portion = 'Full';
                        item.price = item.fullPrice;
                    }
                }
            },
            onCategoryChange(item) {
                item.name = '';
                item.price = 0;
                item.search = '';
                item.hasHalf = false;
                item.portion = 'Regular';
                item.dropdownOpen = true;
            },
            addItem() {
                this.items.push({ 
                    category: '', 
                    name: '', 
                    search: '', 
                    portion: 'Full',
                    hasHalf: false,
                    halfPrice: null,
                    fullPrice: 0,
                    notes: '', 
                    quantity: 1, 
                    price: 0.00, 
                    dropdownOpen: false 
                });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            get subtotal() {
                return this.items.reduce((sum, i) => sum + (Number(i.quantity) * Number(i.price) || 0), 0);
            },
            get grandTotal() {
                return this.subtotal;
            },
            showPaymentModal: false,
            paymentStep: 'prompt',
            paymentAction: 'pay_later',
            paymentMethod: 'Cash',
            splitCash: '',
            splitOnline: '',
            validateAndOpenCheckout() {
                if (this.orderType === 'Dine In' && (!this.table || this.table === '')) {
                    alert('Please select a Table before proceeding with a Dine In order.');
                    return;
                }
                const validItems = this.items.filter(i => i.name && i.name.trim() !== '');
                if (validItems.length === 0) {
                    alert('Please select at least one menu item before submitting the order.');
                    return;
                }
                this.splitCash = '';
                this.splitOnline = '';
                this.paymentStep = 'prompt';
                this.paymentMethod = 'Cash';
                this.showPaymentModal = true;
            },
            submitPayLater() {
                this.paymentAction = 'pay_later';
                this.paymentMethod = null;
                this.$nextTick(() => {
                    this.$refs.orderForm.submit();
                });
            },
            submitPayNow() {
                if (this.paymentMethod === 'Split') {
                    const cash = parseFloat(this.splitCash) || 0;
                    const online = parseFloat(this.splitOnline) || 0;
                    const sum = Math.round((cash + online) * 100) / 100;
                    const total = Math.round(this.grandTotal * 100) / 100;

                    if (cash <= 0 && online <= 0) {
                        alert('Please enter Cash and Online split amounts.');
                        return;
                    }
                    if (Math.abs(sum - total) > 0.01) {
                        alert('Split amounts (Cash: ₹' + cash.toFixed(2) + ' + Online: ₹' + online.toFixed(2) + ' = ₹' + sum.toFixed(2) + ') must equal Total Amount ₹' + total.toFixed(2));
                        return;
                    }
                }

                // Immediately trigger final thermal invoice print
                const validItems = this.items.filter(i => i.name && i.name.trim() !== '');
                if (typeof window.printOrderInvoice === 'function') {
                    window.printOrderInvoice({
                        order_number: '{{ $nextOrderNumber }}',
                        order_type: this.orderType,
                        table_no: this.orderType === 'Dine In' ? ('Table ' + this.table) : 'Take Away',
                        attendant: this.attendant,
                        order_time: this.time || (new Date()).toLocaleString('en-IN'),
                        total_amount: this.grandTotal,
                        payment_method: this.paymentMethod,
                        cash_amount: this.paymentMethod === 'Split' ? parseFloat(this.splitCash || 0) : (this.paymentMethod === 'Cash' ? this.grandTotal : 0),
                        online_amount: this.paymentMethod === 'Split' ? parseFloat(this.splitOnline || 0) : (this.paymentMethod === 'Online' ? this.grandTotal : 0),
                        items: validItems.map(i => ({
                            item_name: i.name,
                            quantity: i.quantity,
                            portion: i.portion,
                            notes: i.notes,
                            unit_price: i.price,
                            total_price: Number(i.quantity) * Number(i.price)
                        }))
                    });
                }

                this.paymentAction = 'pay_now';
                setTimeout(() => {
                    this.$refs.orderForm.submit();
                }, 400);
            },
            printSlip() {
                if (this.orderType === 'Dine In' && (!this.table || this.table === '')) {
                    alert('Please select a Table before printing the slip.');
                    return;
                }
                const validItems = this.items.filter(i => i.name && i.name.trim() !== '');
                if (validItems.length === 0) {
                    alert('Please select at least one item before printing the slip.');
                    return;
                }
                window.printOrderKOTSlip({
                    order_number: '{{ $nextOrderNumber }}',
                    order_type: this.orderType,
                    table_no: this.orderType === 'Dine In' ? ('Table ' + this.table) : 'Take Away',
                    attendant: this.attendant,
                    order_time: this.time || (new Date()).toLocaleString('en-IN'),
                    total_amount: this.grandTotal,
                    items: validItems.map(i => ({
                        item_name: i.name,
                        quantity: i.quantity,
                        portion: i.portion,
                        notes: i.notes,
                        unit_price: i.price,
                        total_price: Number(i.quantity) * Number(i.price)
                    }))
                });
            }
         }"
    >
        <!-- Form Header -->
        <div class="border-b border-gray-100 pb-3 mb-4 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-gray-900 tracking-tight">New Order</h1>
                <p class="text-[11px] text-gray-500 mt-0.5">Order #{{ $nextOrderNumber }} &bull; Currency: <strong class="text-gray-700">Indian Rupee (₹)</strong></p>
            </div>
            <a href="{{ route('restaurant.orders') }}" class="text-xs font-medium text-orange-600 hover:text-orange-700 transition-colors">
                &larr; Back to Orders
            </a>
        </div>

        <form x-ref="orderForm" @submit.prevent="validateAndOpenCheckout()" action="{{ route('restaurant.orders.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="payment_action" :value="paymentAction" />
            <input type="hidden" name="payment_method" :value="paymentMethod" />
            <input type="hidden" name="cash_amount" :value="splitCash" />
            <input type="hidden" name="online_amount" :value="splitOnline" />

            <!-- 1. Order Type (Clean Balanced Radios) -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-center bg-gray-50/70 p-2.5 rounded-md border border-gray-200/70">
                <label class="sm:col-span-2 text-xs font-semibold text-gray-700">
                    Order Type <span class="text-red-500 font-bold">*</span>
                </label>
                <div class="sm:col-span-10 flex items-center space-x-5">
                    <label class="inline-flex items-center cursor-pointer text-xs font-medium text-gray-800 hover:text-orange-600 transition-colors">
                        <input 
                            type="radio" 
                            name="order_type" 
                            value="Dine In" 
                            x-model="orderType"
                            class="w-3.5 h-3.5 text-orange-600 focus:ring-orange-500 border-gray-300 mr-2 cursor-pointer"
                        />
                        <span>Dine In</span>
                    </label>

                    <label class="inline-flex items-center cursor-pointer text-xs font-medium text-gray-800 hover:text-orange-600 transition-colors">
                        <input 
                            type="radio" 
                            name="order_type" 
                            value="Take Away" 
                            x-model="orderType"
                            class="w-3.5 h-3.5 text-orange-600 focus:ring-orange-500 border-gray-300 mr-2 cursor-pointer"
                        />
                        <span>Take Away</span>
                    </label>
                </div>
            </div>

            <!-- 2. Table Dropdown -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-center" x-show="orderType === 'Dine In'">
                <label class="sm:col-span-2 text-xs font-semibold text-gray-700">
                    Table
                </label>
                <div class="sm:col-span-4">
                    <select 
                        name="table" 
                        x-model="table"
                        class="w-full h-8.5 text-xs font-medium bg-white border border-gray-300 rounded-md py-1 px-2.5 text-gray-800 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 cursor-pointer shadow-2xs"
                    >
                        <option value="">Select Table</option>
                        @foreach($tables as $t)
                            <option value="{{ $t }}">Table {{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 3. Time input -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-center">
                <label class="sm:col-span-2 text-xs font-semibold text-gray-700">
                    Time
                </label>
                <div class="sm:col-span-4 relative">
                    <div class="relative flex items-center">
                        <input 
                            type="text" 
                            name="order_time" 
                            x-model="time"
                            class="w-full h-8.5 text-xs font-mono bg-white border border-gray-300 rounded-md py-1 px-2.5 pr-8 text-gray-800 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 shadow-2xs"
                        />
                        <div class="absolute right-2.5 text-gray-400 pointer-events-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/>
                                <line x1="16" y1="2" x2="16" y2="6" stroke-width="2"/>
                                <line x1="8" y1="2" x2="8" y2="6" stroke-width="2"/>
                                <line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Order Items Section (Balanced Sleek Sizing + Stepper) -->
            <div class="pt-2 border-t border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs font-bold text-gray-900">
                        Order Items <span class="text-red-500">*</span>
                    </div>
                    <span class="text-[11px] text-gray-400">Use &plus; and &minus; to adjust quantity</span>
                </div>

                <!-- Items Grid Header -->
                <div class="hidden sm:grid grid-cols-12 gap-2 bg-gray-50/90 border border-gray-200 rounded-t-md px-2.5 py-2 text-[11px] font-bold text-gray-700 uppercase tracking-wider">
                    <div class="col-span-2"><span class="text-red-500 mr-0.5">*</span>Category</div>
                    <div class="col-span-3"><span class="text-red-500 mr-0.5">*</span>Item Name (Search)</div>
                    <div class="col-span-2">Portion</div>
                    <div class="col-span-1">Notes</div>
                    <div class="col-span-2 text-center"><span class="text-red-500 mr-0.5">*</span>Quantity</div>
                    <div class="col-span-2 text-right pr-2">Price (₹)</div>
                </div>

                <!-- Items List Container -->
                <div class="border-x border-b border-gray-200 rounded-b-md divide-y divide-gray-100 sm:divide-y sm:divide-gray-100 px-2 sm:px-2.5 py-2 bg-white space-y-3 sm:space-y-0 shadow-2xs">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="bg-gray-50/80 sm:bg-transparent rounded-xl p-3 sm:p-0 border border-gray-200/70 sm:border-0 grid grid-cols-1 sm:grid-cols-12 gap-2.5 sm:gap-2 items-center sm:py-2">
                            
                            <!-- 1. Selectable Category Dropdown -->
                            <div class="sm:col-span-2">
                                <label class="sm:hidden text-[11px] font-bold text-gray-700 block mb-1">Category <span class="text-red-500">*</span></label>
                                <select 
                                    :name="'items[' + index + '][category]'"
                                    x-model="item.category"
                                    @change="onCategoryChange(item)"
                                    class="w-full h-9 sm:h-8.5 text-xs font-medium bg-white border border-gray-300 rounded-lg sm:rounded-md py-1 px-2.5 sm:px-2 text-gray-800 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 cursor-pointer shadow-2xs"
                                >
                                    <option value="">Select Category</option>
                                    <template x-for="cat in categories" :key="cat">
                                        <option :value="cat" x-text="cat" :selected="cat === item.category"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- 2. Searchable Item Name Combobox -->
                            <div class="sm:col-span-3 relative" @click.outside="item.dropdownOpen = false">
                                <label class="sm:hidden text-[11px] font-bold text-gray-700 block mb-1">Item Name <span class="text-red-500">*</span></label>
                                
                                <input type="hidden" :name="'items[' + index + '][name]'" :value="item.name" required />

                                <div class="relative flex items-center">
                                    <div class="absolute left-2.5 text-gray-400 pointer-events-none">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle cx="11" cy="11" r="7" stroke-width="2"/>
                                            <line x1="21" y1="21" x2="16" y2="16" stroke-width="2"/>
                                        </svg>
                                    </div>

                                    <input 
                                        type="text" 
                                        x-model="item.search"
                                        @focus="item.dropdownOpen = true"
                                        @input="item.dropdownOpen = true"
                                        placeholder="Select Item..."
                                        class="w-full h-9 sm:h-8.5 text-xs font-medium bg-white border border-gray-300 rounded-lg sm:rounded-md py-1 pl-7 pr-6 text-gray-800 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 shadow-2xs"
                                        autocomplete="off"
                                    />

                                    <button 
                                        type="button" 
                                        @click="item.dropdownOpen = !item.dropdownOpen"
                                        class="absolute right-2 text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer"
                                    >
                                        <svg class="w-3 h-3 transition-transform" :class="item.dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Search Results Dropdown List -->
                                <div 
                                    x-show="item.dropdownOpen" 
                                    x-cloak
                                    class="absolute z-30 top-full left-0 right-0 mt-1 max-h-52 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-lg divide-y divide-gray-100"
                                >
                                    <div class="px-2.5 py-1 text-[10px] font-bold text-gray-400 bg-gray-50 uppercase tracking-wider flex items-center justify-between">
                                        <span>Select Item</span>
                                        <span x-text="item.category ? item.category : 'All Dishes'"></span>
                                    </div>
                                    <template x-for="c in getFilteredDishes(item.category, item.search)" :key="c.name">
                                        <div 
                                            @click="selectDish(item, c)"
                                            class="px-2.5 py-2 text-xs hover:bg-orange-50 cursor-pointer flex items-center justify-between transition-colors group"
                                            :class="item.name === c.name ? 'bg-orange-50/70 font-semibold' : ''"
                                        >
                                            <div>
                                                <div class="flex items-center space-x-1.5">
                                                    <span class="text-gray-800 group-hover:text-orange-600 font-medium" x-text="c.name"></span>
                                                    <span x-show="c.has_half_portion" class="bg-orange-100 text-orange-700 text-[9px] font-bold px-1.5 py-0.5 rounded">Half/Full</span>
                                                </div>
                                                <span class="text-[10px] text-gray-400 block" x-text="c.category"></span>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-orange-600 font-bold font-mono text-[11px]" x-text="'₹ ' + Number(c.price).toFixed(2)"></span>
                                                <span x-show="c.has_half_portion" class="text-gray-400 font-mono text-[9px] block" x-text="'Half ₹ ' + Number(c.half_price).toFixed(2)"></span>
                                            </div>
                                        </div>
                                    </template>
                                    <div x-show="getFilteredDishes(item.category, item.search).length === 0" class="px-3 py-2 text-xs text-gray-400 text-center">
                                        No dishes found
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile Row 2 (Portion & Notes side by side) / Desktop columns -->
                            <div class="grid grid-cols-2 sm:contents gap-2 sm:gap-0">
                                <!-- 3. Portion Selector (Full / Half / Regular) -->
                                <div class="sm:col-span-2">
                                    <label class="sm:hidden text-[11px] font-bold text-gray-700 block mb-1">Portion</label>
                                    <select 
                                        :name="'items[' + index + '][portion]'"
                                        x-model="item.portion"
                                        @change="onPortionChange(item)"
                                        :disabled="!item.hasHalf"
                                        class="w-full h-9 sm:h-8.5 text-xs font-medium bg-white border border-gray-300 rounded-lg sm:rounded-md py-1 px-2 text-gray-800 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 cursor-pointer shadow-2xs disabled:bg-gray-50 disabled:text-gray-400 disabled:cursor-not-allowed"
                                    >
                                        <template x-if="!item.name">
                                            <option value="Regular">-</option>
                                        </template>
                                        <template x-if="item.name && item.hasHalf">
                                            <optgroup label="Select Portion">
                                                <option value="Full" x-text="'Full (₹ ' + Number(item.fullPrice).toFixed(0) + ')'"></option>
                                                <option value="Half" x-text="'Half (₹ ' + Number(item.halfPrice).toFixed(0) + ')'"></option>
                                            </optgroup>
                                        </template>
                                        <template x-if="item.name && !item.hasHalf">
                                            <option value="Regular">Regular</option>
                                        </template>
                                    </select>
                                </div>

                                <!-- 4. Notes (Text Input) -->
                                <div class="sm:col-span-1">
                                    <label class="sm:hidden text-[11px] font-bold text-gray-700 block mb-1">Notes</label>
                                    <input 
                                        type="text" 
                                        :name="'items[' + index + '][notes]'"
                                        x-model="item.notes" 
                                        placeholder="Notes..."
                                        class="w-full h-9 sm:h-8.5 text-xs bg-white border border-gray-300 rounded-lg sm:rounded-md py-1 px-2.5 text-gray-700 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 shadow-2xs"
                                    />
                                </div>
                            </div>

                            <!-- Mobile Row 3 (Quantity & Price & Delete) / Desktop columns -->
                            <div class="flex items-center justify-between sm:contents pt-1 sm:pt-0 border-t border-gray-200/50 sm:border-0">
                                <!-- Quantity Stepper -->
                                <div class="sm:col-span-2 flex flex-col items-start sm:items-center sm:justify-center">
                                    <label class="sm:hidden text-[11px] font-bold text-gray-700 block mb-1">Quantity <span class="text-red-500">*</span></label>
                                    <div class="flex items-center border border-gray-300 rounded-lg sm:rounded-md bg-white shadow-2xs h-9 sm:h-8.5 w-26 sm:w-[84px] overflow-hidden">
                                        <!-- Minus Button -->
                                        <button 
                                            type="button" 
                                            @click="if (item.quantity > 1) item.quantity--" 
                                            class="w-8 sm:w-6.5 h-full flex items-center justify-center text-gray-600 hover:text-orange-600 hover:bg-orange-50 font-bold text-sm transition-colors border-r border-gray-200 select-none cursor-pointer"
                                            title="Decrease quantity"
                                        >
                                            &minus;
                                        </button>

                                        <!-- Number Input -->
                                        <input 
                                            type="number" 
                                            min="1" 
                                            :name="'items[' + index + '][quantity]'"
                                            x-model.number="item.quantity"
                                            class="w-full text-center text-xs font-bold text-gray-900 border-none focus:ring-0 p-0 no-spin [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                        />

                                        <!-- Plus Button -->
                                        <button 
                                            type="button" 
                                            @click="item.quantity++" 
                                            class="w-8 sm:w-6.5 h-full flex items-center justify-center text-gray-600 hover:text-orange-600 hover:bg-orange-50 font-bold text-sm transition-colors border-l border-gray-200 select-none cursor-pointer"
                                            title="Increase quantity"
                                        >
                                            &plus;
                                        </button>
                                    </div>
                                </div>

                                <!-- Unit Price in ₹ & Delete action -->
                                <div class="sm:col-span-2 flex flex-col sm:flex-row items-end sm:items-center justify-end space-y-1 sm:space-y-0 sm:space-x-1.5">
                                    <label class="sm:hidden text-[11px] font-bold text-gray-700 block mb-0.5">Price (₹)</label>
                                    <div class="flex items-center space-x-1.5 w-full sm:w-auto justify-end">
                                        <div class="relative flex items-center w-28 sm:w-full">
                                            <span class="absolute left-2.5 text-[11px] text-gray-400 font-semibold">₹</span>
                                            <input 
                                                type="number" 
                                                step="0.01" 
                                                :name="'items[' + index + '][price]'"
                                                x-model.number="item.price"
                                                class="w-full h-9 sm:h-8.5 text-xs font-bold font-mono bg-white border border-gray-300 rounded-lg sm:rounded-md py-1 pl-5 sm:pl-4.5 pr-2 text-right text-gray-900 focus:ring-1 focus:ring-orange-500 shadow-2xs no-spin [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                            />
                                        </div>
                                        
                                        <button 
                                            type="button" 
                                            @click="removeItem(index)" 
                                            class="w-8 h-8 sm:w-6 sm:h-6 flex items-center justify-center rounded-lg sm:rounded text-gray-400 hover:text-red-600 hover:bg-red-50 text-xs font-bold transition-colors cursor-pointer"
                                            title="Remove item"
                                            x-show="items.length > 1"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </template>
                </div>

                <!-- Add New Button & Live INR Bill Calculation -->
                <div class="mt-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 bg-orange-50/50 p-2.5 rounded-lg border border-orange-100">
                    <button 
                        type="button" 
                        @click="addItem()"
                        class="inline-flex items-center text-xs font-bold text-orange-600 hover:text-orange-700 bg-white hover:bg-orange-100/60 border border-orange-200 py-1.5 px-3 rounded-md transition-colors cursor-pointer shadow-2xs"
                    >
                        <span class="text-base mr-1 leading-none font-bold">+</span> Add New Item
                    </button>

                    <div class="flex flex-wrap items-center gap-4 sm:gap-6 text-xs text-gray-700">
                        <div>
                            Total Items: <strong class="font-mono text-gray-900" x-text="items.filter(i => i.name).length"></strong>
                        </div>
                        <div class="text-xs sm:text-sm font-bold text-gray-900 border-l border-gray-300 pl-3">
                            Total: <span class="text-orange-600 font-extrabold font-mono text-sm sm:text-base">₹ <span x-text="grandTotal.toFixed(2)"></span></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 5. Attendant Dropdown -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-center pt-1">
                <label class="sm:col-span-2 text-xs font-semibold text-gray-700">
                    Attendant <span class="text-red-500 font-bold">*</span>
                </label>
                <div class="sm:col-span-4">
                    <select 
                        name="attendant" 
                        x-model="attendant"
                        class="w-full h-8.5 text-xs font-medium bg-white border border-gray-300 rounded-md py-1 px-2.5 text-gray-800 focus:ring-1 focus:ring-orange-500 cursor-pointer shadow-2xs"
                    >
                        @foreach($attendants as $att)
                            <option value="{{ $att }}">{{ $att }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 6. Form Action Buttons -->
            <div class="pt-4 border-t border-gray-100 flex items-center space-x-3">
                <button 
                    type="submit" 
                    class="bg-[#ef7d3b] hover:bg-[#d94e08] text-white text-xs font-bold px-5 py-2 rounded-md shadow-2xs hover:shadow-xs transition-all focus:ring-2 focus:ring-orange-400 focus:outline-none cursor-pointer"
                >
                    Submit Order
                </button>

                <button 
                    type="button" 
                    @click="init(); table = '2'; orderType = 'Dine In';"
                    class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold px-5 py-2 rounded-md shadow-2xs transition-colors focus:outline-none cursor-pointer"
                >
                    Reset Form
                </button>
            </div>

        </form>

        <!-- Payment & Checkout Modal (Pay / Pay Later Flow) -->
        <div 
            x-show="showPaymentModal" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4"
        >
            <div 
                @click.outside="showPaymentModal = false"
                class="bg-white rounded-xl shadow-2xl max-w-md w-full border border-gray-100 overflow-hidden transform transition-all animate-in fade-in zoom-in-95 duration-150"
            >
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-orange-600 to-[#ef7d3b] px-5 py-4 text-white flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm tracking-tight">Order Checkout</h3>
                            <p class="text-[11px] text-orange-100 font-medium">Choose how the customer will settle the bill</p>
                        </div>
                    </div>
                    <button @click="showPaymentModal = false" class="text-white/80 hover:text-white text-lg font-bold">&times;</button>
                </div>

                <!-- Bill Amount Summary Strip -->
                <div class="bg-orange-50/70 border-b border-orange-100 px-5 py-3 flex items-center justify-between">
                    <div class="text-xs text-gray-600">
                        <span class="font-medium">Total Bill:</span>
                        <span class="text-gray-400 text-[11px]"> (<span x-text="items.filter(i => i.name).length"></span> items)</span>
                    </div>
                    <div class="text-lg font-black text-orange-600 font-mono">
                        ₹ <span x-text="grandTotal.toFixed(2)"></span>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-5 space-y-4">
                    <!-- COUPON CARD: Print Order Items Slip Button -->
                    <div 
                        @click="printSlip()"
                        class="relative bg-gradient-to-r from-orange-50/90 via-amber-50/80 to-orange-50/90 border-2 border-dashed border-orange-400 hover:border-orange-500 rounded-xl p-3 shadow-2xs hover:shadow-md transition-all duration-200 cursor-pointer overflow-hidden group select-none flex items-center justify-between"
                        title="Click to print order items list slip (KOT / Customer Copy)"
                    >
                        <!-- Authentic Coupon Ticket Notches -->
                        <div class="absolute -left-2.5 top-1/2 -translate-y-1/2 w-5 h-5 bg-white border-r-2 border-dashed border-orange-400 rounded-full shadow-inner pointer-events-none"></div>
                        <div class="absolute -right-2.5 top-1/2 -translate-y-1/2 w-5 h-5 bg-white border-l-2 border-dashed border-orange-400 rounded-full shadow-inner pointer-events-none"></div>

                        <!-- Left: Coupon Main Body -->
                        <div class="pl-2.5 pr-2 flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-orange-600 text-white shadow-2xs">
                                    <svg class="w-2.5 h-2.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                    </svg>
                                    ORDER SLIP
                                </span>
                                <span class="text-[10px] font-bold text-gray-500 font-mono">
                                    #{{ $nextOrderNumber }}
                                </span>
                            </div>

                            <div class="text-sm font-black text-gray-900 tracking-tight group-hover:text-orange-600 transition-colors flex items-center space-x-1.5">
                                <span>Print Order Items List</span>
                            </div>
                        
                            <div class="mt-2 flex items-center space-x-2 text-[10px] text-gray-600">
                                <span class="bg-white/95 border border-orange-200/80 px-2 py-0.5 rounded font-semibold text-gray-700 shadow-2xs">
                                    📋 <span x-text="items.filter(i => i.name).length"></span> Items
                                </span>
                                <span class="bg-white/95 border border-orange-200/80 px-2 py-0.5 rounded font-semibold text-gray-700 shadow-2xs">
                                    📍 <span x-text="orderType === 'Dine In' ? (table ? ('Table ' + table) : 'Select Table') : 'Take Away'"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Perforated Tear-off Stub Divider -->
                        <div class="relative flex flex-col items-center justify-center pl-3 pr-2 border-l-2 border-dashed border-orange-300 group-hover:border-orange-400 transition-colors">
                            <div class="w-10 h-10 rounded-full bg-white group-hover:bg-orange-600 text-orange-600 group-hover:text-white border border-orange-200 group-hover:border-orange-600 flex items-center justify-center shadow-xs transition-all duration-200 group-hover:scale-105">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                            </div>
                            <span class="text-[10px] font-black uppercase text-orange-600 tracking-wider mt-1 group-hover:text-orange-700">PRINT</span>
                        </div>
                    </div>

                    <!-- Step 1: Prompt between Pay and Pay Later -->
                    <div x-show="paymentStep === 'prompt'" class="space-y-3">
                        <p class="text-xs text-gray-600 font-medium mb-3">
                            Select an action to proceed with this order:
                        </p>

                        <!-- Pay Option Card -->
                        <button 
                            type="button" 
                            @click="paymentStep = 'choose_payment'"
                            class="w-full text-left p-3.5 rounded-lg border-2 border-orange-400/80 hover:border-orange-500 bg-orange-50/40 hover:bg-orange-50 transition-all flex items-center justify-between group cursor-pointer"
                        >
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg bg-orange-500 text-white flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 text-sm flex items-center space-x-1.5">
                                        <span>Pay Now</span>
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-0.5">Collect payment now via Cash, Online, or Split</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-orange-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Pay Later Option Card -->
                        <button 
                            type="button" 
                            @click="submitPayLater()"
                            class="w-full text-left p-3.5 rounded-lg border border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50/80 transition-all flex items-center justify-between group cursor-pointer"
                        >
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800 text-sm flex items-center space-x-1.5">
                                        <span>Pay Later</span>
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-0.5">Keep order as Unpaid. Settle bill when guest leaves.</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Step 2: Pay Options (Cash, Online, Split) -->
                    <div x-show="paymentStep === 'choose_payment'" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-gray-700">Select Payment Method:</label>
                            <button 
                                type="button" 
                                @click="paymentStep = 'prompt'"
                                class="text-[11px] text-orange-600 hover:underline font-semibold cursor-pointer"
                            >
                                &larr; Back
                            </button>
                        </div>

                        <!-- 3 Payment Method Pills -->
                        <div class="grid grid-cols-3 gap-2">
                            <!-- Cash -->
                            <button 
                                type="button" 
                                @click="paymentMethod = 'Cash'"
                                :class="paymentMethod === 'Cash' ? 'border-orange-500 bg-orange-50 text-orange-700 ring-2 ring-orange-200' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'"
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
                                @click="paymentMethod = 'Online'"
                                :class="paymentMethod === 'Online' ? 'border-orange-500 bg-orange-50 text-orange-700 ring-2 ring-orange-200' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'"
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
                                @click="paymentMethod = 'Split'"
                                :class="paymentMethod === 'Split' ? 'border-orange-500 bg-orange-50 text-orange-700 ring-2 ring-orange-200' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'"
                                class="p-2.5 rounded-lg border text-center font-bold text-xs flex flex-col items-center justify-center space-y-1 transition-all cursor-pointer"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                <span>Split</span>
                            </button>
                        </div>

                        <!-- Cash Notice -->
                        <div x-show="paymentMethod === 'Cash'" class="bg-gray-50 rounded-lg p-3 text-xs text-gray-600 border border-gray-200/80">
                            <div class="flex justify-between items-center">
                                <span>Cash Amount to Collect:</span>
                                <strong class="text-gray-900 font-mono text-sm">₹ <span x-text="grandTotal.toFixed(2)"></span></strong>
                            </div>
                        </div>

                        <!-- Online Notice -->
                        <div x-show="paymentMethod === 'Online'" class="bg-gray-50 rounded-lg p-3 text-xs text-gray-600 border border-gray-200/80">
                            <div class="flex justify-between items-center">
                                <span>Online Amount (UPI / Card / QR):</span>
                                <strong class="text-gray-900 font-mono text-sm">₹ <span x-text="grandTotal.toFixed(2)"></span></strong>
                            </div>
                        </div>

                        <!-- Split Inputs -->
                        <div x-show="paymentMethod === 'Split'" class="bg-orange-50/60 rounded-lg p-3 border border-orange-200/80 space-y-2.5">
                            <div class="text-[11px] font-bold text-orange-800 uppercase tracking-wide">
                                Split Breakdown (Cash &amp; Online)
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
                                    <template x-if="Math.abs((Number(splitCash || 0) + Number(splitOnline || 0)) - grandTotal) < 0.01 && (Number(splitCash || 0) > 0 || Number(splitOnline || 0) > 0)">
                                        <span class="text-emerald-700 font-bold flex items-center space-x-1">
                                            <span>✓</span> <span>Amounts match total</span>
                                        </span>
                                    </template>
                                    <template x-if="(Number(splitCash || 0) + Number(splitOnline || 0)) < grandTotal">
                                        <span class="text-amber-700 font-medium">
                                            Remaining: ₹ <span x-text="(grandTotal - (Number(splitCash || 0) + Number(splitOnline || 0))).toFixed(2)"></span>
                                        </span>
                                    </template>
                                    <template x-if="(Number(splitCash || 0) + Number(splitOnline || 0)) > grandTotal">
                                        <span class="text-rose-600 font-bold">
                                            Exceeds total by ₹ <span x-text="((Number(splitCash || 0) + Number(splitOnline || 0)) - grandTotal).toFixed(2)"></span>
                                        </span>
                                    </template>
                                </div>
                                <div class="text-gray-500 text-right">
                                    Sum: <span class="font-mono font-bold text-gray-800">₹ <span x-text="(Number(splitCash || 0) + Number(splitOnline || 0)).toFixed(2)"></span></span> / Total ₹ <span x-text="grandTotal.toFixed(2)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button 
                                type="button" 
                                @click="submitPayNow()"
                                class="w-full bg-[#ef7d3b] hover:bg-[#d94e08] text-white text-xs font-bold py-2.5 px-4 rounded-lg shadow-sm hover:shadow transition-all cursor-pointer flex items-center justify-center space-x-2"
                            >
                                <span>Complete Bill &amp; Pay</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500">
                    <span class="text-[11px]">Table: <strong class="text-gray-700" x-text="orderType === 'Dine In' ? ('Table ' + table) : 'Take Away'"></strong></span>
                    <button 
                        type="button" 
                        @click="showPaymentModal = false" 
                        class="text-gray-500 hover:text-gray-800 font-semibold text-xs transition-colors cursor-pointer"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Print Order Slip Script -->
    <script>
        window.printOrderKOTSlip = function(order) {
            if (!order || !order.items || order.items.length === 0) {
                alert('No items found to print.');
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
                        <div class="dashed"></div>
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

                    <div class="text-center" style="font-size: 10px; color: #444; margin-top: 6px;">
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
        };
    </script>
</x-app-layout>
