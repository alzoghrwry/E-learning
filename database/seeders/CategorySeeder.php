<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Programming','Design','Business','AI','Marketing'];

        foreach ($categories as $cat) {
            Category::factory()->create(['name' => $cat]);
        }
    }
}
