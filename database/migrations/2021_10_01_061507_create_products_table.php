<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name', length: 255);
            $table->string('productdetails', length: 255)->nullable();
            $table->string('unit', length: 255);
            $table->decimal('buy_cost', 18, 3);
            $table->decimal('rate', 18, 3)->nullable();
            $table->decimal('purchase_vat', 18, 3)->nullable();
            $table->decimal('inclusive_rate', 18, 3)->nullable();
            $table->decimal('inclusive_vat_amount', 18, 3)->nullable();
            $table->decimal('selling_cost', 18, 3);
            $table->decimal('vat', 18, 3);
            $table->integer('user_id');
            $table->string('branch', length: 255);
            $table->integer('category_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
