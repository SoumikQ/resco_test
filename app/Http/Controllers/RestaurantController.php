<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\FoodItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RestaurantSetting;
use App\Models\Review;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\EyeFill;
use BaconQrCode\Renderer\RendererStyle\Gradient;
use BaconQrCode\Renderer\RendererStyle\GradientType;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Module\DotsModule;
use BaconQrCode\Renderer\Module\RoundnessModule;
use BaconQrCode\Renderer\Eye\SimpleCircleEye;
use BaconQrCode\Renderer\Eye\PointyEye;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Writer;
use App\Services\ThermalPrintService;

class RestaurantController extends Controller
{
    /**
     * Display the Restaurant Dashboard (Fully Dynamic POS & Kitchen Management System)
     */
    public function dashboard()
    {
        $todayStart = Carbon::today();
        
        // Revenue metrics
        $todayRevenue = (float) Order::whereIn('status', ['Complete', 'Paid'])
            ->where('created_at', '>=', $todayStart)
            ->sum('total_amount');
            
        $totalRevenueAll = (float) Order::whereIn('status', ['Complete', 'Paid'])->sum('total_amount');

        // Order counts
        $todayOrdersCount = Order::where('created_at', '>=', $todayStart)->count();
        $totalOrdersCount = Order::count();
        $unpaidOrdersCount = Order::where('status', 'Unpaid')->count();
        $completedOrdersCount = Order::whereIn('status', ['Complete', 'Paid'])->count();

        // Cash vs Online Breakdown
        $totalCash = (float) Order::whereIn('status', ['Complete', 'Paid'])->sum('cash_amount');
        $totalOnline = (float) Order::whereIn('status', ['Complete', 'Paid'])->sum('online_amount');
        $totalCollected = $totalCash + $totalOnline;
        $cashPercent = $totalCollected > 0 ? round(($totalCash / $totalCollected) * 100) : 50;
        $onlinePercent = $totalCollected > 0 ? round(($totalOnline / $totalCollected) * 100) : 50;

        // Average Order Value
        $avgOrderValue = $completedOrdersCount > 0 ? ($totalRevenueAll / $completedOrdersCount) : 0;

        // Inventory & Menu stats
        $lowStockItemsCount = FoodItem::where('status', 'Low Stock')->count();
        $totalMenuItemsCount = FoodItem::count();

        // Customer Satisfaction stats
        $csatAvg = Review::count() > 0 ? number_format(Review::avg('rating'), 1) : '5.0';
        $csatTotal = Review::count();

        // Recent Orders with items relationship
        $recentOrders = Order::with('items')->latest()->take(6)->get();

        // Top Selling Dishes (dynamic ranking from order_items)
        $topDishes = OrderItem::selectRaw('item_name, category_name, SUM(quantity) as total_qty, SUM(total_price) as total_rev')
            ->groupBy('item_name', 'category_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();
        $maxDishQty = $topDishes->max('total_qty') ?: 1;

        // Last 7 Days Revenue Trend for dynamic bar chart
        $chartData = [];
        $maxChartRev = 100;
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $dayRev = (float) Order::whereIn('status', ['Complete', 'Paid'])
                ->whereDate('created_at', $day->toDateString())
                ->sum('total_amount');
            $dayOrders = Order::whereDate('created_at', $day->toDateString())->count();
            if ($dayRev > $maxChartRev) {
                $maxChartRev = $dayRev;
            }
            $chartData[] = [
                'day' => $day->format('D'),
                'date' => $day->format('d M'),
                'revenue' => $dayRev,
                'orders' => $dayOrders,
                'is_today' => $i === 0,
            ];
        }

        $config = RestaurantSetting::getAllSettings();

        return view('pages.dashboard.dashboard', compact(
            'todayRevenue',
            'totalRevenueAll',
            'todayOrdersCount',
            'totalOrdersCount',
            'unpaidOrdersCount',
            'completedOrdersCount',
            'totalCash',
            'totalOnline',
            'cashPercent',
            'onlinePercent',
            'avgOrderValue',
            'lowStockItemsCount',
            'totalMenuItemsCount',
            'csatAvg',
            'csatTotal',
            'recentOrders',
            'topDishes',
            'maxDishQty',
            'chartData',
            'maxChartRev',
            'config'
        ));
    }

