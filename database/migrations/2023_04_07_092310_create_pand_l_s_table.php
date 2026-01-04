<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePandLSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pand_l_s', function (Blueprint $table) {
            $table->id();
            $table->decimal('openingstock', $precision = 18, $scale = 3);
            $table->decimal('closingstock', $precision = 18, $scale = 3);
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
        Schema::dropIfExists('pand_l_s');
    }
}
