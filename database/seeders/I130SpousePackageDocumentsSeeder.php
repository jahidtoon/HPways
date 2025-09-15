<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class I130SpousePackageDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('packages') || !Schema::hasTable('package_required_documents')) {
            return;
        }

        $visaType = 'I130'; // Spouse Abroad consular petition
        $packageIds = DB::table('packages')->where('visa_type',$visaType)->pluck('id')->all();
        if (empty($packageIds)) return;

        $canonical = collect([
            // Forms
            ['code'=>'FORM_G1145','label'=>'Form G-1145 (E-notification of Application)','required'=>1],
            ['code'=>'FORM_I130','label'=>'Form I-130 (Petition for Alien Relative)','required'=>1],
            ['code'=>'FORM_I130A','label'=>'Form I-130A (Supplementary Information for Spouse Beneficiary)','required'=>1],

            // Petitioner (USC/LPR)
            ['code'=>'PETITIONER_STATUS_PROOF','label'=>'Proof of U.S. citizenship OR copy of green card (front and back)','required'=>1],
            ['code'=>'PETITIONER_ID','label'=>'Government-issued photo ID (passport, driver’s license, etc.)','required'=>1],

            // Beneficiary (Spouse)
            ['code'=>'SPOUSE_BIRTH_CERT','label'=>'Spouse birth certificate (certified translation if not in English)','required'=>1],
            ['code'=>'SPOUSE_PASSPORT','label'=>'Spouse passport biographic page','required'=>1],

            // Marriage & relationship evidence
            ['code'=>'MARRIAGE_CERT','label'=>'Marriage certificate (Original or Certified Copy)','required'=>1],
            ['code'=>'PRIOR_MARRIAGE_TERMINATIONS','label'=>'Proof of termination of all prior marriages (divorce, annulment, or death certificate) — if applicable','required'=>0],
            ['code'=>'PHOTOS_TOGETHER','label'=>'Photos together over time with family/friends (labeled with dates/locations)','required'=>1],
            ['code'=>'CHILDREN_BIRTH_CERTS','label'=>'Birth certificates of children born to the marriage listing both spouses as parents — if any','required'=>0],

            // Secondary evidence (choose at least five — enforce via UI/validation)
            ['code'=>'ONGOING_RELATIONSHIP_PROOF','label'=>'Proof of ongoing relationship (shared accounts, remittances, etc.)','required'=>0],
            ['code'=>'WEDDING_INVITATION','label'=>'Wedding invitation or souvenir','required'=>0],
            ['code'=>'WEDDING_RINGS_RECEIPTS','label'=>'Wedding rings and/or venue booking receipts','required'=>0],
            ['code'=>'INSURANCE_EACH_OTHER','label'=>'Insurance policies naming each other (health/life beneficiary)','required'=>0],
            ['code'=>'AFFIDAVITS_MIN2','label'=>'Signed and notarized affidavits from family/friends (at least 2)','required'=>0],
            ['code'=>'TRAVEL_RECORDS','label'=>'Travel records (flights, boarding passes, hotel bookings)','required'=>0],
            ['code'=>'SOCIAL_MEDIA_EVIDENCE','label'=>'Social media evidence (posts, comments, tagged photos)','required'=>0],
            ['code'=>'CORRESPONDENCE_LOGS','label'=>'Correspondence (emails, chats, SMS, call logs)','required'=>0],

            // Additional
            ['code'=>'NAME_CHANGE_DOCS','label'=>'Marriage certificate, divorce decree, adoption decree, or court order for any name changes — if applicable','required'=>0],
        ])->map(fn($r)=> (object) $r);

        $now = now();
        $rows = [];
        foreach ($packageIds as $pid) {
            foreach ($canonical as $c) {
                $rows[] = [
                    'package_id' => $pid,
                    'code' => strtoupper($c->code),
                    'label' => $c->label,
                    'required' => (bool)($c->required ?? 0),
                    'translation_possible' => false,
                    'active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('package_required_documents')->upsert(
            $rows,
            ['package_id','code'],
            ['label','required','translation_possible','active','updated_at']
        );
    }
}