    /* =========================================================================
       1. ORDERS MODULE (CRUD & POS ORDER ENTRY)
       ========================================================================= */

    /**
     * Orders List with filtering
     */
    public function orders(Request $request)
    {
        $statusFilter = $request->query('status', 'all');

        $query = Order::with('items')->latest();

        if ($statusFilter !== 'all') {
            if ($statusFilter === 'Dine In' || $statusFilter === 'Take Away') {
                $query->where('order_type', $statusFilter);
            } elseif ($statusFilter === 'Complete' || $statusFilter === 'Paid') {
                $query->whereIn('status', ['Complete', 'Paid']);
            } elseif ($statusFilter === 'Unpaid') {
                $query->where('status', 'Unpaid');
            } else {
                $query->where('status', $statusFilter);
            }
        }

        $orders = $query->get();

        $statusClasses = [
            'Complete' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
            'Paid' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
            'Unpaid' => 'bg-amber-100 text-amber-700 border border-amber-200',
            'Cancelled' => 'bg-rose-100 text-rose-700 border border-rose-200',
        ];

        $totalCount = Order::count();
        $dineInCount = Order::where('order_type', 'Dine In')->count();
        $takeAwayCount = Order::where('order_type', 'Take Away')->count();
        $unpaidCount = Order::where('status', 'Unpaid')->count();
        $completeCount = Order::whereIn('status', ['Complete', 'Paid'])->count();

        $autoPrintOrder = null;
        if (session('auto_print_invoice_order_id')) {
            $autoPrintOrder = Order::with('items')->find(session('auto_print_invoice_order_id'));
        }
        $restaurantSettings = RestaurantSetting::getAllSettings();

        return view('pages.orders.index', compact('orders', 'statusFilter', 'statusClasses', 'totalCount', 'dineInCount', 'takeAwayCount', 'unpaidCount', 'completeCount', 'autoPrintOrder', 'restaurantSettings'));
    }

    /**
     * Display New Order Screen (Populated from MySQL database)
     */
    public function newOrder()
    {
        $tables = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 'VIP-1', 'VIP-2'];
        $attendants = [
            'ChrisThomas - 226',
            'Laura Olivia - 228',
            'Michael Jack - 227',
            'Hannah Liam - 229',
        ];

        // Fetch active categories and food items from database
        $categories = Category::where('status', 'Active')->pluck('name');
        $foodItems = FoodItem::with('category')->where('status', '!=', 'Out of Stock')->get()->map(function ($f) {
            return [
                'id' => $f->id,
                'name' => $f->name,
                'category' => $f->category?->name ?? 'Main Menu',
                'price' => (float) $f->price,
                'has_half_portion' => (bool) $f->has_half_portion,
                'half_price' => $f->half_price ? (float) $f->half_price : null,
            ];
        });

        $maxNum = Order::selectRaw('MAX(CAST(order_number AS UNSIGNED)) as max_num')->value('max_num') ?: 100;
        $nextOrderNumber = (string) ($maxNum + 1);
        $restaurantSettings = RestaurantSetting::getAllSettings();

