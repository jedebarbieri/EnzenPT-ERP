<?php

namespace Database\Seeders;

use App\Models\Projects\Budget;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class BudgetSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Budget::truncate();
        Schema::enableForeignKeyConstraints();

        Budget::factory(60)->create();
    }
}
