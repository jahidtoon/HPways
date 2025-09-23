<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                if (!Schema::hasColumn('applications', 'assigned_printer_id')) {
                    $table->foreignId('assigned_printer_id')->nullable()->after('attorney_id')->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('applications', 'printing_started_at')) {
                    $table->timestamp('printing_started_at')->nullable()->after('assigned_printer_id');
                }
                if (!Schema::hasColumn('applications', 'printed_at')) {
                    $table->timestamp('printed_at')->nullable()->after('printing_started_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                if (Schema::hasColumn('applications', 'assigned_printer_id')) {
                    $table->dropConstrainedForeignId('assigned_printer_id');
                }
                if (Schema::hasColumn('applications', 'printing_started_at')) {
                    $table->dropColumn('printing_started_at');
                }
                if (Schema::hasColumn('applications', 'printed_at')) {
                    $table->dropColumn('printed_at');
                }
            });
        }
    }
};
