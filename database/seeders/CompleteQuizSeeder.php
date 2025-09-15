<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QuizNode;

class CompleteQuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing nodes
        QuizNode::truncate();

        // Complete quiz structure from QuizController
        $nodes = [
            // Main Question (Q1)
            [
                'node_id' => 'Q1',
                'title' => 'Primary Immigration Goal',
                'question' => 'What is the primary immigration goal or situation today?',
                'type' => 'single',
                'options' => [
                    ['value' => 'A', 'label' => 'Replace or fix a Green Card', 'next' => 'Q2'],
                    ['value' => 'B', 'label' => 'Bring a fiancé(e), spouse, or relative to the U.S.', 'next' => 'Q3'],
                    ['value' => 'C', 'label' => 'Adjust status to permanent resident / get a Green Card while in the U.S.', 'next' => 'Q4'],
                    ['value' => 'D', 'label' => 'Remove conditions on residence (marriage-based conditional LPR)', 'next' => 'Q5'],
                    ['value' => 'E', 'label' => 'DACA (Deferred Action for Childhood Arrivals)', 'next' => 'Q6'],
                    ['value' => 'F', 'label' => 'Apply for naturalization (become a U.S. citizen)', 'next' => 'Q7'],
                ],
                'x' => 100,
                'y' => 300
            ],

            // Green Card Replacement (Q2)
            [
                'node_id' => 'Q2',
                'title' => 'Green Card Replacement',
                'question' => 'Is the person a lawful permanent resident whose card was lost, stolen, destroyed, has incorrect information, or never received?',
                'type' => 'single',
                'options' => [
                    ['value' => 'YES', 'label' => 'Yes', 'next' => 'FORM_I90'],
                    ['value' => 'NO', 'label' => 'No (They are a conditional resident seeking to remove conditions)', 'next' => 'Q5'],
                    ['value' => 'NONE', 'label' => 'None of the above', 'next' => 'NOT_ELIGIBLE_REPLACE'],
                ],
                'x' => 450,
                'y' => 100
            ],

            // Fiancé/Relative (Q3)
            [
                'node_id' => 'Q3',
                'title' => 'Fiancé or Relative Petition',
                'question' => 'Are you a U.S. citizen petitioning for a fiancé(e) to come to the U.S. to marry you?',
                'type' => 'single',
                'options' => [
                    ['value' => 'YES', 'label' => 'Yes – I am petitioning for my fiancé(e)', 'next' => 'Q31'],
                    ['value' => 'NO', 'label' => 'No – I want to petition for another qualifying relative', 'next' => 'Q34'],
                ],
                'x' => 450,
                'y' => 200
            ],

            // Adjustment of Status (Q4)
            [
                'node_id' => 'Q4',
                'title' => 'Adjustment of Status Eligibility',
                'question' => 'Are you in the U.S. and eligible to apply for lawful permanent resident status (adjust status)?',
                'type' => 'single',
                'options' => [
                    ['value' => 'YES', 'label' => 'Yes – I am eligible to adjust (Form I-485)', 'next' => 'Q41'],
                    ['value' => 'NO', 'label' => 'No / Not sure', 'next' => 'ADJUST_NOT_ELIGIBLE'],
                ],
                'x' => 450,
                'y' => 300
            ],

            // Remove Conditions (Q5)
            [
                'node_id' => 'Q5',
                'title' => 'Remove Conditions on Residence',
                'question' => 'Is the person a conditional permanent resident (marriage-based, married less than 2 years when status was granted) and now needs to remove conditions?',
                'type' => 'single',
                'options' => [
                    ['value' => 'YES', 'label' => 'Yes – need to remove conditions (Form I-751)', 'next' => 'I751'],
                    ['value' => 'NO', 'label' => 'No – this does not apply', 'next' => 'CONDITIONAL_NOT_APPLICABLE'],
                ],
                'x' => 450,
                'y' => 400
            ],

            // DACA (Q6)
            [
                'node_id' => 'Q6',
                'title' => 'DACA Request',
                'question' => 'Is the person seeking DACA (initial request or renewal)?',
                'type' => 'single',
                'options' => [
                    ['value' => 'YES', 'label' => 'Yes – DACA initial or renewal', 'next' => 'DACA_FORMS'],
                    ['value' => 'NO', 'label' => 'No – not a DACA case', 'next' => 'DACA_NOT_APPLICABLE'],
                ],
                'x' => 450,
                'y' => 500
            ],

            // Naturalization (Q7)
            [
                'node_id' => 'Q7',
                'title' => 'Naturalization Eligibility',
                'question' => 'Is the person a lawful permanent resident who wants to apply for U.S. citizenship and meets the basic eligibility requirements?',
                'type' => 'single',
                'options' => [
                    ['value' => 'YES', 'label' => 'Yes – ready to apply for naturalization (Form N-400)', 'next' => 'N400'],
                    ['value' => 'NO', 'label' => 'No – not eligible / not ready yet', 'next' => 'NATURALIZATION_NOT_ELIGIBLE'],
                ],
                'x' => 450,
                'y' => 600
            ],

            // Fiancé Outside US (Q31)
            [
                'node_id' => 'Q31',
                'title' => 'Fiancé Location',
                'question' => 'Is your fiancé currently outside the U.S.?',
                'type' => 'single',
                'options' => [
                    ['value' => 'YES', 'label' => 'Yes', 'next' => 'Q32'],
                    ['value' => 'NO', 'label' => 'No', 'next' => 'K1_NOT_OUTSIDE'],
                ],
                'x' => 750,
                'y' => 150
            ],

            // Fiancé Meeting (Q32)
            [
                'node_id' => 'Q32',
                'title' => 'In-Person Meeting',
                'question' => 'Have you met your fiancé in person in the last two years?',
                'type' => 'single',
                'options' => [
                    ['value' => 'YES', 'label' => 'Yes', 'next' => 'K1_ELIGIBLE'],
                    ['value' => 'NO', 'label' => 'No', 'next' => 'K1_NOT_MET'],
                ],
                'x' => 1050,
                'y' => 150
            ],

            // Relative Petition (Q34)
            [
                'node_id' => 'Q34',
                'title' => 'Relative Petition Eligibility',
                'question' => 'Are you a U.S. citizen or lawful permanent resident petitioning to bring a relative for permanent residence?',
                'type' => 'single',
                'options' => [
                    ['value' => 'YES', 'label' => 'Yes', 'next' => 'Q341'],
                    ['value' => 'NO', 'label' => 'No', 'next' => 'RELATIVE_NOT_ELIGIBLE'],
                ],
                'x' => 750,
                'y' => 250
            ],

            // Work Authorization (Q41)
            [
                'node_id' => 'Q41',
                'title' => 'Work Authorization While Pending',
                'question' => 'Do you want to work while your Form I-485 (adjustment of status) is pending?',
                'type' => 'single',
                'options' => [
                    ['value' => 'YES', 'label' => 'Yes – file Form I-765 for a work permit', 'next' => 'I485_WITH_I765'],
                    ['value' => 'NO', 'label' => 'No – just proceed with Form I-485', 'next' => 'I485_ONLY'],
                ],
                'x' => 750,
                'y' => 300
            ],

            // Petitioner Status (Q341)
            [
                'node_id' => 'Q341',
                'title' => 'Petitioner Immigration Status',
                'question' => 'Are you a U.S. citizen or a lawful permanent resident (Green Card holder)?',
                'type' => 'single',
                'options' => [
                    ['value' => 'CITIZEN', 'label' => 'Yes, I am a U.S. citizen', 'next' => 'Q3421'],
                    ['value' => 'LPR', 'label' => 'Yes, I am a lawful permanent resident', 'next' => 'Q3422'],
                    ['value' => 'NO', 'label' => 'No', 'next' => 'RELATIVE_STATUS_INELIGIBLE'],
                ],
                'x' => 1050,
                'y' => 250
            ],

            // Terminal Nodes
            [
                'node_id' => 'FORM_I90',
                'title' => 'Form I-90 Replacement',
                'question' => 'Terminal: Form I-90 (Application to Replace Permanent Resident Card)',
                'type' => 'terminal',
                'options' => [],
                'x' => 750,
                'y' => 100
            ],

            [
                'node_id' => 'K1_ELIGIBLE',
                'title' => 'K-1 Fiancé Visa Eligible',
                'question' => 'Terminal: K-1 fiancé visa process (Form I-129F)',
                'type' => 'terminal',
                'options' => [],
                'x' => 1350,
                'y' => 120
            ],

            [
                'node_id' => 'K1_NOT_OUTSIDE',
                'title' => 'Fiancé Not Outside US',
                'question' => 'Terminal: Fiancé must be outside U.S. for K-1 visa',
                'type' => 'terminal',
                'options' => [],
                'x' => 1050,
                'y' => 100
            ],

            [
                'node_id' => 'K1_NOT_MET',
                'title' => 'Meeting Requirement Not Met',
                'question' => 'Terminal: Must meet fiancé in person within last 2 years',
                'type' => 'terminal',
                'options' => [],
                'x' => 1350,
                'y' => 180
            ],

            [
                'node_id' => 'I485_WITH_I765',
                'title' => 'I-485 + Work Authorization',
                'question' => 'Terminal: Form I-485 + Form I-765 (work permit)',
                'type' => 'terminal',
                'options' => [],
                'x' => 1050,
                'y' => 280
            ],

            [
                'node_id' => 'I485_ONLY',
                'title' => 'I-485 Only',
                'question' => 'Terminal: Form I-485 (Adjust Status)',
                'type' => 'terminal',
                'options' => [],
                'x' => 1050,
                'y' => 320
            ],

            [
                'node_id' => 'I751',
                'title' => 'Remove Conditions',
                'question' => 'Terminal: Form I-751 (Remove Conditions on Residence)',
                'type' => 'terminal',
                'options' => [],
                'x' => 750,
                'y' => 400
            ],

            [
                'node_id' => 'DACA_FORMS',
                'title' => 'DACA Forms',
                'question' => 'Terminal: Forms I-821D, I-765, I-765WS',
                'type' => 'terminal',
                'options' => [],
                'x' => 750,
                'y' => 500
            ],

            [
                'node_id' => 'N400',
                'title' => 'Naturalization',
                'question' => 'Terminal: Form N-400 (Naturalization)',
                'type' => 'terminal',
                'options' => [],
                'x' => 750,
                'y' => 600
            ],
        ];

        foreach ($nodes as $node) {
            QuizNode::create($node);
        }

        $this->command->info('Complete quiz structure imported successfully!');
        $this->command->info('Created ' . count($nodes) . ' quiz nodes.');
    }
}
