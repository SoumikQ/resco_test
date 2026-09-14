<div class="lg:shrink-0">
    <!-- Backdrop for mobile / tablet drawer -->
    <div 
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs z-40 lg:hidden transition-opacity duration-300" 
        :class="sidebarOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'" 
        aria-hidden="true" 
        x-cloak
        @click="sidebarOpen = false"
    ></div>

    <!-- Sidebar component -->
    <aside
        id="sidebar"
        class="flex flex-col fixed z-50 left-0 top-0 lg:static lg:left-auto lg:top-auto h-[100dvh] overflow-y-auto no-scrollbar w-32 sm:w-28 lg:w-28 shrink-0 bg-white border-r border-gray-200/90 p-0 shadow-2xl lg:shadow-none transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0"
        :class="sidebarOpen ? '!translate-x-0' : '-translate-x-full lg:!translate-x-0'"
        @keydown.escape.window="sidebarOpen = false"
    >
        <!-- Brand Logo Block: Warm Orange with Cutlery & Mobile Close Button -->
        <div class="relative flex items-center justify-between w-full h-16 bg-gradient-to-r from-orange-600 to-[#ef7d3b] shrink-0 shadow-xs px-3">
            <a href="{{ route('restaurant.dashboard') }}" class="flex items-center justify-center flex-1 h-full" title="Restaurants POS" @click="sidebarOpen = false">
                <!-- Cutlery (Fork & Knife) Icon -->
                <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <!-- Fork -->
                    <path d="M18 2v6a3 3 0 0 1-3 3 3 3 0 0 1-3-3V2" />
                    <path d="M15 2v10" />
                    <path d="M15 12v10" />
                    <!-- Knife -->
                    <path d="M7 2v20" />
                    <path d="M7 2a5 5 0 0 1 5 5v5H7" />
                </svg>
            </a>

            <!-- Mobile Close Button with Left Arrow Icon (Visible only on <lg screens) -->
            <button 
                type="button"
                @click="sidebarOpen = false" 
                class="lg:hidden absolute right-2 text-white/90 hover:text-white p-1.5 rounded-lg bg-black/10 hover:bg-black/20 active:scale-95 transition-all focus:outline-none cursor-pointer flex items-center justify-center"
                title="Collapse Sidebar Menu"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 flex flex-col py-2 divide-y divide-gray-100/70 overflow-y-auto">
            
            <!-- 1. Dashboard -->
            <a 
                href="{{ route('restaurant.dashboard') }}" 
                @click="sidebarOpen = false"
                class="flex flex-col items-center justify-center py-3 sm:py-3.5 px-2 transition-all group text-center {{ request()->routeIs('restaurant.dashboard') ? 'border-l-4 border-[#ea580c] bg-orange-50/70 text-[#ea580c] font-semibold' : 'border-l-4 border-transparent text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}"
            >
                <!-- Speedometer Icon -->
                <svg class="w-6 h-6 mb-1.5 transition-colors {{ request()->routeIs('restaurant.dashboard') ? 'text-[#ea580c]' : 'text-gray-400 group-hover:text-gray-700' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m12 14 3-3" />
                    <path d="M3.34 19a10 10 0 1 1 17.32 0" />
                </svg>
                <span class="text-[11px] leading-tight tracking-tight">Dashboard</span>
            </a>

            <!-- 2. Orders -->
            <a 
                href="{{ route('restaurant.orders') }}" 
                @click="sidebarOpen = false"
                class="flex flex-col items-center justify-center py-3 sm:py-3.5 px-2 transition-all group text-center {{ request()->routeIs('restaurant.orders*') ? 'border-l-4 border-[#ea580c] bg-orange-50/70 text-[#ea580c] font-semibold' : 'border-l-4 border-transparent text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}"
            >
                <!-- Cloche / Food Cover Icon -->
                <svg class="w-6 h-6 mb-1.5 transition-colors {{ request()->routeIs('restaurant.orders*') ? 'text-[#ea580c]' : 'text-gray-400 group-hover:text-gray-700' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 4a1 1 0 0 1 1-1h0a1 1 0 0 1 1 1v1h-2V4Z" />
                    <path d="M4 18h16a1 1 0 0 0 1-1c0-4.418-4.03-8-9-8s-9 3.582-9 8a1 1 0 0 0 1 1Z" />
                    <path d="M2 20h20" />
                </svg>
                <span class="text-[11px] leading-tight tracking-tight">Orders</span>
            </a>

            <!-- 3. Food/Menu -->
            <a 
                href="{{ route('restaurant.menu') }}" 
                @click="sidebarOpen = false"
                class="flex flex-col items-center justify-center py-3 sm:py-3.5 px-2 transition-all group text-center {{ request()->routeIs('restaurant.menu*') ? 'border-l-4 border-[#ea580c] bg-orange-50/70 text-[#ea580c] font-semibold' : 'border-l-4 border-transparent text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}"
            >
                <!-- Menu / Cutlery in Book Icon -->
                <svg class="w-6 h-6 mb-1.5 transition-colors {{ request()->routeIs('restaurant.menu*') ? 'text-[#ea580c]' : 'text-gray-400 group-hover:text-gray-700' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z" />
                    <path d="M6 6h10" />
                    <path d="M6 10h10" />
                    <path d="M6 14h6" />
                </svg>
                <span class="text-[11px] leading-tight tracking-tight">Food/Menu</span>
            </a>

            <!-- 4. Category -->
            <a 
                href="{{ route('restaurant.categories') }}" 
                @click="sidebarOpen = false"
                class="flex flex-col items-center justify-center py-3 sm:py-3.5 px-2 transition-all group text-center {{ request()->routeIs('restaurant.categories*') ? 'border-l-4 border-[#ea580c] bg-orange-50/70 text-[#ea580c] font-semibold' : 'border-l-4 border-transparent text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}"
            >
                <!-- Grid / Categories Icon -->
                <svg class="w-6 h-6 mb-1.5 transition-colors {{ request()->routeIs('restaurant.categories*') ? 'text-[#ea580c]' : 'text-gray-400 group-hover:text-gray-700' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1.5" />
                    <rect x="14" y="3" width="7" height="7" rx="1.5" />
                    <rect x="14" y="14" width="7" height="7" rx="1.5" />
                    <rect x="3" y="14" width="7" height="7" rx="1.5" />
                </svg>
                <span class="text-[11px] leading-tight tracking-tight">Category</span>
            </a>

            <!-- 5. Customer Satisfaction -->
            <a 
                href="{{ route('restaurant.customer-satisfaction') }}" 
                @click="sidebarOpen = false"
                class="flex flex-col items-center justify-center py-3 sm:py-3.5 px-2 transition-all group text-center {{ request()->routeIs('restaurant.customer-satisfaction*') ? 'border-l-4 border-[#ea580c] bg-orange-50/70 text-[#ea580c] font-semibold' : 'border-l-4 border-transparent text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}"
            >
                <!-- Customer Satisfaction / Smile Feedback Icon -->
                <svg class="w-6 h-6 mb-1.5 transition-colors {{ request()->routeIs('restaurant.customer-satisfaction*') ? 'text-[#ea580c]' : 'text-gray-400 group-hover:text-gray-700' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M8 14s1.5 2 4 2 4-2 4-2" />
                    <line x1="9" x2="9.01" y1="9" y2="9" stroke-width="2.5" />
                    <line x1="15" x2="15.01" y1="9" y2="9" stroke-width="2.5" />
                </svg>
                <span class="text-[11px] leading-tight tracking-tight">Customer<br/>Satisfaction</span>
            </a>

            {{-- 6. Billing (Hidden as requested. Can be re-enabled anytime)
            <a 
                href="{{ route('restaurant.billing') }}" 
                @click="sidebarOpen = false"
                class="flex flex-col items-center justify-center py-3 sm:py-3.5 px-2 transition-all group text-center {{ request()->routeIs('restaurant.billing*') ? 'border-l-4 border-[#ea580c] bg-orange-50/70 text-[#ea580c] font-semibold' : 'border-l-4 border-transparent text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}"
            >
                <svg class="w-6 h-6 mb-1.5 transition-colors {{ request()->routeIs('restaurant.billing*') ? 'text-[#ea580c]' : 'text-gray-400 group-hover:text-gray-700' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z" />
                    <path d="M16 8h-6" />
                    <path d="M16 12h-6" />
                    <path d="M16 16h-4" />
                </svg>
                <span class="text-[11px] leading-tight tracking-tight">Billing</span>
            </a>
            --}}

            <!-- 7. Basic Reports -->
            <a 
                href="{{ route('restaurant.reports') }}" 
                @click="sidebarOpen = false"
                class="flex flex-col items-center justify-center py-3 sm:py-3.5 px-2 transition-all group text-center {{ request()->routeIs('restaurant.reports*') ? 'border-l-4 border-[#ea580c] bg-orange-50/70 text-[#ea580c] font-semibold' : 'border-l-4 border-transparent text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}"
            >
                <!-- Reports / Analytics Icon -->
                <svg class="w-6 h-6 mb-1.5 transition-colors {{ request()->routeIs('restaurant.reports*') ? 'text-[#ea580c]' : 'text-gray-400 group-hover:text-gray-700' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 3v18h18" />
                    <path d="m19 9-5 5-4-4-3 3" />
                </svg>
                <span class="text-[11px] leading-tight tracking-tight">Basic Reports</span>
            </a>

            <!-- 8. Settings -->
            <a 
                href="{{ route('restaurant.settings') }}" 
                @click="sidebarOpen = false"
                class="flex flex-col items-center justify-center py-3 sm:py-3.5 px-2 transition-all group text-center {{ request()->routeIs('restaurant.settings*') ? 'border-l-4 border-[#ea580c] bg-orange-50/70 text-[#ea580c] font-semibold' : 'border-l-4 border-transparent text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}"
            >
                <!-- Gear / Settings Icon -->
                <svg class="w-6 h-6 mb-1.5 transition-colors {{ request()->routeIs('restaurant.settings*') ? 'text-[#ea580c]' : 'text-gray-400 group-hover:text-gray-700' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                <span class="text-[11px] leading-tight tracking-tight">Settings</span>
            </a>

        </nav>
    </aside>
</div>