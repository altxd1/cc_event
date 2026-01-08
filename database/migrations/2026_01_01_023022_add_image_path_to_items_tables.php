<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // event_places
        if (Schema::hasTable('event_places') && !Schema::hasColumn('event_places', 'image_url')) {
            Schema::table('event_places', function (Blueprint $table) {
                // Store the URL for the place image; nullable to allow no image
                $table->string('image_url')->nullable();
            });
        }

        // food_items
        if (Schema::hasTable('food_items') && !Schema::hasColumn('food_items', 'image_url')) {
            Schema::table('food_items', function (Blueprint $table) {
                $table->string('image_url')->nullable();
            });
        }

        // event_designs
        if (Schema::hasTable('event_designs') && !Schema::hasColumn('event_designs', 'image_url')) {
            Schema::table('event_designs', function (Blueprint $table) {
                $table->string('image_url')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('event_places') && Schema::hasColumn('event_places', 'image_url')) {
            Schema::table('event_places', function (Blueprint $table) {
                $table->dropColumn('image_url');
            });
        }

        if (Schema::hasTable('food_items') && Schema::hasColumn('food_items', 'image_url')) {
            Schema::table('food_items', function (Blueprint $table) {
                $table->dropColumn('image_url');
            });
        }

        if (Schema::hasTable('event_designs') && Schema::hasColumn('event_designs', 'image_url')) {
            Schema::table('event_designs', function (Blueprint $table) {
                $table->dropColumn('image_url');
            });
        }
    }
};