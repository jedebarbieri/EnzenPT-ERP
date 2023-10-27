<?php

namespace Database\Seeders;

use App\Models\Projects\Budget;
use App\Models\Projects\BudgetDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class BudgetSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        BudgetDetail::truncate();
        Budget::truncate();
        Schema::enableForeignKeyConstraints();

        Budget::factory(25)->create();
    }
}
