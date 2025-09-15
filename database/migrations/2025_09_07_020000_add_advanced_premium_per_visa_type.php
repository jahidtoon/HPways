<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('packages')) return;
        // Visa types we already support
        $visaTypes = ['I485','I130','K1','I751','I90','DACA','N400'];
        // Map same as controller
        $map = [
            'I485' => 1,
            'I130' => 3,
            'K1'   => 5,
            'I751' => 6,
            'I90'  => 7,
            'DACA' => 8,
            'N400' => 9,
        ];
        $tiers = ['Advanced','Premium'];

        foreach ($visaTypes as $vt) {
            $catId = $map[$vt] ?? null;
            if (!$catId) continue;
            foreach ($tiers as $tier) {
                $exists = DB::table('packages')
                    ->where('visa_type',$vt)
                    ->where('visa_category_id',$catId)
                    ->where('name',$tier)
                    ->exists();
                if ($exists) continue;

                // Base template: visa_type NULL row for same category & tier
                $base = DB::table('packages')
                    ->whereNull('visa_type')
                    ->where('visa_category_id',$catId)
                    ->where('name',$tier)
                    ->first();
                if (!$base) continue; // nothing to clone

                DB::table('packages')->insert([
                    'visa_category_id' => $catId,
                    'name' => $tier,
                    'features' => $base->features,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'visa_type' => $vt,
                    'code' => $base->code, // shared code pattern
                    'price_cents' => $base->price_cents,
                    'active' => $base->active,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('packages')) return;
        DB::table('packages')->whereNotNull('visa_type')->whereIn('name',["Advanced","Premium"]) ->delete();
    }
};
