<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuyproductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buyproducts', function (Blueprint $table) {
            $table->charset('utf8mb4');
            $table->collation('	utf8mb4_general_ci');

            $table->id();
            $table->string('product_name', length: 255);
            $table->unsignedBigInteger('product_id');
            $table->decimal('quantity', 18, 3);
            $table->decimal('remain_quantity', 18, 3);
            $table->string('unit', length: 255);
            $table->decimal('one_pro_buycost', 18, 3)->nullable();
            $table->decimal('one_pro_buycost_rate', 18, 3)->nullable();
            $table->decimal('inclusive_rate', 18, 3)->nullable();
            $table->decimal('exclusive_rate', 18, 3)->nullable();
            $table->decimal('mrp', 18, 3);
            $table->decimal('price', 18, 3);
            $table->decimal('price_wo_discount', 18, 3)->nullable();
            $table->decimal('vat_amount', 18, 3);
            $table->integer('fixed_vat');
            $table->string('branch', length: 255);
            $table->string('transaction_id', length: 255);
            $table->string('customer_name', length: 255)->nullable();
            $table->string('trn_number', length: 255)->nullable();
            $table->string('phone', length: 255)->nullable();
            $table->integer('payment_type')->default(1);
            $table->integer('user_id');
            $table->string('email', length: 255)->nullable();
            $table->decimal('netrate', 18, 3)->nullable();
            $table->decimal('total_amount', 18, 3)->nullable();
            $table->decimal('totalamount_wo_discount', 18, 3)->nullable();
            $table->string('discount_type', length: 255)->nullable()->comment('none, percentage, amount');
            $table->decimal('discount', 18, 3)->nullable();
            $table->decimal('discount_amount', 18, 3)->nullable();
            $table->decimal('buycostadd', 18, 3)->nullable();
            $table->decimal('buycost_rate_add', 18, 3)->nullable();
            $table->integer('credit_user_id')->nullable();
            $table->integer('cash_user_id')->nullable();
            $table->integer('vat_type')->nullable()->comment('1-inclusive, 2- exclusive');
            $table->integer('total_discount_type')->nullable()->comment('0 - none, 1- percentage, 2 -amount');
            $table->decimal('total_discount_percent', 18, 3)->nullable();
            $table->decimal('total_discount_amount', 18, 3)->nullable();
            $table->decimal('bill_grand_total', 18, 3)->nullable();
            $table->decimal('bill_grand_total_wo_discount', 18, 3)->nullable();
            $table->integer('edit')->nullable()->comment('1- edited');
            $table->string('edit_comment', length: 255)->nullable();
            $table->integer('to_invoice')->nullable()->comment('1- sales order, 2-quotation');
            $table->string('sales_order_trans_ID', length: 255)->nullable();

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
        Schema::dropIfExists('buyproducts');
    }
}
