<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('payable_type');    // App\Models\Appointment | LabBooking | PharmacyOrder
            $table->unsignedBigInteger('payable_id');
            $table->string('receipt_no')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('method')->default('bypass'); // bypass, upi, card, cash
            $table->string('status')->default('success'); // success, pending, failed
            $table->string('transaction_ref')->nullable();
            $table->timestamps();

            $table->index(['payable_type', 'payable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
