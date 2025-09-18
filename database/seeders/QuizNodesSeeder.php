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
                    ['code' => 'A', 'label' => 'Replace or fix a Green Card', 'next' => 'N2_Q1'],
                    ['code' => 'B', 'label' => 'Bring a fiancé(e) or spouse/relative to the U.S.', 'next' => 'N3_Q1'],
                    ['code' => 'C', 'label' => 'Adjust status to permanent resident / get a Green Card while in US', 'next' => 'N4_Q1'],
                    ['code' => 'D', 'label' => 'Remove conditions on residence (marriage-based conditional LPR)', 'next' => 'N5_Q1'],
                    ['code' => 'E', 'label' => 'DACA (consideration of deferred action for childhood arrivals) – Renewal', 'next' => 'N6_Q1'],
                    ['code' => 'F', 'label' => 'Apply for naturalization (become a US citizen)', 'next' => 'N7_Q1']
                ]
            ],
            [
                'node_id' => 'N2_Q1',
                'title' => 'I-90: Location',
                'question' => 'Do you currently live in the United States?',
                'type' => 'single',
                'options' => [
                    ['code' => 'yes', 'label' => 'Yes', 'next' => 'N2_Q2'],
                    ['code' => 'no', 'label' => 'No', 'ineligible' => 'Sorry you are not eligible to replace/fix a green card at this time.']
                ]
            ],
            [
                'node_id' => 'N2_Q2',
                'title' => 'I-90: Status',
                'question' => 'What is your current immigration status?',
                'type' => 'single',
                'options' => [
                    ['code' => 'pr', 'label' => 'I have permanent resident status', 'next' => 'N2_Q3'],
                    ['code' => 'nonpr', 'label' => 'I have non-permanent resident status', 'ineligible' => 'Sorry you are not eligible to replace/fix a green card at this time.']
                ]
            ],
            [
                'node_id' => 'N2_Q3',
                'title' => 'I-90: Reason',
                'question' => 'What is the current status of your Green Card?',
                'type' => 'single',
                'options' => [
                    ['code' => 'lost', 'label' => 'Lost, Stolen, Damaged or Destroyed', 'eligible' => true],
                    ['code' => 'expired', 'label' => 'Card Expired or Expiring Soon', 'next' => 'N2B_Q1'],
                    ['code' => 'never', 'label' => 'Card Issued but Never Received', 'eligible' => true],
                    ['code' => 'uscis_error', 'label' => 'Incorrect Information on Card (USCIS Error)', 'eligible' => true],
                    ['code' => 'name_change', 'label' => 'Biographic Information Changed (Name)', 'eligible' => true],
                    ['code' => 'gender_change', 'label' => 'Biographic Information Changed (Gender)', 'eligible' => true],
                    ['code' => 'turning14', 'label' => 'Turning 14 Years Old', 'eligible' => true],
                    ['code' => 'none', 'label' => 'None of the Above', 'ineligible' => 'Sorry you are not eligible to replace/fix a green card at this time.']
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
