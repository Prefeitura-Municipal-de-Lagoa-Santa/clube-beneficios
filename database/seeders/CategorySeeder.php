<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Alimentação', 'icon' => 'UtensilsCrossed', 'sort_order' => 1],
            ['name' => 'Saúde', 'icon' => 'Heart', 'sort_order' => 2],
            ['name' => 'Educação', 'icon' => 'GraduationCap', 'sort_order' => 3],
            ['name' => 'Lazer', 'icon' => 'Gamepad2', 'sort_order' => 4],
            ['name' => 'Beleza', 'icon' => 'Sparkles', 'sort_order' => 5],
            ['name' => 'Automotivo', 'icon' => 'Car', 'sort_order' => 6],
            ['name' => 'Serviços', 'icon' => 'Wrench', 'sort_order' => 7],
            ['name' => 'Varejo', 'icon' => 'ShoppingBag', 'sort_order' => 8],
        ];

        foreach ($categories as $data) {
            Category::firstOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['slug' => Str::slug($data['name'])])
            );
        }
    }
}
