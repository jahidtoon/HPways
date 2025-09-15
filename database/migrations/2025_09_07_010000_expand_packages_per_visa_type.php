<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('packages')) return;
        $visaTypes = config('visas.types', []);
        if (empty($visaTypes)) return;
    $basePackages = DB::table('packages')->whereNull('visa_type')->get();
        foreach ($visaTypes as $vt) {
            foreach ($basePackages as $bp) {
                $exists = DB::table('packages')->where('visa_type',$vt)->where('code',$bp->code)->exists();
                if ($exists) continue;
                DB::table('packages')->insert([
                    'visa_category_id' => $bp->visa_category_id, // preserve linkage (may be null)
                    'visa_type' => $vt,
                    'code' => $bp->code,
                    'name' => $bp->name,
                    'price_cents' => $bp->price_cents,
                    'features' => $bp->features,
                    'active' => $bp->active,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('packages')) return;
        $visaTypes = config('visas.types', []);
        if (empty($visaTypes)) return;
        DB::table('packages')->whereIn('visa_type',$visaTypes)->delete();
    }
};
