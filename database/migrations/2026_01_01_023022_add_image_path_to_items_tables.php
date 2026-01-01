<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // event_places
        if (Schema::hasTable('event_places') && !Schema::hasColumn('event_places', 'image_path')) {
            Schema::table('event_places', function (Blueprint $table) {
                $table->string('image_path')->nullable();
            });
        }

        // food_items
        if (Schema::hasTable('food_items') && !Schema::hasColumn('food_items', 'image_path')) {
            Schema::table('food_items', function (Blueprint $table) {
                $table->string('image_path')->nullable();
            });
        }

        // event_designs
        if (Schema::hasTable('event_designs') && !Schema::hasColumn('event_designs', 'image_path')) {
            Schema::table('event_designs', function (Blueprint $table) {
                $table->string('image_path')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('event_places') && Schema::hasColumn('event_places', 'image_path')) {
            Schema::table('event_places', function (Blueprint $table) {
                $table->dropColumn('image_path');
            });
        }

        if (Schema::hasTable('food_items') && Schema::hasColumn('food_items', 'image_path')) {
            Schema::table('food_items', function (Blueprint $table) {
                $table->dropColumn('image_path');
            });
        }

        if (Schema::hasTable('event_designs') && Schema::hasColumn('event_designs', 'image_path')) {
            Schema::table('event_designs', function (Blueprint $table) {
                $table->dropColumn('image_path');
            });
        }
    }
};