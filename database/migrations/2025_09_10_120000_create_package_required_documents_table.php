<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('package_required_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id');
            $table->string('code', 50);      // e.g., PASSPORT, MARRIAGE_CERT
            $table->string('label');         // human label
            $table->boolean('required')->default(true);
            $table->boolean('translation_possible')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->unique(['package_id','code']);
            $table->index(['package_id','active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_required_documents');
    }
};