        return view('pages.orders.new-order', compact('tables', 'attendants', 'categories', 'foodItems', 'nextOrderNumber', 'restaurantSettings'));
    }

    /**
     * Store new order in MySQL database
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'order_type' => 'required|string',
            'attendant' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += ($item['quantity'] * $item['price']);
        }
        $tax = 0.00; // No GST as requested
        $total = $subtotal;
        $maxNum = Order::selectRaw('MAX(CAST(order_number AS UNSIGNED)) as max_num')->value('max_num') ?: 100;
        $nextOrderNumber = (string) ($maxNum + 1);

        $paymentAction = $request->input('payment_action', 'pay_later');
        $paymentMethod = null;
        $cashAmount = 0.00;
        $onlineAmount = 0.00;
        $status = 'Unpaid';

        if ($paymentAction === 'pay_now') {
            $status = 'Complete';
            $paymentMethod = $request->input('payment_method', 'Cash');
            if ($paymentMethod === 'Cash') {
                $cashAmount = $total;
            } elseif ($paymentMethod === 'Online') {
                $onlineAmount = $total;
            } elseif ($paymentMethod === 'Split') {
                $cashAmount = (float) $request->input('cash_amount', 0);
                $onlineAmount = (float) $request->input('online_amount', 0);
            }
        }

        $order = Order::create([
            'order_number' => $nextOrderNumber,
            'order_type' => $request->order_type,
            'table_no' => $request->order_type === 'Dine In' ? ($request->table ? 'Table ' . $request->table : 'Table -') : null,
            'attendant' => $request->attendant,
            'order_time' => now()->format('d-M-Y H:i:s'),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total_amount' => $total,
            'status' => $status,
            'payment_method' => $paymentMethod,
            'cash_amount' => $cashAmount,
            'online_amount' => $onlineAmount,
            'notes' => $request->notes,
        ]);

        foreach ($request->items as $item) {
            $food = FoodItem::where('name', $item['name'])->first();
            OrderItem::create([
                'order_id' => $order->id,
                'food_item_id' => $food?->id,
                'item_name' => $item['name'],
                'category_name' => $item['category'] ?? ($food?->category?->name ?? 'Main Menu'),
                'portion' => $item['portion'] ?? 'Regular',
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'total_price' => $item['quantity'] * $item['price'],
                'notes' => $item['notes'] ?? null,
            ]);

            // Increment orders count on food item
            if ($food) {
                $food->increment('orders_count', $item['quantity']);
            }
        }

        $msg = $status === 'Complete' 
            ? 'Order #' . $order->order_number . ' placed & bill completed (' . $paymentMethod . ')!' 
            : 'Order #' . $order->order_number . ' placed successfully (Pay Later - Unpaid)!';

        $redirect = redirect()->route('restaurant.orders')->with('success', $msg);
        if ($status === 'Complete') {
            $redirect->with('auto_print_invoice_order_id', $order->id);
        }

        return $redirect;
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $request->validate(['status' => 'required|string']);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order #' . $order->order_number . ' status updated to ' . $request->status);
    }

    /**
     * Settle order bill payment from Unpaid to Complete
     */
    public function settleOrderPayment(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'payment_method' => 'required|in:Cash,Online,Split',
            'cash_amount' => 'nullable|numeric|min:0',
            'online_amount' => 'nullable|numeric|min:0',
        ]);

        $paymentMethod = $request->payment_method;
        $cashAmount = 0.00;
        $onlineAmount = 0.00;

        if ($paymentMethod === 'Cash') {
            $cashAmount = $order->total_amount;
        } elseif ($paymentMethod === 'Online') {
            $onlineAmount = $order->total_amount;
        } elseif ($paymentMethod === 'Split') {
            $cashAmount = (float) ($request->cash_amount ?? 0);
            $onlineAmount = (float) ($request->online_amount ?? 0);
        }

        $order->update([
            'status' => 'Complete',
            'payment_method' => $paymentMethod,
            'cash_amount' => $cashAmount,
            'online_amount' => $onlineAmount,
        ]);

        return back()->with('success', 'Order #' . $order->order_number . ' bill settled successfully via ' . $paymentMethod . '!')
                     ->with('auto_print_invoice_order_id', $order->id);
    }

    /* =========================================================================
       2. FOOD / MENU MODULE (DATABASE CRUD)
       ========================================================================= */

    /**
     * Food / Menu catalog
     */
    public function menu(Request $request)
    {
        $selectedCategory = $request->query('category', 'all');

        $categories = Category::withCount('foodItems')->where('status', 'Active')->get();

        $query = FoodItem::with('category')->latest();

        if ($selectedCategory !== 'all') {
            $query->whereHas('category', function ($q) use ($selectedCategory) {
                $q->where('slug', $selectedCategory)->orWhere('name', $selectedCategory);
            });
        }

        $dishes = $query->get();

        return view('pages.menu.index', compact('dishes', 'categories', 'selectedCategory'));
    }

    /**
     * Store new dish in MySQL database
     */
    public function storeDish(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'half_price' => 'nullable|numeric|min:0',
            'prep_time' => 'nullable|string',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'is_veg' => 'nullable|boolean',
        ]);

        $hasHalf = $request->boolean('has_half_portion');

        FoodItem::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'has_half_portion' => $hasHalf,
            'half_price' => $hasHalf ? $request->half_price : null,
            'prep_time' => $request->prep_time ?: '15 mins',
            'status' => $request->status ?: 'Available',
            'description' => $request->description,
            'is_veg' => $request->boolean('is_veg'),
            'rating' => 5.0,
            'orders_count' => 0,
        ]);

        return redirect()->route('restaurant.menu')->with('success', 'New dish added successfully to menu!');
    }

    /**
     * Update dish in MySQL database
     */
    public function updateDish(Request $request, $id)
    {
        $dish = FoodItem::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'half_price' => 'nullable|numeric|min:0',
            'prep_time' => 'nullable|string',
            'status' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $hasHalf = $request->boolean('has_half_portion');

        $dish->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'has_half_portion' => $hasHalf,
            'half_price' => $hasHalf ? $request->half_price : null,
            'prep_time' => $request->prep_time,
            'status' => $request->status,
            'description' => $request->description,
            'is_veg' => $request->boolean('is_veg'),
        ]);

        return redirect()->route('restaurant.menu')->with('success', 'Dish updated successfully!');
    }

    /**
     * Delete dish
     */
    public function deleteDish($id)
    {
        $dish = FoodItem::findOrFail($id);
        $dish->delete();

        return redirect()->route('restaurant.menu')->with('success', 'Dish removed from menu.');
    }

    /**
     * Toggle dish availability status
     */
    public function toggleDishStatus($id)
    {
        $dish = FoodItem::findOrFail($id);
        $dish->status = ($dish->status === 'Available') ? 'Out of Stock' : 'Available';
        $dish->save();

        return back()->with('success', 'Dish availability updated!');
    }

    /* =========================================================================
       3. CATEGORY MODULE (DATABASE CRUD)
       ========================================================================= */

    /**
     * Categories List
     */
    public function categories()
    {
        $categories = Category::withCount('foodItems')->get();

        return view('pages.categories.index', compact('categories'));
    }

    /**
     * Store new category in MySQL database
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon ?: 'dish',
            'status' => $request->status ?: 'Active',
        ]);

        return redirect()->route('restaurant.categories')->with('success', 'Category created successfully!');
    }

    /**
     * Update category
     */
    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('restaurant.categories')->with('success', 'Category updated successfully!');
    }

    /**
     * Delete category
     */
    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('restaurant.categories')->with('success', 'Category deleted successfully.');
    }

    /* =========================================================================
       4. CUSTOMER SATISFACTION (CSAT) & QR REVIEW SYSTEM
       ========================================================================= */

    public function customerSatisfaction(Request $request)
    {
        $ratingFilter = $request->input('rating');
        $reviewsQuery = Review::latest();
        if ($ratingFilter) {
            $reviewsQuery->where('rating', $ratingFilter);
        }
        $reviews = $reviewsQuery->get();

        $totalReviews = Review::count();
        $overallScore = $totalReviews > 0 ? number_format(Review::avg('rating'), 1) : '5.0';
        $positiveReviews = Review::where('rating', '>=', 4)->count();
        $satisfactionRate = $totalReviews > 0 ? round(($positiveReviews / $totalReviews) * 100) : 100;

        $starCounts = [
            5 => Review::where('rating', 5)->count(),
            4 => Review::where('rating', 4)->count(),
            3 => Review::where('rating', 3)->count(),
            2 => Review::where('rating', 2)->count(),
            1 => Review::where('rating', 1)->count(),
        ];

        // Generate modern vector SVG QR code with stylish dot-matrix / curves and gradients
        $qrStyle = $request->input('qr_style', 'dots');
        $reviewUrl = route('restaurant.review');
        $qrSvg = $this->generateModernQr($reviewUrl, $qrStyle, 260, true);

        $stats = [
            'overall_score' => $overallScore,
            'total_reviews' => $totalReviews,
            'satisfaction_rate' => $satisfactionRate . '%',
            'star_counts' => $starCounts,
            'positive_reviews' => $positiveReviews,
        ];

        $config = RestaurantSetting::getAllSettings();

        return view('pages.customer-satisfaction.index', compact('stats', 'reviews', 'qrSvg', 'reviewUrl', 'config', 'ratingFilter', 'qrStyle'));
    }

    /**
     * Show customer-facing review submission form
     */
    public function showReviewForm(Request $request)
    {
        $config = RestaurantSetting::getAllSettings();
        return view('pages.reviews.form', compact('config'));
    }

    /**
     * Store customer review submission
     */
    public function storeReview(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1500',
            'customer_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        $customerName = trim($request->customer_name);
        if (!$customerName) {
            $customerName = $request->phone ? ('Guest (' . $request->phone . ')') : 'Valued Guest';
        }

        $review = Review::create([
            'customer_name' => $customerName,
            'phone' => $request->phone,
            'table_no' => null,
            'rating' => (int) $request->rating,
            'comment' => $request->comment,
            'status' => 'Approved',
        ]);

        return back()->with('review_submitted', true)
                     ->with('rating', $review->rating)
                     ->with('customer_name', $review->customer_name)
                     ->with('phone', $review->phone);
    }

    /**
     * View and print the Restaurant Table Tent Standee Card template
     */
    public function tableTentTemplate(Request $request)
    {
        $qrStyle = $request->input('qr_style', 'dots');
        $url = route('restaurant.review');

        $qrSvg = $this->generateModernQr($url, $qrStyle, 260, true);

        $config = RestaurantSetting::getAllSettings();

        return view('pages.customer-satisfaction.table-tent', compact('config', 'qrSvg', 'url', 'qrStyle'));
    }

    /**
     * Download standalone SVG QR Code
     */
    public function downloadQrCode(Request $request)
    {
        $qrStyle = $request->input('qr_style', 'dots');
        $url = route('restaurant.review');

        $svg = $this->generateModernQr($url, $qrStyle, 360, true);

        $filename = 'resco_review_qr_' . $qrStyle . '.svg';

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    /**
     * Generate modern, stylish vector SVG QR Code
     * Supported styles:
     * - 'dots': Vibrant Terracotta-to-Dark Slate diagonal gradient with circular dot modules & pointy finder eyes
     * - 'curves': Fluid squircle rounded modules with sunset orange accents
     * - 'gold': Royal Gold & Champagne luxury gradient with circular target eyes
     * - 'emerald': Fresh Mint & Deep Emerald gradient with modern dot modules
     */
    private function generateModernQr(string $url, string $style = 'dots', int $size = 280, bool $includeBadge = true): string
    {
        $accentColor = '#ea580c';
        $bgRgb = new Rgb(255, 255, 255);

        if ($style === 'curves') {
            $fill = Fill::withForegroundColor(
                $bgRgb,
                new Rgb(30, 41, 59),
                new EyeFill(new Rgb(234, 88, 12), new Rgb(249, 115, 22)),
                new EyeFill(new Rgb(234, 88, 12), new Rgb(249, 115, 22)),
                new EyeFill(new Rgb(234, 88, 12), new Rgb(249, 115, 22))
            );
            $module = new RoundnessModule(RoundnessModule::STRONG);
            $eye = PointyEye::instance();
            $accentColor = '#ea580c';
        } elseif ($style === 'gold') {
            $fill = Fill::withForegroundGradient(
                $bgRgb,
                new Gradient(new Rgb(217, 119, 6), new Rgb(30, 41, 59), GradientType::DIAGONAL()),
                new EyeFill(new Rgb(217, 119, 6), new Rgb(180, 83, 9)),
                new EyeFill(new Rgb(217, 119, 6), new Rgb(180, 83, 9)),
                new EyeFill(new Rgb(217, 119, 6), new Rgb(180, 83, 9))
            );
            $module = new DotsModule(0.88);
            $eye = SimpleCircleEye::instance();
            $accentColor = '#d97706';
        } elseif ($style === 'emerald') {
            $fill = Fill::withForegroundGradient(
                $bgRgb,
                new Gradient(new Rgb(16, 185, 129), new Rgb(15, 23, 42), GradientType::DIAGONAL()),
                new EyeFill(new Rgb(16, 185, 129), new Rgb(5, 150, 105)),
                new EyeFill(new Rgb(16, 185, 129), new Rgb(5, 150, 105)),
                new EyeFill(new Rgb(16, 185, 129), new Rgb(5, 150, 105))
            );
            $module = new DotsModule(0.88);
            $eye = PointyEye::instance();
            $accentColor = '#10b981';
        } else { // default 'dots' (Modern Sunset Terracotta Dot-Matrix)
            $fill = Fill::withForegroundGradient(
                $bgRgb,
                new Gradient(new Rgb(234, 88, 12), new Rgb(24, 24, 27), GradientType::DIAGONAL()),
                new EyeFill(new Rgb(234, 88, 12), new Rgb(194, 65, 12)),
                new EyeFill(new Rgb(234, 88, 12), new Rgb(194, 65, 12)),
                new EyeFill(new Rgb(234, 88, 12), new Rgb(194, 65, 12))
            );
            $module = new DotsModule(0.88);
            $eye = PointyEye::instance();
            $accentColor = '#ea580c';
        }

        $renderer = new ImageRenderer(
            new RendererStyle($size, 1, $module, $eye, $fill),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        // ErrorCorrectionLevel Q (25% recovery capacity) ensures reliable phone camera scanning even with center badge
        $svg = $writer->writeString($url, Encoder::DEFAULT_BYTE_MODE_ENCODING, ErrorCorrectionLevel::Q());

        if ($includeBadge) {
            $center = $size / 2;
            $radius = round($size * 0.11);
            $innerR = round($radius * 0.80);
            $starR = round($innerR * 0.65);

            $p1x = $center; $p1y = $center - $starR;
            $p2x = $center + $starR * 0.28; $p2y = $center - $starR * 0.28;
            $p3x = $center + $starR; $p3y = $center - $starR * 0.20;
            $p4x = $center + $starR * 0.42; $p4y = $center + $starR * 0.28;
            $p5x = $center + $starR * 0.65; $p5y = $center + $starR * 0.95;
            $p6x = $center; $p6y = $center + $starR * 0.50;
            $p7x = $center - $starR * 0.65; $p7y = $center + $starR * 0.95;
            $p8x = $center - $starR * 0.42; $p8y = $center + $starR * 0.28;
            $p9x = $center - $starR; $p9y = $center - $starR * 0.20;
            $p10x = $center - $starR * 0.28; $p10y = $center - $starR * 0.28;

            $starPath = "M $p1x $p1y L $p2x $p2y L $p3x $p3y L $p4x $p4y L $p5x $p5y L $p6x $p6y L $p7x $p7y L $p8x $p8y L $p9x $p9y L $p10x $p10y Z";

            $badge = "<g id=\"qr-center-badge\">"
                   . "<circle cx=\"$center\" cy=\"$center\" r=\"$radius\" fill=\"#ffffff\" stroke=\"$accentColor\" stroke-width=\"3.5\"/>"
                   . "<circle cx=\"$center\" cy=\"$center\" r=\"$innerR\" fill=\"$accentColor\"/>"
                   . "<path d=\"$starPath\" fill=\"#ffffff\"/>"
                   . "</g>";

            $svg = str_replace('</svg>', $badge . '</svg>', $svg);
        }

        return $svg;
    }

    /**
     * Delete customer review
     */
    public function deleteReview($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Customer review deleted successfully.');
    }

    public function billing()
    {
        $invoices = Order::with('items')->latest()->get();

        return view('pages.billing.index', compact('invoices'));
    }

    public function reports(Request $request)
    {
        $preset = $request->input('preset', 'today');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $start = null;
        $end = null;
        $label = 'Today';

        if ($request->filled('from_date') || $request->filled('to_date')) {
            $preset = 'custom';
            if ($request->filled('from_date')) {
                $start = Carbon::parse($fromDate)->startOfDay();
            }
            if ($request->filled('to_date')) {
                $end = Carbon::parse($toDate)->endOfDay();
            } else {
                $end = Carbon::today()->endOfDay();
            }
            $label = ($start ? $start->format('d M Y') : 'Beginning') . ' - ' . ($end ? $end->format('d M Y') : 'Today');
        } else {
            if ($preset === 'today') {
                $start = Carbon::today()->startOfDay();
                $end = Carbon::today()->endOfDay();
                $label = 'Today (' . Carbon::today()->format('d M Y') . ')';
                $fromDate = Carbon::today()->toDateString();
                $toDate = Carbon::today()->toDateString();
            } elseif ($preset === 'yesterday') {
                $start = Carbon::yesterday()->startOfDay();
                $end = Carbon::yesterday()->endOfDay();
                $label = 'Yesterday (' . Carbon::yesterday()->format('d M Y') . ')';
                $fromDate = Carbon::yesterday()->toDateString();
                $toDate = Carbon::yesterday()->toDateString();
            } elseif ($preset === 'this_week') {
                $start = Carbon::today()->subDays(6)->startOfDay();
                $end = Carbon::today()->endOfDay();
                $label = 'Last 7 Days (' . $start->format('d M') . ' - ' . $end->format('d M Y') . ')';
                $fromDate = $start->toDateString();
                $toDate = $end->toDateString();
            } elseif ($preset === 'this_month') {
                $start = Carbon::today()->startOfMonth()->startOfDay();
                $end = Carbon::today()->endOfMonth()->endOfDay();
                $label = 'This Month (' . Carbon::today()->format('F Y') . ')';
                $fromDate = $start->toDateString();
                $toDate = Carbon::today()->toDateString();
            } elseif ($preset === 'all') {
                $start = null;
                $end = null;
                $label = 'All Time';
                $fromDate = '';
                $toDate = '';
            }
        }

        // Fetch orders in range
        $ordersQuery = Order::with('items')->latest();
        if ($start) {
            $ordersQuery->where('created_at', '>=', $start);
        }
        if ($end) {
            $ordersQuery->where('created_at', '<=', $end);
        }

        $allOrders = $ordersQuery->get();
        $paidOrders = $allOrders->whereIn('status', ['Complete', 'Paid']);
        $unpaidOrders = $allOrders->where('status', 'Unpaid');

        // Payment & Financial Calculations
        $totalSales = (float) $paidOrders->sum('total_amount');

        $totalCash = (float) $paidOrders->sum(function ($o) {
            if ($o->payment_method === 'Cash') {
                return (float) $o->total_amount;
            } elseif ($o->payment_method === 'Split') {
                return (float) $o->cash_amount;
            }
            return 0;
        });

        $totalOnline = (float) $paidOrders->sum(function ($o) {
            if ($o->payment_method === 'Online') {
                return (float) $o->total_amount;
            } elseif ($o->payment_method === 'Split') {
                return (float) $o->online_amount;
            }
            return 0;
        });

        // Cash in Hand represents physical cash collected in the register
        $cashInHand = $totalCash;

        $completedCount = $paidOrders->count();
        $unpaidCount = $unpaidOrders->count();
        $unpaidAmount = (float) $unpaidOrders->sum('total_amount');
        $avgOrderValue = $completedCount > 0 ? ($totalSales / $completedCount) : 0;

        // Top Selling Dishes in selected range
        $topDishesQuery = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['Complete', 'Paid']);

        if ($start) {
            $topDishesQuery->where('orders.created_at', '>=', $start);
        }
        if ($end) {
            $topDishesQuery->where('orders.created_at', '<=', $end);
        }

        $topDishes = $topDishesQuery
            ->selectRaw('order_items.item_name, order_items.category_name, SUM(order_items.quantity) as total_qty, SUM(order_items.total_price) as total_revenue')
            ->groupBy('order_items.item_name', 'order_items.category_name')
            ->orderByDesc('total_qty')
            ->take(8)
            ->get();

        // Category-wise Sales in selected range
        $categorySalesQuery = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['Complete', 'Paid']);

        if ($start) {
            $categorySalesQuery->where('orders.created_at', '>=', $start);
        }
        if ($end) {
            $categorySalesQuery->where('orders.created_at', '<=', $end);
        }

        $categorySales = $categorySalesQuery
            ->selectRaw('order_items.category_name, SUM(order_items.quantity) as total_qty, SUM(order_items.total_price) as total_revenue')
            ->groupBy('order_items.category_name')
            ->orderByDesc('total_revenue')
            ->get();

        // CSV Export handling
        if ($request->input('export') === 'csv') {
            return $this->exportSalesReportCsv($allOrders, $label, $totalSales, $totalCash, $totalOnline, $cashInHand);
        }

        return view('pages.reports.index', compact(
            'allOrders',
            'paidOrders',
            'unpaidOrders',
            'totalSales',
            'totalCash',
            'totalOnline',
            'cashInHand',
            'completedCount',
            'unpaidCount',
            'unpaidAmount',
            'avgOrderValue',
            'topDishes',
            'categorySales',
            'preset',
            'fromDate',
            'toDate',
            'label'
        ));
    }

    /**
     * Export Sales Report as CSV
     */
    private function exportSalesReportCsv($orders, $label, $totalSales, $totalCash, $totalOnline, $cashInHand)
    {
        $filename = 'resco_sales_report_' . date('Y_m_d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($orders, $label, $totalSales, $totalCash, $totalOnline, $cashInHand) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['RESCO RESTAURANT - SALES & COLLECTIONS REPORT']);
            fputcsv($handle, ['Filter Range:', $label]);
            fputcsv($handle, ['Generated At:', now()->format('d-M-Y H:i:s')]);
            fputcsv($handle, []);

            fputcsv($handle, ['SUMMARY TOTALS']);
            fputcsv($handle, ['Total Sales (Gross):', 'INR ' . number_format($totalSales, 2)]);
            fputcsv($handle, ['Total Cash Collected:', 'INR ' . number_format($totalCash, 2)]);
            fputcsv($handle, ['Total Online Collected:', 'INR ' . number_format($totalOnline, 2)]);
            fputcsv($handle, ['Cash in Hand:', 'INR ' . number_format($cashInHand, 2)]);
            fputcsv($handle, []);

            fputcsv($handle, ['ORDER TRANSACTIONS']);
            fputcsv($handle, ['Order #', 'Order Type', 'Table', 'Attendant', 'Order Time', 'Payment Mode', 'Cash (INR)', 'Online (INR)', 'Total (INR)', 'Status']);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    $order->order_type,
                    $order->table_no ?: 'N/A',
                    $order->attendant,
                    $order->order_time ?: $order->created_at->format('d-M-Y H:i:s'),
                    $order->payment_method ?: 'Unpaid',
                    number_format((float)$order->cash_amount, 2),
                    number_format((float)$order->online_amount, 2),
                    number_format((float)$order->total_amount, 2),
                    $order->status,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function settings()
    {
        $config = RestaurantSetting::getAllSettings();
        $installedPrinters = ThermalPrintService::getInstalledPrinters();
        $defaultPrinter = ThermalPrintService::getDefaultPrinter();

        return view('pages.settings.index', compact('config', 'installedPrinters', 'defaultPrinter'));
    }

    public function updateSettings(Request $request)
    {
        $fields = [
            'restaurant_name', 
            'tagline', 
            'currency', 
            'phone', 
            'email', 
            'address', 
            'gstin', 
            'invoice_footer', 
            'bg_pattern', 
            'bg_pattern_opacity', 
            'printer_paper_size',
            'direct_print_enabled',
            'direct_printer_name'
        ];

        foreach ($fields as $f) {
            if ($request->has($f)) {
                RestaurantSetting::set($f, $request->input($f));
            }
        }

        // Handle direct_print_enabled checkbox when unchecked
        if (!$request->has('direct_print_enabled')) {
            RestaurantSetting::set('direct_print_enabled', '0');
        }

        return back()->with('success', 'Restaurant and thermal printer settings saved successfully!');
    }

    /**
     * Direct Print Order to Connected Thermal Printer (Zero Browser Dialog)
     */
    public function directPrintOrder(Request $request, $id)
    {
        $order = Order::with('items')->findOrFail($id);
        $result = ThermalPrintService::printOrderDirect($order);

        return response()->json($result);
    }

    /**
     * Send a Test Print job directly to the selected thermal printer
     */
    public function testDirectPrint(Request $request)
    {
        $printerName = $request->input('printer_name');
        $paperSize = $request->input('paper_size', '58mm');

        $result = ThermalPrintService::printTestReceipt($printerName, $paperSize);

        if ($result['success']) {
            return back()->with('success', '✅ ' . $result['message']);
        } else {
            return back()->with('error', '❌ ' . $result['message']);
        }
    }
}
