<x-app-layout>
    @section('page_title', 'Settings')

    <div class="space-y-6">

        <!-- Flash Success Notification -->
        @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-semibold flex items-center justify-between shadow-2xs">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">✕</button>
        </div>
        @endif

        <!-- Flash Error Notification -->
        @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-xs font-semibold flex items-center justify-between shadow-2xs">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700 cursor-pointer">✕</button>
        </div>
        @endif

        <!-- Header -->
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">Restaurant Settings</h1>
            <p class="text-xs text-gray-500 mt-0.5">Configure restaurant profile, 58mm thermal printer direct printing, and software wallpaper.</p>
        </div>

        <!-- Settings Form Card -->
        <div class="bg-white rounded-lg border border-gray-200/90 shadow-2xs p-6 sm:p-8 max-w-4xl space-y-6">
            <form action="{{ route('restaurant.settings.update') }}" method="POST" class="space-y-6">
                @csrf

                <!-- 1. Restaurant Profile -->
                <div>
                    <h3 class="text-sm font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center space-x-2">
                        <span>Restaurant &amp; Bill Information</span>
                        <span class="text-[11px] text-gray-400 font-normal">(Prints on final thermal invoice)</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Restaurant Name <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                name="restaurant_name"
                                value="{{ $config['restaurant_name'] ?? 'The Grand Royal Restaurant' }}"
                                required
                                class="w-full text-xs bg-white border border-gray-300 rounded-md py-2 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500 shadow-2xs"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tagline / Subtitle</label>
                            <input 
                                type="text" 
                                name="tagline"
                                value="{{ $config['tagline'] ?? '' }}"
                                placeholder="e.g. Finest Culinary & Dining Experience"
                                class="w-full text-xs bg-white border border-gray-300 rounded-md py-2 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500 shadow-2xs"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Contact Phone</label>
                            <input 
                                type="text" 
                                name="phone"
                                value="{{ $config['phone'] ?? '' }}"
                                placeholder="e.g. +91 98765 43210"
                                class="w-full text-xs bg-white border border-gray-300 rounded-md py-2 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500 shadow-2xs"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Official Email</label>
                            <input 
                                type="email" 
                                name="email"
                                value="{{ $config['email'] ?? '' }}"
                                placeholder="e.g. info@grandroyal.in"
                                class="w-full text-xs bg-white border border-gray-300 rounded-md py-2 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500 shadow-2xs"
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Physical Address</label>
                            <input 
                                type="text" 
                                name="address"
                                value="{{ $config['address'] ?? '' }}"
                                placeholder="e.g. 12 Park Street, Kolkata, West Bengal - 700016"
                                class="w-full text-xs bg-white border border-gray-300 rounded-md py-2 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500 shadow-2xs"
                            />
                        </div>
                    </div>
                </div>

                <!-- 2. Currency & Thermal Printer Direct Print -->
                <div>
                    <h3 class="text-sm font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span>Thermal Printer &amp; Direct Silent Printing</span>
                            <span class="text-[11px] text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Zero Browser Dialog</span>
                        </div>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Currency Symbol</label>
                            <input 
                                type="text" 
                                name="currency"
                                value="{{ $config['currency'] ?? '₹' }}"
                                class="w-full text-xs font-mono font-bold bg-white border border-gray-300 rounded-md py-2 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500 shadow-2xs"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">GSTIN / Tax ID (Optional)</label>
                            <input 
                                type="text" 
                                name="gstin"
                                value="{{ $config['gstin'] ?? '' }}"
                                placeholder="e.g. 19ABCDE1234F1Z5"
                                class="w-full text-xs font-mono uppercase bg-white border border-gray-300 rounded-md py-2 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500 shadow-2xs"
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Invoice Footer Note</label>
                            <input 
                                type="text" 
                                name="invoice_footer"
                                value="{{ $config['invoice_footer'] ?? 'Thank you for dining with us! Please visit again.' }}"
                                placeholder="Thank you message printed at the bottom of the bill"
                                class="w-full text-xs bg-white border border-gray-300 rounded-md py-2 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500 shadow-2xs"
                            />
                        </div>

                        <!-- Direct Printing Box -->
                        <div class="sm:col-span-2 bg-gradient-to-r from-orange-50/80 via-amber-50/50 to-orange-50/80 p-4 rounded-lg border border-orange-200/80 space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="pt-0.5">
                                    <input 
                                        type="checkbox" 
                                        id="direct_print_enabled" 
                                        name="direct_print_enabled" 
                                        value="1" 
                                        {{ ($config['direct_print_enabled'] ?? '1') !== '0' ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500 cursor-pointer"
                                    />
                                </div>
                                <div class="flex-1">
                                    <label for="direct_print_enabled" class="text-xs font-bold text-gray-900 cursor-pointer flex items-center space-x-2">
                                        <span>Enable 1-Click Direct Silent Printing (No Window / No Browser Dialog)</span>
                                    </label>
                                    <p class="text-[11px] text-gray-600 mt-0.5">
                                        When enabled, clicking <strong>"Print Bill"</strong> or placing an order immediately sends the receipt directly to your connected thermal printer in the background without opening any print window.
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-orange-200/60">
                                <div>
                                    <label class="block text-xs font-bold text-gray-800 mb-1">Printer Paper Roll Size</label>
                                    <select 
                                        name="printer_paper_size"
                                        class="w-full text-xs bg-white border border-gray-300 rounded-md py-2 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500 shadow-2xs font-semibold"
                                    >
                                        <option value="58mm" {{ ($config['printer_paper_size'] ?? '58mm') === '58mm' ? 'selected' : '' }}>
                                            🧾 58mm (2-Inch Thermal Roll - 32 columns)
                                        </option>
                                        <option value="80mm" {{ ($config['printer_paper_size'] ?? '') === '80mm' ? 'selected' : '' }}>
                                            🧾 80mm (3-Inch Standard Roll - 48 columns)
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-800 mb-1">Target Thermal Printer Device</label>
                                    <select 
                                        name="direct_printer_name"
                                        class="w-full text-xs bg-white border border-gray-300 rounded-md py-2 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500 shadow-2xs font-semibold"
                                    >
                                        <option value="" {{ empty($config['direct_printer_name']) ? 'selected' : '' }}>
                                            🌟 Default Windows Printer ({{ $defaultPrinter ?? 'Auto Detect' }})
                                        </option>
                                        @foreach($installedPrinters ?? [] as $printer)
                                        <option value="{{ $printer }}" {{ ($config['direct_printer_name'] ?? '') === $printer ? 'selected' : '' }}>
                                            🖨️ {{ $printer }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <p class="text-[10px] text-gray-500 mt-1">Select your 58mm POS printer or keep default.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Restaurant Theme Wallpaper (Food Doodles Background) -->
                <div>
                    <h3 class="text-sm font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span>Software Background Wallpaper</span>
                            <span class="text-[11px] text-gray-400 font-normal">(Food &amp; Restaurant Doodle Pattern)</span>
                        </div>
                        <span class="text-[11px] text-orange-600 font-semibold bg-orange-50 px-2 py-0.5 rounded">Theme</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Pattern Style Selector -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Wallpaper Style</label>
                            <select 
                                name="bg_pattern"
                                class="w-full text-xs bg-white border border-gray-300 rounded-md py-2 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500 shadow-2xs"
                            >
                                <option value="red" {{ ($config['bg_pattern'] ?? 'red') === 'red' ? 'selected' : '' }}>
                                    🔴 Red Food Doodles (User Image Style)
                                </option>
                                <option value="orange" {{ ($config['bg_pattern'] ?? '') === 'orange' ? 'selected' : '' }}>
                                    🟠 Warm Brand Orange Doodles
                                </option>
                                <option value="slate" {{ ($config['bg_pattern'] ?? '') === 'slate' ? 'selected' : '' }}>
                                    ⚫ Neutral Slate Charcoal Doodles
                                </option>
                                <option value="none" {{ ($config['bg_pattern'] ?? '') === 'none' ? 'selected' : '' }}>
                                    ⚪ Off / Clean Plain Background
                                </option>
                            </select>
                        </div>

                        <!-- Opacity Selector -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Wallpaper Opacity (Subtlety)</label>
                            <select 
                                name="bg_pattern_opacity"
                                class="w-full text-xs bg-white border border-gray-300 rounded-md py-2 px-3 text-gray-800 focus:ring-1 focus:ring-orange-500 shadow-2xs"
                            >
                                <option value="0.04" {{ ($config['bg_pattern_opacity'] ?? '0.06') == '0.04' ? 'selected' : '' }}>
                                    Very Soft &amp; Minimal (4% Opacity)
                                </option>
                                <option value="0.06" {{ ($config['bg_pattern_opacity'] ?? '0.06') == '0.06' ? 'selected' : '' }}>
                                    Balanced &amp; Elegant (6% Opacity - Recommended)
                                </option>
                                <option value="0.09" {{ ($config['bg_pattern_opacity'] ?? '0.06') == '0.09' ? 'selected' : '' }}>
                                    Vivid &amp; Distinct (9% Opacity)
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                    <button 
                        type="submit" 
                        class="w-full sm:w-auto bg-[#ef7d3b] hover:bg-[#d94e08] text-white text-xs font-bold px-6 py-2.5 rounded-lg shadow-sm hover:shadow transition-all cursor-pointer flex items-center justify-center space-x-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Save Settings</span>
                    </button>
                </div>

            </form>

            <!-- Test Print Box -->
            <div class="pt-4 border-t border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-gray-50/80 p-4 rounded-lg">
                <div>
                    <div class="text-xs font-bold text-gray-800">Test Your 58mm Thermal Printer</div>
                    <div class="text-[11px] text-gray-500">Sends a sample test slip directly to the connected printer to verify direct printing.</div>
                </div>
                <form action="{{ route('restaurant.settings.test-print') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="printer_name" value="{{ $config['direct_printer_name'] ?? '' }}" />
                    <input type="hidden" name="paper_size" value="{{ $config['printer_paper_size'] ?? '58mm' }}" />
                    <button 
                        type="submit"
                        class="px-4 py-2 bg-gray-800 hover:bg-black text-white text-xs font-semibold rounded-lg shadow-sm hover:shadow transition-all cursor-pointer flex items-center space-x-2"
                    >
                        <span>🖨️ Send Test Slip to Printer</span>
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
