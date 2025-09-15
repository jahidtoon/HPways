<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequiredDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            'I130' => [
                ['code'=>'PETITIONER_PASSPORT','label'=>'Petitioner Passport ID Page'],
                ['code'=>'BENEFICIARY_PASSPORT','label'=>'Beneficiary Passport ID Page'],
                ['code'=>'MARRIAGE_CERT','label'=>'Marriage Certificate','translation_possible'=>true],
                ['code'=>'PROOF_RELATION','label'=>'Proof of Bona Fide Relationship (Photos, Chats)','translation_possible'=>true,'required'=>false],
            ],
            'I485' => [
                ['code'=>'APPLICANT_PASSPORT','label'=>'Applicant Passport ID Page'],
                ['code'=>'BIRTH_CERT','label'=>'Birth Certificate','translation_possible'=>true],
                ['code'=>'I94','label'=>'Form I-94 Arrival/Departure Record'],
                ['code'=>'MEDICAL_EXAM','label'=>'Form I-693 Medical Exam (if available)','required'=>false],
            ],
            'I751' => [
                ['code'=>'CONDITIONAL_GREEN_CARD','label'=>'Conditional Green Card (front/back)'],
                ['code'=>'MARRIAGE_CERT','label'=>'Marriage Certificate','translation_possible'=>true],
                ['code'=>'JOINT_DOCS','label'=>'Joint Financial / Residence Evidence','required'=>false],
            ],
            'K1' => [
                ['code'=>'US_CITIZEN_PROOF','label'=>'Proof of U.S. Citizenship'],
                ['code'=>'INTENT_TO_MARRY','label'=>'Statements of Intent to Marry (Both)'],
                ['code'=>'IN_PERSON_PROOF','label'=>'Proof Met In Person (Travel, Photos)'],
            ],
            'N400' => [
                ['code'=>'GREEN_CARD','label'=>'Permanent Resident Card (front/back)'],
                ['code'=>'SELECTIVE_SERVICE','label'=>'Selective Service Proof (if applicable)','required'=>false],
            ],
            'DACA' => [
                ['code'=>'DACA_ID','label'=>'Previous DACA Approval / ID'],
                ['code'=>'DACA_EAD','label'=>'Employment Authorization Card'],
            ],
            'I90' => [
                ['code'=>'EXISTING_GREEN_CARD','label'=>'Green Card (if available)'],
                ['code'=>'POLICE_REPORT','label'=>'Police Report (if lost/stolen)','required'=>false],
            ],
        ];

        foreach($templates as $visa => $items){
            foreach($items as $row){
                DB::table('required_documents')->updateOrInsert(
                    ['visa_type'=>$visa,'code'=>$row['code']],
                    [
                        'label'=>$row['label'],
                        'required'=>$row['required'] ?? true,
                        'translation_possible'=>$row['translation_possible'] ?? false,
                        'active'=>true,
                        'updated_at'=>now(),
                        'created_at'=>now(),
                    ]
                );
            }
        }
    }
}
