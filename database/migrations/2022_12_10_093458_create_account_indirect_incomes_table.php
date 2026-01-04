<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountIndirectIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_indirect_incomes', function (Blueprint $table) {
            $table->id();
            $table->string('comment');
            $table->decimal('amount', $precision = 18, $scale = 3);
            $table->date('date');
            $table->string('branch');
            $table->integer('user_id');
            $table->timestamps();
            $table->longText('file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_indirect_incomes');
    }
}
