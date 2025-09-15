<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('provider', 30)->default('stripe');
            $table->string('provider_ref')->nullable(); // intent id
            $table->unsignedInteger('amount_cents');
            $table->string('currency', 10)->default('usd');
            $table->string('status', 30)->default('pending'); // pending|succeeded|failed|refunded
            $table->json('payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index(['status','provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
