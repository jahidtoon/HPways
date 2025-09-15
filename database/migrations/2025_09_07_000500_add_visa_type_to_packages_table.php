<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('packages','visa_type')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->string('visa_type',40)->nullable()->after('id');
                // Optional index for filtering
                $table->index('visa_type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('packages','visa_type')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropIndex(['visa_type']);
                $table->dropColumn('visa_type');
            });
        }
    }
};
