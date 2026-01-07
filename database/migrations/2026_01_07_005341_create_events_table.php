<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Index for faster availability queries
            $table->index(['location', 'start_time', 'end_time']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}