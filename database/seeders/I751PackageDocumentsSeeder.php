<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class I751PackageDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch all I-751 packages
        $packages = DB::table('packages')->where('visa_type','I751')->pluck('id');
        if ($packages->isEmpty()) { return; }

        $rows = [
            // Forms
            ['code'=>'FORM_G1145','label'=>'Form G-1145 (E-notification of Application)','required'=>true,'translation_possible'=>false,'active'=>true],
            ['code'=>'FORM_I751','label'=>'Form I-751 (Petition to Remove Conditions on Residence) – joint filing','required'=>true,'translation_possible'=>false,'active'=>true],

            // Proof of Conditional Green Card
            ['code'=>'CONDITIONAL_GC','label'=>'Copy of front and back of conditional green card','required'=>true,'translation_possible'=>false,'active'=>true],
            ['code'=>'GOVT_ID','label'=>'Government-issued photo ID (passport, driver’s license, etc.)','required'=>true,'translation_possible'=>false,'active'=>true],

            // Marriage & Relationship Evidence
            ['code'=>'MARRIAGE_CERT','label'=>'Marriage certificate (Original or Certified Copy)','required'=>true,'translation_possible'=>false,'active'=>true],
            ['code'=>'PRIOR_MARRIAGE_TERMINATION','label'=>'Termination of prior marriages (divorce/annulment/death certificate) — if applicable','required'=>false,'translation_possible'=>true,'active'=>true],
            ['code'=>'REL_PHOTOS','label'=>'Photos together (family events, holidays, trips) with captions/descriptions','required'=>true,'translation_possible'=>false,'active'=>true],

            // Choose at least 8 from the following (schema can’t enforce the count; UI will guide)
            ['code'=>'CHILDREN_BIRTH_CERTS','label'=>'Birth certificates of children born to the marriage (if any)','required'=>false,'translation_possible'=>true,'active'=>true],
            ['code'=>'JOINT_LEASE','label'=>'Joint lease/mortgage or property documents in both names','required'=>false,'translation_possible'=>false,'active'=>true],
            ['code'=>'SSA_MVA_NAME_CHANGE','label'=>'Proof of name change with Social Security and/or MVA','required'=>false,'translation_possible'=>false,'active'=>true],
            ['code'=>'JOINT_BANK','label'=>'Joint bank account statements','required'=>false,'translation_possible'=>false,'active'=>true],
            ['code'=>'JOINT_TAX_RETURNS','label'=>'Joint federal and state tax returns (or IRS transcripts)','required'=>false,'translation_possible'=>false,'active'=>true],
            ['code'=>'JOINT_LOANS_CREDIT','label'=>'Joint loan and/or credit card statements','required'=>false,'translation_possible'=>false,'active'=>true],
            ['code'=>'INSURANCE_POLICIES','label'=>'Insurance policies naming each other','required'=>false,'translation_possible'=>false,'active'=>true],
            ['code'=>'JOINT_UTILITIES','label'=>'Utility bills showing both names','required'=>false,'translation_possible'=>false,'active'=>true],
            ['code'=>'AFFIDAVITS_RELATIONSHIP','label'=>'Signed and notarized affidavits from family/friends (at least 2)','required'=>false,'translation_possible'=>false,'active'=>true],
            ['code'=>'TRAVEL_RECORDS','label'=>'Travel records (airline tickets, hotel reservations, passport stamps)','required'=>false,'translation_possible'=>false,'active'=>true],
            ['code'=>'INSURANCE_BENEFICIARY','label'=>'Health, auto, or life insurance listing spouse as beneficiary/dependent','required'=>false,'translation_possible'=>false,'active'=>true],
            ['code'=>'ADOPTION_RECORDS','label'=>'Adoption records of children (if applicable)','required'=>false,'translation_possible'=>false,'active'=>true],
            ['code'=>'VEHICLE_REGISTRATIONS','label'=>'Vehicle registrations in both names','required'=>false,'translation_possible'=>false,'active'=>true],

            // Optional catch-all
            ['code'=>'ADDITIONAL_DOCS_OPTIONAL','label'=>'Any other additional documents (optional)','required'=>false,'translation_possible'=>false,'active'=>true],
        ];

        foreach ($packages as $pid) {
            foreach ($rows as $r) {
                // upsert by (package_id, code)
                $existing = DB::table('package_required_documents')->where('package_id',$pid)->where('code',$r['code'])->first();
                if ($existing) {
                    DB::table('package_required_documents')->where('id',$existing->id)->update([
                        'label' => $r['label'],
                        'required' => $r['required'],
                        'translation_possible' => $r['translation_possible'],
                        'active' => $r['active'],
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('package_required_documents')->insert([
                        'package_id' => $pid,
                        'code' => $r['code'],
                        'label' => $r['label'],
                        'required' => $r['required'],
                        'translation_possible' => $r['translation_possible'],
                        'active' => $r['active'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
