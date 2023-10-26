<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('created_at')->default(now());
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->unsignedSmallInteger('status')->nullable();
            $table->string('name', 500)->nullable();
            $table->double('gain_margin')->nullable();
            $table->string('project_name', 500)->nullable();
            $table->string('project_number', 500)->nullable();
            $table->string('project_location', 500)->nullable();
            $table->double('total_power_pick')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('budgets');
    }
}
