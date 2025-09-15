<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                if (!Schema::hasColumn('applications','user_id')) return; // safety
                $table->index(['user_id','visa_type','status'],'apps_user_visa_status_idx');
            });
        }
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments','application_id')) return;
                $table->index(['application_id','status'],'payments_app_status_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->dropIndex('apps_user_visa_status_idx');
            });
        }
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropIndex('payments_app_status_idx');
            });
        }
    }
};
