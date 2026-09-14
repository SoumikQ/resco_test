<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\FoodItem;
use App\Models\Order;
use App\Models\OrderItem;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Categories
        $categoriesData = [
            ['name' => 'Main Menu', 'slug' => 'main-menu', 'icon' => 'dish', 'description' => 'Delectable gravies and main course delicacies'],
            ['name' => 'Starters & Tandoor', 'slug' => 'starters-tandoor', 'icon' => 'appetizer', 'description' => 'Tandoori kebabs, tikkas and crispy starters'],
            ['name' => 'Breads & Biriyanis', 'slug' => 'breads-biriyanis', 'icon' => 'bread', 'description' => 'Aromatic dum biriyanis and fresh tandoori breads'],
            ['name' => 'South Indian & Snacks', 'slug' => 'south-indian', 'icon' => 'dish', 'description' => 'Crispy dosas, idlis and savory snacks'],
            ['name' => 'Beverages & Drinks', 'slug' => 'beverages', 'icon' => 'drink', 'description' => 'Chilled lassi, shakes, fresh juices and hot chai'],
            ['name' => 'Desserts & Sweets', 'slug' => 'desserts', 'icon' => 'dessert', 'description' => 'Authentic Indian sweets and desserts'],
        ];

        $categories = [];
        foreach ($categoriesData as $c) {
            $categories[$c['name']] = Category::create($c);
        }

        // 2. Seed Food Items (in Indian Rupee ₹)
        $itemsData = [
            // Main Menu
            [
                'category' => 'Main Menu',
                'name' => 'Butter Chicken',
                'price' => 240.00,
                'prep_time' => '15 mins',
                'status' => 'Available',
                'is_veg' => false,
                'rating' => 4.9,
                'orders_count' => 310,
                'description' => 'Tender chicken simmered in rich creamy tomato cashew gravy',
            ],
            [
                'category' => 'Main Menu',
                'name' => 'Paneer Butter Masala',
                'price' => 180.00,
                'prep_time' => '12 mins',
                'status' => 'Available',
                'is_veg' => true,
                'rating' => 4.8,
                'orders_count' => 240,
                'description' => 'Fresh cottage cheese cubes cooked in buttery spiced makhani gravy',
            ],
            [
                'category' => 'Main Menu',
                'name' => 'Dal Makhani',
                'price' => 150.00,
                'prep_time' => '10 mins',
                'status' => 'Available',
                'is_veg' => true,
                'rating' => 4.7,
                'orders_count' => 195,
                'description' => 'Black lentils slow cooked overnight with butter and cream',
            ],
            [
                'category' => 'Main Menu',
                'name' => 'Mutton Rogan Josh',
                'price' => 320.00,
                'prep_time' => '20 mins',
                'status' => 'Available',
                'is_veg' => false,
                'rating' => 4.9,
                'orders_count' => 150,
                'description' => 'Kashmiri style tender mutton braised in aromatic red gravy',
            ],

            // Breads & Biriyanis
            [
                'category' => 'Breads & Biriyanis',
                'name' => 'Chicken Dum Biriyani',
                'price' => 220.00,
                'prep_time' => '18 mins',
                'status' => 'Available',
                'is_veg' => false,
                'rating' => 4.9,
                'orders_count' => 540,
                'description' => 'Fragrant basmati rice layered with spiced marinated chicken and saffron',
            ],
            [
                'category' => 'Breads & Biriyanis',
                'name' => 'Mutton Hyderabadi Biriyani',
                'price' => 310.00,
                'prep_time' => '22 mins',
                'status' => 'Available',
                'is_veg' => false,
                'rating' => 4.8,
                'orders_count' => 390,
                'description' => 'Authentic royal spiced mutton dum biriyani served with mirchi ka salan',
            ],
            [
                'category' => 'Breads & Biriyanis',
                'name' => 'Butter Garlic Naan',
                'price' => 45.00,
                'prep_time' => '6 mins',
                'status' => 'Available',
                'is_veg' => true,
                'rating' => 4.9,
                'orders_count' => 780,
                'description' => 'Clay oven baked flatbread garnished with minced garlic and melted butter',
            ],
            [
                'category' => 'Breads & Biriyanis',
                'name' => 'Tandoori Roti',
                'price' => 20.00,
                'prep_time' => '5 mins',
                'status' => 'Available',
                'is_veg' => true,
                'rating' => 4.6,
                'orders_count' => 920,
                'description' => 'Traditional whole wheat bread cooked crisp in tandoor',
            ],

            // Starters & Tandoor
            [
                'category' => 'Starters & Tandoor',
                'name' => 'Chicken Tikka Kebab',
                'price' => 190.00,
                'prep_time' => '15 mins',
                'status' => 'Available',
                'is_veg' => false,
                'rating' => 4.8,
                'orders_count' => 290,
                'description' => 'Boneless chicken cubes marinated in yogurt and tandoori masala grilled to perfection',
            ],
            [
                'category' => 'Starters & Tandoor',
                'name' => 'Paneer Tikka',
                'price' => 170.00,
                'prep_time' => '12 mins',
                'status' => 'Available',
                'is_veg' => true,
                'rating' => 4.7,
                'orders_count' => 220,
                'description' => 'Char-grilled cottage cheese with bell peppers and onions',
            ],

            // Beverages & Drinks
            [
                'category' => 'Beverages & Drinks',
                'name' => 'Sweet Mango Lassi',
                'price' => 60.00,
                'prep_time' => '4 mins',
                'status' => 'Available',
                'is_veg' => true,
                'rating' => 4.9,
                'orders_count' => 340,
                'description' => 'Thick creamy yogurt smoothie blended with sweet Alphonso mango pulp',
            ],
            [
                'category' => 'Beverages & Drinks',
                'name' => 'Special Masala Chai',
                'price' => 30.00,
                'prep_time' => '5 mins',
                'status' => 'Available',
                'is_veg' => true,
                'rating' => 4.8,
                'orders_count' => 410,
                'description' => 'Hot brewed Indian milk tea infused with cardamom, ginger and cinnamon',
            ],

            // Desserts
            [
                'category' => 'Desserts & Sweets',
                'name' => 'Gulab Jamun (2 pcs)',
                'price' => 50.00,
                'prep_time' => '3 mins',
                'status' => 'Available',
                'is_veg' => true,
                'rating' => 4.9,
                'orders_count' => 380,
                'description' => 'Soft fried milk solid dumplings soaked in rose flavored warm sugar syrup',
            ],
            [
                'category' => 'Desserts & Sweets',
                'name' => 'Kesar Rasmalai (2 pcs)',
                'price' => 70.00,
                'prep_time' => '3 mins',
                'status' => 'Low Stock',
                'is_veg' => true,
                'rating' => 4.8,
                'orders_count' => 180,
                'description' => 'Spongy cottage cheese patties in saffron flavored sweetened milk with pistachios',
            ],
        ];

        $foodItems = [];
        foreach ($itemsData as $it) {
            $catName = $it['category'];
            unset($it['category']);
            $it['category_id'] = $categories[$catName]->id;
            $foodItems[$it['name']] = FoodItem::create($it);
        }

        // 3. Seed Sample Orders (in Indian Rupee ₹)
        $ordersData = [
            [
                'order_number' => '112',
                'order_type' => 'Take Away',
                'table_no' => null,
                'attendant' => 'Laura Olivia-228',
                'order_time' => '09-Jan-2023 10:00:00',
                'status' => 'Paid',
                'notes' => 'Pack extra mint chutney',
                'items' => [
                    ['name' => 'Chicken Dum Biriyani', 'qty' => 1, 'price' => 220.00],
                    ['name' => 'Sweet Mango Lassi', 'qty' => 1, 'price' => 60.00],
                ]
            ],
            [
                'order_number' => '111',
                'order_type' => 'Take Away',
                'table_no' => null,
                'attendant' => 'Michael Jack-227',
                'order_time' => '10-Jan-2023 09:05:00',
                'status' => 'KOT Pending',
                'notes' => 'Medium spicy',
                'items' => [
                    ['name' => 'Butter Chicken', 'qty' => 2, 'price' => 240.00],
                    ['name' => 'Butter Garlic Naan', 'qty' => 4, 'price' => 45.00],
                    ['name' => 'Sweet Mango Lassi', 'qty' => 2, 'price' => 60.00],
                ]
            ],
            [
                'order_number' => '110',
                'order_type' => 'Dine In',
                'table_no' => 'Table 2',
                'attendant' => 'Chris Thomas-226',
                'order_time' => '04-Jan-2023 01:47:53',
                'status' => 'Paid',
                'notes' => 'Hot serving requested',
                'items' => [
                    ['name' => 'Chicken Dum Biriyani', 'qty' => 2, 'price' => 220.00],
                    ['name' => 'Chicken Tikka Kebab', 'qty' => 1, 'price' => 190.00],
                    ['name' => 'Sweet Mango Lassi', 'qty' => 2, 'price' => 60.00],
                ]
            ],
            [
                'order_number' => '109',
                'order_type' => 'Dine In',
                'table_no' => 'Table 5',
                'attendant' => 'Hannah Liam-229',
                'order_time' => '20-Jan-2023 05:30:00',
                'status' => 'Bill Generated',
                'notes' => 'Bill split requested',
                'items' => [
                    ['name' => 'Paneer Butter Masala', 'qty' => 1, 'price' => 180.00],
                    ['name' => 'Butter Garlic Naan', 'qty' => 2, 'price' => 45.00],
                    ['name' => 'Gulab Jamun (2 pcs)', 'qty' => 1, 'price' => 50.00],
                ]
            ],
            [
                'order_number' => '108',
                'order_type' => 'Dine In',
                'table_no' => 'Table 1',
                'attendant' => 'Laura Olivia-228',
                'order_time' => '23-Jan-2023 00:47:53',
                'status' => 'Items Served',
                'notes' => 'Quick service',
                'items' => [
                    ['name' => 'Mutton Hyderabadi Biriyani', 'qty' => 1, 'price' => 310.00],
                    ['name' => 'Special Masala Chai', 'qty' => 2, 'price' => 30.00],
                ]
            ],
        ];

        foreach ($ordersData as $od) {
            $items = $od['items'];
            unset($od['items']);

            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += ($item['qty'] * $item['price']);
            }
            $tax = round($subtotal * 0.05, 2); // 5% GST
            $total = $subtotal + $tax;

            $od['subtotal'] = $subtotal;
            $od['tax'] = $tax;
            $od['total_amount'] = $total;

            $order = Order::create($od);

            foreach ($items as $item) {
                $food = $foodItems[$item['name']] ?? null;
                OrderItem::create([
                    'order_id' => $order->id,
                    'food_item_id' => $food?->id,
                    'item_name' => $item['name'],
                    'category_name' => $food?->category?->name ?? 'Main Menu',
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['qty'] * $item['price'],
                ]);
            }
        }
    }
}
