<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blood_inventory', function (Blueprint $table) {
            $table->id();
            $table->string('blood_group', 5)->unique();
            $table->unsignedInteger('units')->default(0);
            $table->timestamps();
        });

        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('patient_name');
            $table->string('blood_group', 5);
            $table->unsignedInteger('units');
            $table->string('hospital')->nullable();
            $table->string('contact_phone');
            $table->date('needed_by')->nullable();
            $table->text('reason')->nullable();
            $table->string('status')->default('pending'); // pending, fulfilled, cancelled
            $table->timestamps();
        });

        Schema::create('blood_donors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('blood_group', 5);
            $table->string('phone');
            $table->string('city')->nullable();
            $table->date('last_donated_at')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_donors');
        Schema::dropIfExists('blood_requests');
        Schema::dropIfExists('blood_inventory');
    }
};
