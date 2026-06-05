<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('site_content', function (Blueprint $table){
            $table->id();
            $table->string('site_name')->nullable();
            $table->string('site_title')->nullable();
            $table->text('site_description')->nullable();
            $table->string('help_contact')->nullable();
            $table->string('follow_by')->nullable();
            $table->string('site_email')->nullable();
            $table->string('site_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
       
    }
};
