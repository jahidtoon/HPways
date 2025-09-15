<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class I485ChildPackageDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('packages') || !Schema::hasTable('package_required_documents')) {
            return;
        }

        $visaType = 'I485_CHILD';
        $packageIds = DB::table('packages')->where('visa_type',$visaType)->pluck('id')->all();
        if (empty($packageIds)) return;

        $canonical = collect([
            // Forms
            ['code'=>'FORM_G1145','label'=>'Form G-1145 (E-notification of Application)','required'=>1],
            ['code'=>'FORM_I130','label'=>'Form I-130 (Petition for Alien Relative)','required'=>1],
            ['code'=>'FORM_I485','label'=>'Form I-485 (Application to Register Permanent Residence or Adjust Status)','required'=>1],
            ['code'=>'FORM_I864','label'=>'Form I-864 (Affidavit of Support)','required'=>1],
            ['code'=>'FORM_I864A','label'=>'Form I-864A (Household Member) OR another I-864 (Joint Sponsor)','required'=>0],
            ['code'=>'FORM_I765','label'=>'Form I-765 (Employment Authorization) — if applying for EAD while I-485 pending','required'=>0],
            ['code'=>'FORM_I131','label'=>'Form I-131 (Advance Parole) — if applying for travel while I-485 pending','required'=>0],

            // Petitioner (USC/LPR)
            ['code'=>'PETITIONER_STATUS_PROOF','label'=>'Petitioner status: US citizenship (birth cert/passport/naturalization) OR green card (front/back)','required'=>1],
            ['code'=>'PETITIONER_ID','label'=>'Government-issued photo ID (petitioner)','required'=>1],

            // Relationship Proof
            ['code'=>'REL_PRIMARY_BIRTH_CERT','label'=>'Child’s birth certificate showing sponsoring parent’s name','required'=>1],
            ['code'=>'REL_ADOPTION','label'=>'If adopted: final adoption decree + proof of legal custody/residency before age 16','required'=>0],
            ['code'=>'REL_STEPCHILD','label'=>'If stepchild: marriage certificate between parent and stepparent + prior marriage terminations','required'=>0],
            ['code'=>'REL_DNA','label'=>'DNA test result from USCIS-approved lab (if needed)','required'=>0],

            // Secondary Relationship Proof (choose at least five — enforce via UI/validation)
            ['code'=>'SEC_DNA_TEST','label'=>'DNA test result from USCIS-approved lab','required'=>0],
            ['code'=>'SEC_MEDICAL_RECORDS','label'=>'Medical/health records showing both names','required'=>0],
            ['code'=>'SEC_CHURCH_RECORDS','label'=>'Church/religious records listing both names','required'=>0],
            ['code'=>'SEC_INSURANCE','label'=>'Insurance records naming both petitioner and beneficiary','required'=>0],
            ['code'=>'SEC_EMPLOYMENT_RECORDS','label'=>'Employment records showing both names','required'=>0],
            ['code'=>'SEC_TAX_FINANCIAL','label'=>'Financial records (tax returns) listing both names','required'=>0],
            ['code'=>'SEC_CENSUS_TRIBAL','label'=>'Census or tribal records in both names','required'=>0],
            ['code'=>'SEC_GOVT_IDS','label'=>'Government records/IDs showing both names','required'=>0],

            // Child identity & immigration
            ['code'=>'CHILD_BIRTH_CERT','label'=>'Child’s birth certificate (certified English translation if not in English)','required'=>1],
            ['code'=>'CHILD_PASSPORT','label'=>'Child’s passport biographic page','required'=>1],
            ['code'=>'I94','label'=>'Form I-94 Arrival/Departure Record','required'=>1],
            ['code'=>'PROOF_LAWFUL_ENTRY','label'=>'Proof of lawful entry (visa, admission stamp, or parole documents)','required'=>1],

            // Financial (I-864)
            ['code'=>'I864_TAX_PROOFS','label'=>'Most recent 3 years IRS tax return/transcript or Form 1040 with W-2s','required'=>1],
            ['code'=>'I864_PAYSTUBS_OR_SELFEMP','label'=>'Employment verification letter and/or 6 months pay stubs OR proof of self-employment','required'=>1],
            ['code'=>'I864_BANK_STATEMENTS','label'=>'Six months bank statements','required'=>0],
            ['code'=>'I864_ASSETS','label'=>'Evidence of assets (if income is insufficient)','required'=>0],

            // Joint Sponsor (if applicable)
            ['code'=>'JS_STATUS_PROOF','label'=>'Joint sponsor status: US citizenship OR green card (front/back) — if applicable','required'=>0],
            ['code'=>'JS_ID','label'=>'Joint sponsor government-issued photo ID — if applicable','required'=>0],
            ['code'=>'JS_TAX_RETURNS','label'=>'Joint sponsor: most recent 3 years tax return or IRS transcripts — if applicable','required'=>0],
            ['code'=>'JS_W2_1099','label'=>'Joint sponsor: W-2s and/or 1099s (most recent tax year) — if applicable','required'=>0],
            ['code'=>'JS_PAYSTUBS_OR_SELFEMP','label'=>'Joint sponsor: employment letter and/or 6 months pay stubs OR proof of self-employment — if applicable','required'=>0],

            // Household Member (if applicable)
            ['code'=>'HM_STATUS_PROOF','label'=>'Household member status: US citizenship OR green card (front/back) — if applicable','required'=>0],
            ['code'=>'HM_ID','label'=>'Household member government-issued photo ID — if applicable','required'=>0],
            ['code'=>'HM_TAX_RETURNS','label'=>'Household member: most recent 3 years tax return or IRS transcripts — if applicable','required'=>0],
            ['code'=>'HM_W2_1099','label'=>'Household member: W-2s and/or 1099s (most recent tax year) — if applicable','required'=>0],
            ['code'=>'HM_PAYSTUBS_OR_SELFEMP','label'=>'Household member: employment letter and/or 6 months pay stubs OR proof of self-employment — if applicable','required'=>0],

            // Other
            ['code'=>'ADDITIONAL_DOCS_OPTIONAL','label'=>'Any other additional documents (optional)','required'=>0],
            ['code'=>'NAME_CHANGE_DOCS','label'=>'Marriage/divorce/adoption decree or court order for any name changes (parent or child) — if applicable','required'=>0],
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
