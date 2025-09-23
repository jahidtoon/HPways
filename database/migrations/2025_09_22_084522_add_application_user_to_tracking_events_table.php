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
        Schema::table('tracking_events', function (Blueprint $table) {
            $table->foreignId('application_id')->nullable()->after('shipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->after('application_id')->constrained()->cascadeOnDelete();
            $table->timestamp('occurred_at')->nullable()->after('event_date');
            $table->json('metadata')->nullable()->after('occurred_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tracking_events', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['application_id', 'user_id', 'occurred_at', 'metadata']);
        });
    }
};
