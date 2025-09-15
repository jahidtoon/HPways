<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('visa_type', 40)->nullable(); // null = global
            $table->string('code', 30); // basic, advanced, premium
            $table->string('name');
            $table->unsignedInteger('price_cents');
            $table->json('features')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['visa_type','code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
