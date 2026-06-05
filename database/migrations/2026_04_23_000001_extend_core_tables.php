<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('address')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('address');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->string('gender', 10)->nullable()->after('dob');
            $table->text('medical_history')->nullable()->after('allergies');
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('is_active');
            $table->string('qualification')->nullable()->after('bio');
            $table->string('clinic_address')->nullable()->after('qualification');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('appointment_date');
            $table->text('doctor_notes')->nullable()->after('reason');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'address', 'avatar']);
        });
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['gender', 'medical_history']);
        });
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['bio', 'qualification', 'clinic_address']);
        });
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['reason', 'doctor_notes']);
        });
    }
};
