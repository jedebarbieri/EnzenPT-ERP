<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budget_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('budget_id');
            $table->double('unit_price')->nullable();
            $table->double('quantity')->nullable();
            $table->double('tax_percentage')->unsigned()->nullable();
            $table->double('discount')->nullable();
            $table->double('sell_price')->nullable();
            
            $table->foreign('budget_id', 'fk_budget_detail_budget')->references('id')->on('budgets')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('item_id', 'fk_budget_detail_item')->references('id')->on('items')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('budget_details');
    }
}
