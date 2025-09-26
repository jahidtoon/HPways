<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'last_tracking_update')) {
                $table->timestamp('last_tracking_update')->nullable()->after('delivered_at');
            }
            if (!Schema::hasColumn('shipments', 'actual_carrier')) {
                $table->string('actual_carrier', 60)->nullable()->after('carrier');
            }
            if (!Schema::hasColumn('shipments', 'actual_service')) {
                $table->string('actual_service', 80)->nullable()->after('service');
            }
            if (!Schema::hasColumn('shipments', 'shipping_notes')) {
                $table->text('shipping_notes')->nullable()->after('special_instructions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'last_tracking_update')) {
                $table->dropColumn('last_tracking_update');
            }
            if (Schema::hasColumn('shipments', 'actual_carrier')) {
                $table->dropColumn('actual_carrier');
            }
            if (Schema::hasColumn('shipments', 'actual_service')) {
                $table->dropColumn('actual_service');
            }
            if (Schema::hasColumn('shipments', 'shipping_notes')) {
                $table->dropColumn('shipping_notes');
            }
        });
    }
};
