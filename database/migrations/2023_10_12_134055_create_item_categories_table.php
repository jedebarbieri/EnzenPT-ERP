<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 500)->nullable();
            $table->dateTime('created_at')->default(now());
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('prefix', 45)->nullable();
            $table->unsignedInteger('parent_item_categories_id')->nullable();
            $table->foreign('parent_item_categories_id', 'fk_item_categories_parent_item')->references('id')->on('item_categories')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_categories');
    }
};
