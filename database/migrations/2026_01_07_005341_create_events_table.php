<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            // Primary key for events table
            $table->bigIncrements('event_id');
            // Reference to the user who created the event
            $table->unsignedBigInteger('user_id');
            // Reference to the chosen venue
            $table->unsignedBigInteger('place_id');
            // Reference to the selected food package
            $table->unsignedBigInteger('food_id');
            // Reference to the selected design theme
            $table->unsignedBigInteger('design_id');
            // Name of the event (e.g. "Birthday Party")
            $table->string('event_name');
            // Date of the event
            $table->date('event_date');
            // Time of the event
            $table->time('event_time');
            // Number of guests attending
            $table->integer('number_of_guests');
            // Additional requests from the user
            $table->text('special_requests')->nullable();
            // Total calculated price for the event
            $table->decimal('total_price', 10, 2)->default(0);
            // Event status (pending, approved, rejected, etc.)
            $table->string('status')->default('pending');
            // Timestamps: created_at and updated_at; created_at is required by our models
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            // Index on date/time/place for efficient availability checks
            $table->index(['event_date', 'event_time', 'place_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}