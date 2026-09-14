<x-app-layout>
    @section('page_title', 'Food / Menu')

    <div class="space-y-6" x-data="{ addModalOpen: false, editModalOpen: false, currentDish: {} }">

        <!-- Flash Success Notification -->
        @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-semibold flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
        </div>
        @endif

        <!-- Top Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">Food & Menu Catalog</h1>
                <p class="text-xs text-gray-500 mt-0.5">Manage recipe catalog, pricing in Indian Rupee (₹), and live stock availability.</p>
            </div>
            
            <button 
                @click="addModalOpen = true"
                class="inline-flex items-center justify-center bg-[#ef7d3b] hover:bg-[#d94e08] text-white text-xs font-semibold px-4 py-2.5 rounded-md shadow-2xs transition-colors cursor-pointer"
            >
                <span class="text-base mr-1.5 leading-none">+</span> Add Dish
            </button>
        </div>

        <!-- Categories Filter Tabs -->
        <div class="flex items-center space-x-2 overflow-x-auto py-1 pb-2 text-xs no-scrollbar">
            <a 
                href="{{ route('restaurant.menu') }}" 
                class="px-3.5 py-2 rounded-lg font-semibold shrink-0 transition-colors {{ $selectedCategory === 'all' ? 'bg-[#ea580c] text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}"
            >
                All Items ({{ $dishes->count() }})
            </a>
            @foreach($categories as $cat)
            <a 
                href="{{ route('restaurant.menu', ['category' => $cat->slug]) }}" 
                class="px-3.5 py-2 rounded-lg font-semibold shrink-0 transition-colors {{ $selectedCategory === $cat->slug ? 'bg-[#ea580c] text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}"
            >
                {{ $cat->name }} ({{ $cat->food_items_count }})
            </a>
            @endforeach
        </div>

        <!-- Food Items Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
            @forelse($dishes as $dish)
            <div class="bg-white rounded-lg border border-gray-200/90 shadow-2xs p-4 flex flex-col justify-between hover:shadow-xs transition-shadow">
                <div>
                    <!-- Category Badge, Veg Indicator & Status -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-[10px] font-semibold tracking-wide uppercase px-2 py-0.5 rounded bg-gray-100 text-gray-600">
                                {{ $dish->category?->name ?? 'Main Menu' }}
                            </span>
                            @if($dish->is_veg)
                                <span class="w-3.5 h-3.5 border border-emerald-600 flex items-center justify-center p-0.5 rounded-xs" title="Pure Veg">
                                    <span class="w-1.5 h-1.5 bg-emerald-600 rounded-full"></span>
                                </span>
                            @else
                                <span class="w-3.5 h-3.5 border border-rose-600 flex items-center justify-center p-0.5 rounded-xs" title="Non-Veg">
                                    <span class="w-1.5 h-1.5 bg-rose-600 rounded-full"></span>
                                </span>
                            @endif
                        </div>
                        
                        <!-- Toggle Availability Status Form -->
                        <form action="{{ route('restaurant.menu.toggle', $dish->id) }}" method="POST">
                            @csrf
                            <button 
                                type="submit" 
                                class="text-[11px] font-medium px-2 py-0.5 rounded transition-colors {{ $dish->status === 'Available' ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : ($dish->status === 'Low Stock' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700 hover:bg-rose-200') }}"
                                title="Click to toggle availability"
                            >
                                {{ $dish->status }}
                            </button>
                        </form>
                    </div>

                    <!-- Dish Name -->
                    <h3 class="font-bold text-gray-900 text-base mb-1">
                        {{ $dish->name }}
                    </h3>

                    <p class="text-xs text-gray-500 mb-3 line-clamp-2">
                        {{ $dish->description ?: 'Freshly prepared restaurant special.' }}
                    </p>

                    <!-- Prep Time -->
                    @if($dish->prep_time)
                    <div class="flex items-center text-xs text-gray-500 mb-4 gap-1.5">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $dish->prep_time }}</span>
                    </div>
                    @else
                    <div class="mb-4"></div>
                    @endif
                </div>

                <!-- Footer: Price in ₹ & Actions -->
                <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                    <div>
                        <div class="text-base font-bold text-[#ea580c] font-mono">
                            ₹ {{ number_format($dish->price, 2) }}
                            @if($dish->has_half_portion)
                                <span class="text-[10px] text-gray-500 font-sans font-normal">(Full)</span>
                            @endif
                        </div>
                        @if($dish->has_half_portion)
                            <div class="text-xs font-semibold text-gray-600 font-mono">
                                ₹ {{ number_format($dish->half_price, 2) }} <span class="text-[10px] text-gray-400 font-sans font-normal">(Half)</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center space-x-2 text-xs">
                        <!-- Edit Button -->
                        <button 
                            @click="currentDish = {{ $dish->toJson() }}; editModalOpen = true;"
                            class="px-2.5 py-1 border border-gray-200 rounded text-gray-600 hover:bg-gray-50 font-medium transition-colors"
                        >
                            Edit
                        </button>

                        <!-- Delete Form -->
                        <form action="{{ route('restaurant.menu.delete', $dish->id) }}" method="POST" onsubmit="return confirm('Delete this dish?')">
                            @csrf
                            <button type="submit" class="p-1 text-gray-400 hover:text-rose-600" title="Delete">
                                &times;
                            </button>
                        </form>
                    </div>
                </div>

            </div>
            @empty
            <div class="col-span-full py-12 text-center text-gray-400 text-xs bg-white rounded-lg border border-gray-200">
                No food dishes found for this category. Click "+ Add Dish" above to add one.
            </div>
            @endforelse
        </div>

        <!-- Add Dish Modal -->
        <div x-show="addModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.outside="addModalOpen = false" class="bg-white rounded-xl shadow-xl max-w-md w-full p-5 sm:p-6 border border-gray-200 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <h3 class="font-bold text-gray-900 text-base">Add New Food Item</h3>
                    <button @click="addModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg p-1">&times;</button>
                </div>

                <form action="{{ route('restaurant.menu.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Dish Name *</label>
                        <input type="text" name="name" required class="w-full bg-white border border-gray-300 rounded-lg py-2 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500" placeholder="e.g. Paneer Tikka Masala"/>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Category *</label>
                        <select name="category_id" required class="w-full bg-white border border-gray-300 rounded-lg py-2 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500">
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3" x-data="{ hasHalf: false }">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Full Price (₹) *</label>
                            <input type="number" step="0.01" name="price" required class="w-full bg-white border border-gray-300 rounded-lg py-2 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500" placeholder="180.00"/>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Prep Time</label>
                            <input type="text" name="prep_time" value="15 mins" class="w-full bg-white border border-gray-300 rounded-lg py-2 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500"/>
                        </div>
                        <div class="col-span-2 bg-orange-50/70 p-2.5 rounded-lg border border-orange-200/80">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="has_half_portion" value="1" x-model="hasHalf" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 mr-2"/>
                                <span class="font-bold text-gray-800">Has Half Plate / Portion?</span>
                            </label>
                            <div x-show="hasHalf" x-cloak class="mt-2">
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Half Plate Price (₹) *</label>
                                <input type="number" step="0.01" name="half_price" class="w-full bg-white border border-gray-300 rounded-lg py-1.5 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500" placeholder="e.g. 100.00"/>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full bg-white border border-gray-300 rounded-lg py-2 px-3 text-gray-800">
                                <option value="Available">Available</option>
                                <option value="Low Stock">Low Stock</option>
                                <option value="Out of Stock">Out of Stock</option>
                            </select>
                        </div>
                        <div class="flex items-center pt-5">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_veg" value="1" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 mr-2"/>
                                <span class="font-medium text-gray-700">Pure Vegetarian</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full bg-white border border-gray-300 rounded-lg py-2 px-3 text-gray-800" placeholder="Ingredients and flavor notes..."></textarea>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex items-center justify-end space-x-2">
                        <button type="button" @click="addModalOpen = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-[#ef7d3b] hover:bg-[#d94e08] text-white font-semibold rounded-lg shadow-xs">Save Dish</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Dish Modal -->
        <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.outside="editModalOpen = false" class="bg-white rounded-xl shadow-xl max-w-md w-full p-5 sm:p-6 border border-gray-200 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <h3 class="font-bold text-gray-900 text-base">Edit Food Item</h3>
                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg p-1">&times;</button>
                </div>

                <form :action="'{{ url('menu') }}/' + currentDish.id + '/update'" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Dish Name *</label>
                        <input type="text" name="name" x-model="currentDish.name" required class="w-full bg-white border border-gray-300 rounded py-2 px-3 text-gray-800"/>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Category *</label>
                        <select name="category_id" x-model="currentDish.category_id" required class="w-full bg-white border border-gray-300 rounded py-2 px-3 text-gray-800">
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Full Price (₹) *</label>
                            <input type="number" step="0.01" name="price" x-model="currentDish.price" required class="w-full bg-white border border-gray-300 rounded py-2 px-3 text-gray-800"/>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Prep Time</label>
                            <input type="text" name="prep_time" x-model="currentDish.prep_time" class="w-full bg-white border border-gray-300 rounded py-2 px-3 text-gray-800"/>
                        </div>
                        <div class="col-span-2 bg-orange-50/70 p-2.5 rounded border border-orange-200/80">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="has_half_portion" value="1" x-model="currentDish.has_half_portion" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 mr-2"/>
                                <span class="font-bold text-gray-800">Has Half Plate / Portion?</span>
                            </label>
                            <div x-show="currentDish.has_half_portion" class="mt-2">
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Half Plate Price (₹) *</label>
                                <input type="number" step="0.01" name="half_price" x-model="currentDish.half_price" class="w-full bg-white border border-gray-300 rounded py-1.5 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500" placeholder="e.g. 100.00"/>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Status</label>
                            <select name="status" x-model="currentDish.status" class="w-full bg-white border border-gray-300 rounded py-2 px-3 text-gray-800">
                                <option value="Available">Available</option>
                                <option value="Low Stock">Low Stock</option>
                                <option value="Out of Stock">Out of Stock</option>
                            </select>
                        </div>
                        <div class="flex items-center pt-5">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_veg" value="1" :checked="currentDish.is_veg" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 mr-2"/>
                                <span class="font-medium text-gray-700">Pure Vegetarian</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Description</label>
                        <textarea name="description" x-model="currentDish.description" rows="2" class="w-full bg-white border border-gray-300 rounded py-2 px-3 text-gray-800"></textarea>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex items-center justify-end space-x-2">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 border border-gray-300 rounded text-gray-600 hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-[#ef7d3b] hover:bg-[#d94e08] text-white font-semibold rounded shadow-xs">Update Dish</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
