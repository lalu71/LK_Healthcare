<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('manufacturer')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->string('unit')->default('strip'); // strip, bottle, tube
            $table->boolean('requires_prescription')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('pharmacy_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('order_code')->unique();
            $table->string('status')->default('placed'); // placed, packed, shipped, delivered, cancelled
            $table->string('payment_status')->default('unpaid');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('delivery_address');
            $table->string('delivery_phone');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pharmacy_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacy_order_items');
        Schema::dropIfExists('pharmacy_orders');
        Schema::dropIfExists('medicines');
    }
};
