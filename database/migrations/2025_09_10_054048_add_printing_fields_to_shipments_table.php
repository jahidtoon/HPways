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
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('recipient_name')->nullable();
            $table->string('recipient_address')->nullable();
            $table->string('recipient_city')->nullable();
            $table->string('recipient_state')->nullable();
            $table->string('recipient_zip')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->text('special_instructions')->nullable();
            $table->timestamp('prepared_at')->nullable();
            $table->foreignId('prepared_by')->nullable()->constrained('users');
            $table->foreignId('shipped_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn([
                'recipient_name',
                'recipient_address', 
                'recipient_city',
                'recipient_state',
                'recipient_zip',
                'recipient_phone',
                'special_instructions',
                'prepared_at',
                'prepared_by',
                'shipped_by'
            ]);
        });
    }
};
