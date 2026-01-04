<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();

            // Link to parent transaction
            $table->foreignId('transaction_id')
                  ->constrained('journal_transactions')
                  ->onDelete('cascade');

            // Core fields (from original structure)
            $table->date('entry_date');
            $table->string('entity');
            $table->string('account');
            $table->string('description')->nullable();
            $table->string('paid_through');
            $table->string('reference');

            $table->decimal('debit', 15, 2)->default(0.00);
            $table->decimal('credit', 15, 2)->default(0.00);

            $table->string('branch')->nullable();
            $table->unsignedBigInteger('user_id');

            // Source tracking (new additions)
            $table->string('source_table')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();

            $table->timestamps();

            // Prevent duplicate journalization
            $table->unique(['source_table', 'source_id'], 'unique_source_entry');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_entries');
    }
}
