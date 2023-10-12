<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budget', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->dateTime('created_at')->default(new Expression('NOW()'));
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->unsignedInteger('item_id');
            $table->double('quantity')->nullable();
            $table->unsignedSmallInteger('status')->nullable();
            
            $table->foreign('item_id', 'fk_budget_item1')->references('id')->on('item')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('budget');
    }
}
