<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('visa_type', 40); // e.g. I130, I90, K1, AOS, I751, DACA, N400
            $table->string('status', 40)->default('draft');
            $table->unsignedTinyInteger('progress_pct')->default(0);
            $table->string('payment_status', 30)->default('unpaid');
            $table->timestamp('submitted_at')->nullable();
            $table->json('missing_documents')->nullable();
            $table->foreignId('case_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('attorney_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['visa_type','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
