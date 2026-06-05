<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('services', function (Blueprint $table){
            $table->id();
            $table->string('title');
            $table->string('short_discription')->nullable();
            $table->string('image')->nullable();
            $table->string('status')->default(1);
            $table->timestamps();
       });
    }

    public function down(): void
    {
      
    }
};
