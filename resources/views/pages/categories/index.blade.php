<x-app-layout>
    @section('page_title', 'Category')

    <div class="space-y-6" x-data="{ addModalOpen: false, editModalOpen: false, currentCategory: {} }">

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

        <!-- Top Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">Food Categories</h1>
                <p class="text-xs text-gray-500 mt-0.5">Manage dining menu categories and organize dishes.</p>
            </div>
            
            <button 
                @click="addModalOpen = true"
                class="inline-flex items-center justify-center bg-[#ef7d3b] hover:bg-[#d94e08] text-white text-xs font-semibold px-4 py-2.5 rounded-md shadow-2xs transition-colors cursor-pointer"
            >
                <span class="text-base mr-1.5 leading-none">+</span> Add Category
            </button>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
            @forelse($categories as $cat)
            <div class="bg-white rounded-xl border border-gray-200/90 shadow-2xs p-4 sm:p-5 hover:shadow-xs transition-shadow flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center text-orange-600 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="7" height="7" rx="1.5" stroke-width="2"/>
                                    <rect x="14" y="3" width="7" height="7" rx="1.5" stroke-width="2"/>
                                    <rect x="14" y="14" width="7" height="7" rx="1.5" stroke-width="2"/>
                                    <rect x="3" y="14" width="7" height="7" rx="1.5" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="truncate">
                                <h3 class="font-bold text-gray-900 text-sm sm:text-base truncate">
                                    {{ $cat->name }}
                                </h3>
                                <p class="text-xs text-orange-600 font-semibold mt-0.5">
                                    {{ $cat->food_items_count }} Items
                                </p>
                            </div>
                        </div>

                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] sm:text-[11px] font-semibold shrink-0 {{ $cat->status === 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $cat->status }}
                        </span>
                    </div>

                    <p class="text-xs text-gray-500 my-3.5 line-clamp-2">
                        {{ $cat->description ?: 'Categorized items for ordering and kitchen preparation.' }}
                    </p>
                </div>

                <!-- Action Footer -->
                <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                    <a href="{{ route('restaurant.menu', ['category' => $cat->slug]) }}" class="text-orange-600 hover:text-orange-700 font-semibold">
                        Dishes &rarr;
                    </a>

                    <div class="flex items-center space-x-2">
                        <button 
                            @click="currentCategory = {{ $cat->toJson() }}; editModalOpen = true;"
                            class="px-2.5 py-1 border border-gray-200 rounded-md text-gray-600 hover:bg-gray-50 font-medium"
                        >
                            Edit
                        </button>

                        <form action="{{ route('restaurant.categories.delete', $cat->id) }}" method="POST" onsubmit="return confirm('Delete this category? Associated food items will also be removed.')">
                            @csrf
                            <button type="submit" class="p-1 text-gray-400 hover:text-rose-600 font-bold" title="Delete">
                                &times;
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center text-gray-400 text-xs bg-white rounded-xl border border-gray-200">
                No categories found. Click "+ Add Category" to create your first category.
            </div>
            @endforelse
        </div>

        <!-- Add Category Modal -->
        <div x-show="addModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.outside="addModalOpen = false" class="bg-white rounded-xl shadow-xl max-w-md w-full p-5 sm:p-6 border border-gray-200 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <h3 class="font-bold text-gray-900 text-base">Add New Category</h3>
                    <button @click="addModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg p-1">&times;</button>
                </div>

                <form action="{{ route('restaurant.categories.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Category Name *</label>
                        <input type="text" name="name" required class="w-full bg-white border border-gray-300 rounded-lg py-2 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500" placeholder="e.g. Tandoori Specials"/>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full bg-white border border-gray-300 rounded-lg py-2 px-3 text-gray-800">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full bg-white border border-gray-300 rounded-lg py-2 px-3 text-gray-800" placeholder="Short description of this category..."></textarea>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex items-center justify-end space-x-2">
                        <button type="button" @click="addModalOpen = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-[#ef7d3b] hover:bg-[#d94e08] text-white font-semibold rounded-lg shadow-xs">Save Category</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Category Modal -->
        <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.outside="editModalOpen = false" class="bg-white rounded-xl shadow-xl max-w-md w-full p-5 sm:p-6 border border-gray-200 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <h3 class="font-bold text-gray-900 text-base">Edit Category</h3>
                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg p-1">&times;</button>
                </div>

                <form :action="'{{ url('categories') }}/' + currentCategory.id + '/update'" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Category Name *</label>
                        <input type="text" name="name" x-model="currentCategory.name" required class="w-full bg-white border border-gray-300 rounded-lg py-2 px-3 text-gray-800"/>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Status</label>
                        <select name="status" x-model="currentCategory.status" class="w-full bg-white border border-gray-300 rounded-lg py-2 px-3 text-gray-800">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Description</label>
                        <textarea name="description" x-model="currentCategory.description" rows="2" class="w-full bg-white border border-gray-300 rounded-lg py-2 px-3 text-gray-800"></textarea>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex items-center justify-end space-x-2">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-[#ef7d3b] hover:bg-[#d94e08] text-white font-semibold rounded-lg shadow-xs">Update Category</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
