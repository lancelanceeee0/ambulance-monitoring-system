<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_logs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time');
            $table->string('plate_number');
            $table->string('driver_name');
            $table->string('base_location');
            $table->foreignId('dispatched_by_id')->constrained('users');
            $table->string('dispatched_by_name');
            $table->string('status')->default('On Mission');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_logs');
    }
};
