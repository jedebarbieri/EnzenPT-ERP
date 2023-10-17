<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetOfQuantityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budgets_of_quantity', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('created_at')->default(new Expression('NOW()'));
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->unsignedInteger('item_id');
            $table->double('quantity')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->unsignedSmallInteger('status')->nullable();
            
            $table->foreign('item_id', 'fk_budgets_of_quantity_item')->references('id')->on('items')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('budget_of_quantity');
    }
}
