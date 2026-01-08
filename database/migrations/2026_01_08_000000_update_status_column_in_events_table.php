<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adjust the length and type of the events.status column.
 *
 * Earlier versions of this application defined the status column as an
 * ENUM or a very short VARCHAR, which caused MySQL to truncate values
 * like "unpaid" or "awaiting_payment". This migration alters the
 * column to a regular string column with a reasonable length and a
 * sensible default of "pending". Running this migration will ensure
 * that future inserts for statuses (pending, approved, rejected,
 * completed) do not trigger data truncation errors.
 */
class UpdateStatusColumnInEventsTable extends Migration
{
    public function up()
    {
        // Modify the status column to a string with length 20 and default value
        Schema::table('events', function (Blueprint $table) {
            // The `change()` method allows modifying existing columns. We set
            // the length to 20 characters to accommodate future status values.
            $table->string('status', 20)->default('pending')->change();
        });
    }

    public function down()
    {
        // Revert the status column back to its original definition. For
        // compatibility with legacy systems we assume a simple VARCHAR of
        // length 20 and default 'pending'. If you previously used an ENUM
        // or different type, adjust this accordingly.
        Schema::table('events', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->change();
        });
    }
}