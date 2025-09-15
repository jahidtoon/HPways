<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('required_documents', function (Blueprint $table) {
            $table->id();
            $table->string('visa_type',40); // e.g. I130, I485 etc.
            $table->string('code',50);      // internal doc code e.g. PASSPORT, MARRIAGE_CERT
            $table->string('label');        // human label
            $table->boolean('required')->default(true);
            $table->boolean('translation_possible')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['visa_type','code']);
            $table->index(['visa_type','active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('required_documents');
    }
};
