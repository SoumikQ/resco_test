<header class="sticky top-0 z-40 bg-white border-b border-gray-200/90 shadow-2xs">
    <div class="h-16 px-3.5 sm:px-6 lg:px-8 flex items-center justify-between">
        
        <!-- Left: Mobile Toggle & Page Title -->
        <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
            <!-- Mobile Sidebar Toggle -->
            <button 
                class="lg:hidden text-gray-500 hover:text-gray-800 focus:outline-none p-2 -ml-1.5 rounded-lg hover:bg-gray-100 cursor-pointer"
                @click.stop="sidebarOpen = !sidebarOpen"
                title="Toggle Sidebar Menu"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Section Title -->
            <h1 class="text-base sm:text-xl font-bold text-gray-900 tracking-tight truncate">
                @yield('page_title', 'Restaurants')
            </h1>
        </div>

        <!-- Right: Notifications & User Profile -->
        <div class="flex items-center space-x-2.5 sm:space-x-5 shrink-0">
            
            <!-- Notification Bell -->
            <button class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors rounded-full hover:bg-gray-100 cursor-pointer" title="Notifications">
                <span class="sr-only">Notifications</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <!-- Notification Dot -->
                <span class="absolute top-2 right-2 w-2 h-2 bg-orange-500 rounded-full ring-2 ring-white"></span>
            </button>

            <!-- User Menu Dropdown (Alpine.js) -->
            <div class="relative" x-data="{ userMenuOpen: false }">
                <!-- Trigger Button -->
                <button 
                    @click="userMenuOpen = !userMenuOpen" 
                    type="button"
                    class="flex items-center space-x-2 p-1 rounded-xl hover:bg-gray-100/80 transition-colors focus:outline-none cursor-pointer group"
                    :class="{ 'bg-gray-100/80': userMenuOpen }"
                >
                    <!-- User Avatar / Initial -->
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full overflow-hidden ring-2 ring-orange-500/20 bg-gradient-to-tr from-orange-500 to-amber-500 flex items-center justify-center text-white font-bold text-xs shadow-xs">
                        @if(Auth::check() && Auth::user()->profile_photo_path)
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover"/>
                        @else
                            <span>{{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'M' }}</span>
                        @endif
                    </div>

                    <!-- User Name & Role (Hidden on mobile) -->
                    <div class="hidden sm:flex flex-col text-left">
                        <span class="text-xs font-bold text-gray-800 group-hover:text-orange-600 transition-colors leading-tight">
                            {{ Auth::check() ? Auth::user()->name : 'Manager (Admin)' }}
                        </span>
                        <span class="text-[10px] text-gray-500 leading-tight">
                            {{ Auth::check() ? 'Shift Active' : 'Staff / Owner' }}
                        </span>
                    </div>

                    <!-- Chevron Icon -->
                    <svg 
                        class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-transform duration-200"
                        :class="{ 'rotate-180 text-orange-500': userMenuOpen }"
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div 
                    x-show="userMenuOpen"
                    @click.outside="userMenuOpen = false"
                    @keydown.escape.window="userMenuOpen = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                    class="absolute right-0 mt-2 w-64 max-w-[calc(100vw-1.5rem)] bg-white rounded-2xl shadow-2xl border border-gray-200/90 py-2 z-50 divide-y divide-gray-100"
                    style="display: none;"
                >
                    <!-- User Header in Dropdown -->
                    <div class="px-4 py-3 bg-gray-50/60 rounded-t-2xl">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-orange-500 to-amber-500 flex items-center justify-center text-white font-black text-sm shadow-xs">
                                <span>{{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'M' }}</span>
                            </div>
                            <div class="truncate flex-1">
                                <div class="text-xs font-bold text-gray-900 truncate">
                                    {{ Auth::check() ? Auth::user()->name : 'Restaurant Manager' }}
                                </div>
                                <div class="text-[11px] text-gray-500 truncate">
                                    {{ Auth::check() ? Auth::user()->email : 'admin@resco.com' }}
                                </div>
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 mt-1">
                                    <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                    Administrator
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Links -->
                    <div class="py-1.5 text-xs text-gray-700">
                        <a 
                            href="{{ route('restaurant.settings') }}" 
                            class="flex items-center gap-2.5 px-4 py-2 hover:bg-orange-50 hover:text-orange-600 transition-colors font-medium"
                            @click="userMenuOpen = false"
                        >
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                            </svg>
                            <span>Restaurant Settings</span>
                        </a>

                        <a 
                            href="{{ route('restaurant.reports') }}" 
                            class="flex items-center gap-2.5 px-4 py-2 hover:bg-orange-50 hover:text-orange-600 transition-colors font-medium"
                            @click="userMenuOpen = false"
                        >
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <line x1="18" y1="20" x2="18" y2="10"></line>
                                <line x1="12" y1="20" x2="12" y2="4"></line>
                                <line x1="6" y1="20" x2="6" y2="14"></line>
                            </svg>
                            <span>Daily Sales Report</span>
                        </a>

                        <a 
                            href="{{ route('restaurant.customer-satisfaction') }}" 
                            class="flex items-center gap-2.5 px-4 py-2 hover:bg-orange-50 hover:text-orange-600 transition-colors font-medium"
                            @click="userMenuOpen = false"
                        >
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            <span>Review QR Standee</span>
                        </a>
                    </div>

                    <!-- Log Out Action (Always Visible) -->
                    <div class="py-1 text-xs">
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full flex items-center gap-2.5 px-4 py-2.5 text-rose-600 hover:bg-rose-50 hover:text-rose-700 font-semibold transition-colors text-left cursor-pointer group"
                            >
                                <svg class="w-4 h-4 text-rose-500 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span>Log Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>
</header>