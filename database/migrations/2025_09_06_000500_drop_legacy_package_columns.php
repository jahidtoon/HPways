<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if(!Schema::hasTable('packages')) return;
        $driver = Schema::getConnection()->getDriverName();
        // SQLite simple no-op (can't safely drop columns + FKs without table rebuild)
        if ($driver === 'sqlite') {
            return; // skip safely
        }
        Schema::table('packages', function(Blueprint $table){
            if(Schema::hasColumn('packages','price')) {
                $table->dropColumn('price');
            }
            if(Schema::hasColumn('packages','description')) {
                $table->dropColumn('description');
            }
            if(Schema::hasColumn('packages','visa_category_id')) {
                try { $table->dropForeign(['visa_category_id']); } catch (\Throwable $e) {}
                $table->dropColumn('visa_category_id');
            }
        });
    }

    public function down(): void
    {
        // Minimal rollback: restore columns (without data)
        if(!Schema::hasTable('packages')) return;
        // Down still no-op for SQLite safety
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'sqlite') return;
        Schema::table('packages', function(Blueprint $table){
            if(!Schema::hasColumn('packages','visa_category_id')) {
                $table->foreignId('visa_category_id')->nullable()->constrained('visa_categories')->nullOnDelete();
            }
            if(!Schema::hasColumn('packages','price')) {
                $table->decimal('price',10,2)->nullable();
            }
            if(!Schema::hasColumn('packages','description')) {
                $table->text('description')->nullable();
            }
        });
    }
};
