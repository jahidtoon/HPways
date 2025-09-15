<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('type', 60);
            $table->string('original_name');
            $table->string('stored_path');
            $table->unsignedBigInteger('size_bytes');
            $table->string('mime', 80)->nullable();
            $table->string('status', 30)->default('pending'); // pending|approved|rejected
            $table->boolean('needs_translation')->default(false);
            $table->string('translation_status', 30)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['application_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
