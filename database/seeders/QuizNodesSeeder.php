<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QuizNode;

class QuizNodesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nodes = [
            [
                'node_id' => 'Q1',
                'title' => 'Primary Immigration Goal',
                'question' => 'What is your primary immigration goal or situation today?',
                'type' => 'single',
                'options' => [
                    ['value' => 'A', 'label' => 'Replace or fix a Green Card', 'next' => 'N2_Q1'],
                    ['value' => 'B', 'label' => 'Bring a fiancé(e) or spouse/relative to the U.S.', 'next' => 'N3_Q1'],
                    ['value' => 'C', 'label' => 'Adjust status to permanent resident / get a Green Card while in US', 'next' => 'N4_Q1'],
                    ['value' => 'D', 'label' => 'Remove conditions on residence (marriage-based conditional LPR)', 'next' => 'N5_Q1'],
                    ['value' => 'E', 'label' => 'DACA (consideration of deferred action for childhood arrivals) – Renewal', 'next' => 'N6_Q1'],
                    ['value' => 'F', 'label' => 'Apply for naturalization (become a US citizen)', 'next' => 'N7_Q1']
                ]
            ],
            [
                'node_id' => 'N2_Q1',
                'title' => 'I-90: Location',
                'question' => 'Do you currently live in the United States?',
                'type' => 'single',
                'options' => [
                    ['value' => 'yes', 'label' => 'Yes', 'next' => 'N2_Q2'],
                    ['value' => 'no', 'label' => 'No', 'ineligible' => 'Sorry you are not eligible to replace/fix a green card at this time.']
                ]
            ],
            [
                'node_id' => 'N2_Q2',
                'title' => 'I-90: Status',
                'question' => 'What is your current immigration status?',
                'type' => 'single',
                'options' => [
                    ['value' => 'pr', 'label' => 'I have permanent resident status', 'next' => 'N2_Q3'],
                    ['value' => 'nonpr', 'label' => 'I have non-permanent resident status', 'ineligible' => 'Sorry you are not eligible to replace/fix a green card at this time.']
                ]
            ],
            [
                'node_id' => 'N2_Q3',
                'title' => 'I-90: Reason',
                'question' => 'What is the current status of your Green Card?',
                'type' => 'single',
                'options' => [
                    ['value' => 'lost', 'label' => 'Lost, Stolen, Damaged or Destroyed', 'eligible' => true],
                    ['value' => 'expired', 'label' => 'Card Expired or Expiring Soon', 'next' => 'N2B_Q1'],
                    ['value' => 'never', 'label' => 'Card Issued but Never Received', 'eligible' => true],
                    ['value' => 'uscis_error', 'label' => 'Incorrect Information on Card (USCIS Error)', 'eligible' => true],
                    ['value' => 'name_change', 'label' => 'Biographic Information Changed (Name)', 'eligible' => true],
                    ['value' => 'gender_change', 'label' => 'Biographic Information Changed (Gender)', 'eligible' => true],
                    ['value' => 'turning14', 'label' => 'Turning 14 Years Old', 'eligible' => true],
                    ['value' => 'none', 'label' => 'None of the Above', 'ineligible' => 'Sorry you are not eligible to replace/fix a green card at this time.']
                ]
            ],
            // Add more nodes as needed...
        ];

        foreach ($nodes as $node) {
            QuizNode::updateOrCreate(
                ['node_id' => $node['node_id']],
                $node
            );
        }
    }
}
