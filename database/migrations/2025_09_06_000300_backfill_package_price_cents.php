<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('packages')) return;
        // If price_cents column exists and legacy price decimal column exists, backfill.
        $hasPriceCents = Schema::hasColumn('packages','price_cents');
        $hasPrice = Schema::hasColumn('packages','price');
        if ($hasPriceCents && $hasPrice) {
            DB::table('packages')->whereNull('price_cents')->orWhere('price_cents',0)->orderBy('id')->chunkById(200, function($rows){
                foreach ($rows as $row) {
                    $decimal = $row->price ?? 0;
                    $cents = (int) round($decimal * 100);
                    DB::table('packages')->where('id',$row->id)->update(['price_cents'=>$cents]);
                }
            });
        }
    }

    public function down(): void
    {
        // No rollback for data migration
    }
};
