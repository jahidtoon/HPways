<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class I485PackageDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('packages') || !Schema::hasTable('package_required_documents')) {
            return;
        }

        $visaType = 'I485';

        // Get all package IDs for this visa type
        $packageIds = DB::table('packages')
            ->where('visa_type', $visaType)
            ->pluck('id')
            ->all();

        if (empty($packageIds)) return;

        // Aggregate canonical doc set from any existing I-485 packages (Advanced/Premium already populated)
        $canonical = DB::table('package_required_documents as prd')
            ->join('packages as p', 'p.id', '=', 'prd.package_id')
            ->where('p.visa_type', $visaType)
            ->select([
                DB::raw('UPPER(prd.code) as code'),
                DB::raw('MIN(prd.label) as label'),
                DB::raw('MAX(CASE WHEN prd.required THEN 1 ELSE 0 END) as required'),
                DB::raw('MAX(CASE WHEN prd.translation_possible THEN 1 ELSE 0 END) as translation_possible'),
            ])
            ->groupBy(DB::raw('UPPER(prd.code)'))
            ->get();

        // Fallback: if no canonical found (fresh DB), use a curated list derived from product spec
        if ($canonical->isEmpty()) {
            $canonical = collect([
                ['code'=>'FORM_G1145','label'=>'Form G-1145 (E-notification of Application)','required'=>1,'translation_possible'=>0],
                ['code'=>'FORM_I130','label'=>'Form I-130 (Petition for Alien Relative)','required'=>1,'translation_possible'=>0],
                ['code'=>'FORM_I130A','label'=>'Form I-130A (Supplementary Information for Spouse Beneficiary)','required'=>1,'translation_possible'=>0],
                ['code'=>'FORM_I485','label'=>'Form I-485 (Application to Register Permanent Residence or Adjust Status)','required'=>1,'translation_possible'=>0],
                ['code'=>'FORM_I864','label'=>'Form I-864 (Affidavit of Support)','required'=>1,'translation_possible'=>0],
                ['code'=>'FORM_I864A','label'=>'Form I-864A (Household Member) OR another I-864 (Joint Sponsor)','required'=>0,'translation_possible'=>0],
                ['code'=>'FORM_I765','label'=>'Form I-765 (Employment Authorization) — if applying for EAD while I-485 pending','required'=>0,'translation_possible'=>0],
                ['code'=>'FORM_I131','label'=>'Form I-131 (Advance Parole) — if applying for travel while I-485 pending','required'=>0,'translation_possible'=>0],
                ['code'=>'PHOTO','label'=>'Passport-style photos','required'=>1,'translation_possible'=>0],
                ['code'=>'PETITIONER_STATUS_PROOF','label'=>'Proof of petitioner status: US citizenship OR green card (front and back)','required'=>1,'translation_possible'=>0],
                ['code'=>'PETITIONER_ID','label'=>'Government-issued photo ID (petitioner) — passport or driver’s license','required'=>1,'translation_possible'=>0],
                ['code'=>'BIRTH_CERT','label'=>'Birth certificate (beneficiary) — certified translation if not in English','required'=>1,'translation_possible'=>1],
                ['code'=>'PASSPORT','label'=>'Passport biographic page (beneficiary)','required'=>1,'translation_possible'=>0],
                ['code'=>'I94','label'=>'Form I-94 Arrival/Departure Record','required'=>1,'translation_possible'=>0],
                ['code'=>'PROOF_LAWFUL_ENTRY','label'=>'Proof of lawful entry (visa, admission stamp, or parole documents)','required'=>1,'translation_possible'=>0],
                ['code'=>'MARRIAGE_CERT','label'=>'Marriage certificate (Original or Certified Copy)','required'=>1,'translation_possible'=>1],
                ['code'=>'PRIOR_MARRIAGE_TERMINATION','label'=>'Termination of prior marriages (divorce/annulment/death certificate) — if applicable','required'=>0,'translation_possible'=>1],
                ['code'=>'REL_PHOTOS','label'=>'Photos together over time, with family/friends (label dates/locations)','required'=>1,'translation_possible'=>0],
                ['code'=>'CHILDREN_BIRTH_CERTS','label'=>'Birth certificates of children born to the marriage (if any)','required'=>0,'translation_possible'=>1],
                ['code'=>'JOINT_LEASE','label'=>'Joint lease/mortgage or property documents (both names)','required'=>0,'translation_possible'=>0],
                ['code'=>'JOINT_BANK','label'=>'Joint bank account statements','required'=>0,'translation_possible'=>0],
                ['code'=>'JOINT_TAX_RETURNS','label'=>'Joint tax returns (IRS transcripts or copies)','required'=>0,'translation_possible'=>0],
                ['code'=>'JOINT_LOANS_DEBTS','label'=>'Shared loans or debts (car/student/personal with both names)','required'=>0,'translation_possible'=>0],
                ['code'=>'WEDDING_INVITATION','label'=>'Wedding souvenir/invitation','required'=>0,'translation_possible'=>0],
                ['code'=>'WEDDING_RECEIPTS','label'=>'Wedding rings and/or venue booking receipts','required'=>0,'translation_possible'=>0],
                ['code'=>'INSURANCE_POLICIES','label'=>'Insurance policies naming each other (health/auto/life)','required'=>0,'translation_possible'=>0],
                ['code'=>'JOINT_UTILITIES','label'=>'Utility bills showing both names (gas/electric/water/internet)','required'=>0,'translation_possible'=>0],
                ['code'=>'AFFIDAVITS_RELATIONSHIP','label'=>'Signed and notarized affidavits from family/friends (at least 2)','required'=>0,'translation_possible'=>0],
                ['code'=>'TRAVEL_RECORDS','label'=>'Travel records showing joint travel (itineraries/boarding passes/hotels)','required'=>0,'translation_possible'=>0],
                ['code'=>'SOCIAL_MEDIA_EVIDENCE','label'=>'Social media evidence (posts/comments/tagged photos)','required'=>0,'translation_possible'=>0],
                ['code'=>'CORRESPONDENCE','label'=>'Correspondence (emails/chats/SMS/call records)','required'=>0,'translation_possible'=>0],
                ['code'=>'I864_TAX_PROOFS','label'=>'Most recent 3 years IRS tax return/transcript or Form 1040 with W-2s','required'=>1,'translation_possible'=>0],
                ['code'=>'I864_PAYSTUBS_OR_SELFEMP','label'=>'Employment verification letter and/or 6 months pay stubs OR proof of self-employment','required'=>1,'translation_possible'=>0],
                ['code'=>'I864_BANK_STATEMENTS','label'=>'Six months bank statements','required'=>0,'translation_possible'=>0],
                ['code'=>'I864_ASSETS','label'=>'Evidence of assets (if income is insufficient)','required'=>0,'translation_possible'=>0],
                ['code'=>'JS_STATUS_PROOF','label'=>'Joint sponsor status: US citizenship OR green card (front/back) — if applicable','required'=>0,'translation_possible'=>0],
                ['code'=>'JS_ID','label'=>'Joint sponsor government-issued photo ID — if applicable','required'=>0,'translation_possible'=>0],
                ['code'=>'JS_TAX_RETURNS','label'=>'Joint sponsor: most recent 3 years tax return or IRS transcripts — if applicable','required'=>0,'translation_possible'=>0],
                ['code'=>'JS_W2_1099','label'=>'Joint sponsor: W-2s and/or 1099s (most recent tax year) — if applicable','required'=>0,'translation_possible'=>0],
                ['code'=>'JS_PAYSTUBS_OR_SELFEMP','label'=>'Joint sponsor: employment letter and/or 6 months pay stubs OR proof of self-employment — if applicable','required'=>0,'translation_possible'=>0],
                ['code'=>'HM_STATUS_PROOF','label'=>'Household member status: US citizenship OR green card (front/back) — if applicable','required'=>0,'translation_possible'=>0],
                ['code'=>'HM_ID','label'=>'Household member government-issued photo ID — if applicable','required'=>0,'translation_possible'=>0],
                ['code'=>'HM_TAX_RETURNS','label'=>'Household member: most recent 3 years tax return or IRS transcripts — if applicable','required'=>0,'translation_possible'=>0],
                ['code'=>'HM_W2_1099','label'=>'Household member: W-2s and/or 1099s (most recent tax year) — if applicable','required'=>0,'translation_possible'=>0],
                ['code'=>'HM_PAYSTUBS_OR_SELFEMP','label'=>'Household member: employment letter and/or 6 months pay stubs OR proof of self-employment — if applicable','required'=>0,'translation_possible'=>0],
                ['code'=>'I693','label'=>'Form I-693 (Medical Exam) — provide if available or when requested','required'=>0,'translation_possible'=>0],
                ['code'=>'ADDITIONAL_DOCS_OPTIONAL','label'=>'Any other additional documents (optional)','required'=>0,'translation_possible'=>0],
            ])->map(function($r){ return (object)$r; });
        }

        $now = now();
        $rows = [];
        foreach ($packageIds as $pid) {
            foreach ($canonical as $c) {
                $rows[] = [
                    'package_id' => $pid,
                    'code' => strtoupper($c->code),
                    'label' => $c->label,
                    'required' => (bool)($c->required ?? 0),
                    'translation_possible' => (bool)($c->translation_possible ?? 0),
                    'active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
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
