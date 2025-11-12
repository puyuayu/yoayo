<?php

namespace Database\Seeders;

use App\Models\categories;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class kategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Cake',
                'description' => 'Berbagai jenis kue ulang tahun, cake tart, dan kue bolu'
            ],
            [
                'name' => 'Cookies',
                'description' => 'Kue kering, cookies, dan biskuit'
            ],
            [
                'name' => 'Pastry',
                'description' => 'Roti manis, pastry, dan danish'
            ],
            [
                'name' => 'Pudding',
                'description' => 'Puding, jelly, dan dessert dingin'
            ],
        ];

        foreach ($categories as $category) {
            categories::create($category);
        }
    }
}
