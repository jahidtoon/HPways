<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PackageRequiredDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('packages') || !Schema::hasTable('package_required_documents')) {
            return;
        }

        $map = config('required_documents', []);
        if (empty($map)) return;

        $now = now();

        // Get all active packages grouped by visa_type
        $packages = DB::table('packages')->where('active',1)->get();
        foreach ($packages as $pkg) {
            $visa = strtoupper($pkg->visa_type ?? '');
            if (!$visa || empty($map[$visa])) continue;

            $rows = [];
            foreach ($map[$visa] as $item) {
                $code = strtoupper($item['code'] ?? '');
                if (!$code) continue;
                $rows[] = [
                    'package_id' => $pkg->id,
                    'code' => $code,
                    'label' => $item['label'] ?? $code,
                    'required' => (bool)($item['required'] ?? false),
                    'translation_possible' => (bool)($item['translation_possible'] ?? false),
                    'active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($rows)) {
                DB::table('package_required_documents')->upsert(
                    $rows,
                    ['package_id','code'],
                    ['label','required','translation_possible','active','updated_at']
                );
            }
        }
    }
}
