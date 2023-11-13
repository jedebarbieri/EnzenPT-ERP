<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {       
        // The initial seeders are always those...
        \App\Models\User::factory()->create([
            'name' => 'Justo Debarbieri',
            'email' => 'justo.debarbieri@enzen.com',
            'password' => 'Enzen2023',
        ]);

        $this->call(ItemCategoriesSeeder::class);
        $this->call(ItemSeeder::class);

        // From here, the seeders are only for development or testing
        if (app()->environment() !== 'production') {
            $this->call(BudgetSeeder::class);
        }


    }
}
