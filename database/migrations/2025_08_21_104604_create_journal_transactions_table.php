<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('journal_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->string('reference')->nullable();
            $table->text('narration')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('journal_transactions');
    }
}
