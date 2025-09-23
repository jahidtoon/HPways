<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attorney_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('applicant_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('case_manager_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('status', 32)->default('requested'); // requested, scheduled, approved, declined, canceled, completed
            $table->string('requested_by', 32)->nullable(); // 'attorney' | 'applicant' | 'case_manager' | 'admin'
            $table->string('topic')->nullable();
            $table->text('notes')->nullable();

            $table->dateTime('scheduled_for')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();

            $table->string('provider', 64)->default('zoom');
            $table->string('join_url', 2048)->nullable();
            $table->string('start_url', 2048)->nullable();
            $table->string('provider_meeting_id', 191)->nullable();

            $table->timestamps();

            $table->index(['application_id']);
            $table->index(['status']);
            $table->index(['scheduled_for']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
