<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSourceColumnsToJournalEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('journal_entries', 'source_table')) {
                $table->string('source_table')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('journal_entries', 'source_id')) {
                $table->unsignedBigInteger('source_id')->nullable()->after('source_table');
            }

            // ✅ Add unique index to prevent duplicates
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
        Schema::table('journal_entries', function (Blueprint $table) {
            // Drop the unique index
            $table->dropUnique('unique_source_entry');

            // Drop the columns if they exist
            if (Schema::hasColumn('journal_entries', 'source_table')) {
                $table->dropColumn('source_table');
            }
            if (Schema::hasColumn('journal_entries', 'source_id')) {
                $table->dropColumn('source_id');
            }
        });
    }
}
