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
        // For SQLite, we need to recreate the table to make shipment_id nullable
        // This is a workaround for SQLite limitations
        Schema::dropIfExists('tracking_events_backup');
        
        // Create backup table
        Schema::create('tracking_events_backup', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->string('location')->nullable();
            $table->string('status_class', 30)->nullable();
            $table->timestamp('event_time');
            $table->string('event_type')->nullable();
            $table->timestamp('event_date')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['shipment_id','event_time']);
        });
        
        // Copy data
        DB::statement('INSERT INTO tracking_events_backup (id, shipment_id, application_id, user_id, description, location, status_class, event_time, event_type, event_date, occurred_at, metadata, created_at, updated_at) SELECT id, shipment_id, application_id, user_id, description, location, status_class, event_time, event_type, event_date, occurred_at, metadata, created_at, updated_at FROM tracking_events');
        
        // Drop old table and rename backup
        Schema::drop('tracking_events');
        Schema::rename('tracking_events_backup', 'tracking_events');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a complex rollback for SQLite, so we'll skip it
        // In production, you should have proper backups
    }
};
