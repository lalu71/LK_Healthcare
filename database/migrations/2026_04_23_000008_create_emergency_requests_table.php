<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('contact_name');
            $table->string('contact_phone');
            $table->string('location');
            $table->string('latitude', 32)->nullable();
            $table->string('longitude', 32)->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending, dispatched, arrived, resolved, cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_requests');
    }
};
