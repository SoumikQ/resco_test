<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = [
            [
                'customer_name' => 'Rahul Sharma',
                'table_no' => 'Table 2',
                'rating' => 5,
                'comment' => 'The Chicken Dum Biriyani was exceptionally flavorful and served piping hot! Excellent service by Chris.',
                'attendant' => 'Chris Thomas-226',
                'created_at' => now()->subMinutes(45),
            ],
            [
                'customer_name' => 'Pooja Verma',
                'table_no' => 'Table 5',
                'rating' => 5,
                'comment' => 'Great ambiance! Butter chicken and garlic naan is definitely a must-try. Fast service and clean tables.',
                'attendant' => 'Hannah Liam-229',
                'created_at' => now()->subHours(3),
            ],
            [
                'customer_name' => 'Amit Sen',
                'table_no' => 'Take Away',
                'rating' => 4,
                'comment' => 'Packaging was neat and the food stayed warm. Sweet mango lassi was very refreshing.',
                'attendant' => 'Laura Olivia-228',
                'created_at' => now()->subDay(),
            ],
            [
                'customer_name' => 'Sneha Mukherjee',
                'table_no' => 'Table 1',
                'rating' => 5,
                'comment' => 'Authentic Mughlai flavours and wonderful warm gulab jamuns. Five stars for food quality!',
                'attendant' => 'Laura Olivia-228',
                'created_at' => now()->subDays(2),
            ],
            [
                'customer_name' => 'Vikram Roy',
                'table_no' => 'Table 4',
                'rating' => 4,
                'comment' => 'Paneer Butter Masala was rich and creamy. Tandoori roti was crisp. Will visit again with family.',
                'attendant' => 'Chris Thomas-226',
                'created_at' => now()->subDays(3),
            ],
        ];

        foreach ($reviews as $r) {
            Review::create($r);
        }
    }
}
