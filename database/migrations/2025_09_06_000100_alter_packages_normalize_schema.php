<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('packages')) {
            return; // created by earlier migration path; skip if missing
        }
        Schema::table('packages', function (Blueprint $table) {
            // Add new normalized columns if not present
            if (!Schema::hasColumn('packages','visa_type')) {
                $table->string('visa_type',40)->nullable()->after('id');
            }
            if (!Schema::hasColumn('packages','code')) {
                $table->string('code',30)->after('visa_type')->default('basic');
            }
            if (!Schema::hasColumn('packages','price_cents')) {
                $table->unsignedInteger('price_cents')->after('name')->default(0);
            }
            if (!Schema::hasColumn('packages','active')) {
                $table->boolean('active')->default(true);
            }
            // If legacy decimal price exists and price_cents just added, migrate data later in a DB pass.
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // We won't drop columns to avoid data loss; reversible migration kept simple.
        });
    }
};
