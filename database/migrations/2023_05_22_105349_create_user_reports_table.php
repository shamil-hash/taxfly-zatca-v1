<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->decimal('opening_balane', $precision = 18, $scale = 3);
            $table->integer('total_sales');
            $table->decimal('total_sales_amount', $precision = 18, $scale = 3);
            $table->decimal('total_amount', $precision = 18, $scale = 3);
            $table->decimal('total_cash', $precision = 18, $scale = 3);
            $table->integer('branch');
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
        Schema::dropIfExists('user_reports');
    }
}
