<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\VisaTypeMapper;

class QuizController extends Controller
{
    /**
     * Static decision tree (partial) implementing Node 1 (Q1) and Node 2 (Q2.1) only.
     * Additional nodes will be appended later.
     */
    protected function tree(): array
    {
        return [
            // Node 1 (Q1)
            1 => [
                'id' => 1,
                'text' => 'What is the primary immigration goal or situation today?',
                'options' => [
                    ['code' => 'A', 'label' => 'Replace or fix a Green Card', 'next' => 2],
                    ['code' => 'B', 'label' => 'Bring a fiancé(e), spouse, or relative to the U.S.', 'next' => 3],
                    ['code' => 'C', 'label' => 'Adjust status to permanent resident / get a Green Card while in the U.S.', 'next' => 4],
                    ['code' => 'D', 'label' => 'Remove conditions on residence (marriage-based conditional LPR)', 'next' => 5],
                    ['code' => 'E', 'label' => 'DACA (Deferred Action for Childhood Arrivals)', 'next' => 6],
                    ['code' => 'F', 'label' => 'Apply for naturalization (become a U.S. citizen)', 'next' => 7],
                ],
            ],
            // Node 2 (Q2.1)
            2 => [
                'id' => 2,
                'text' => 'Is the person a lawful permanent resident whose card was lost, stolen, destroyed, has incorrect information, or never received?',
                'options' => [
                    // Yes branch => terminal recommendation (Form I-90)
                    ['code' => 'YES', 'label' => 'Yes', 'next' => 'FORM_I90'],
                    // No branch => go to Node 5 per spec
                    ['code' => 'NO', 'label' => 'No (They are a conditional resident seeking to remove conditions)', 'next' => 5],
                    // None of the above => ineligible terminal
                    ['code' => 'NONE', 'label' => 'None of the above', 'next' => 'NOT_ELIGIBLE_REPLACE'],
                ],
            ],
            // Node 4 (Q4.1) — Adjust status eligibility
            4 => [
                'id' => 4,
                'text' => 'Are you in the U.S. and eligible to apply for lawful permanent resident status (adjust status)?',
                'options' => [
                    ['code' => 'YES', 'label' => 'Yes – I am eligible to adjust (Form I-485)', 'next' => 41],
                    ['code' => 'NO', 'label' => 'No / Not sure', 'next' => 'ADJUST_NOT_ELIGIBLE'],
                ],
            ],
            // Node 5 (Q5.1) — Conditional resident remove conditions
            5 => [
                'id' => 5,
                'text' => 'Is the person a conditional permanent resident (marriage-based, married less than 2 years when status was granted) and now needs to remove conditions?',
                'options' => [
                    ['code' => 'YES', 'label' => 'Yes – need to remove conditions (Form I-751)', 'next' => 'I751'],
                    ['code' => 'NO', 'label' => 'No – this does not apply', 'next' => 'CONDITIONAL_NOT_APPLICABLE'],
                ],
            ],
            // Node 41 (Q4.2) — Work authorization while pending
            41 => [
                'id' => 41,
                'text' => 'Do you want to work while your Form I-485 (adjustment of status) is pending?',
                'options' => [
                    ['code' => 'YES', 'label' => 'Yes – file Form I-765 for a work permit', 'next' => 'I485_WITH_I765'],
                    ['code' => 'NO', 'label' => 'No – just proceed with Form I-485', 'next' => 'I485_ONLY'],
                ],
            ],
            // Node 3 (Q3.1) — Fiancé or Relative path decision
            3 => [
                'id' => 3,
                'text' => 'Are you a U.S. citizen petitioning for a fiancé(e) to come to the U.S. to marry you?',
                'options' => [
                    ['code' => 'YES', 'label' => 'Yes – I am petitioning for my fiancé(e)', 'next' => 31],
                    ['code' => 'NO', 'label' => 'No – I want to petition for another qualifying relative', 'next' => 34],
                ],
            ],
            // Node 31 (Q3.2) — Fiancé outside U.S.
            31 => [
                'id' => 31,
                'text' => 'Is your fiancé currently outside the U.S.?',
                'options' => [
                    ['code' => 'YES', 'label' => 'Yes', 'next' => 32],
                    ['code' => 'NO', 'label' => 'No', 'next' => 'K1_NOT_OUTSIDE'],
                ],
            ],
            // Node 32 (Q3.3) — In-person meeting last 2 years
            32 => [
                'id' => 32,
                'text' => 'Have you met your fiancé in person in the last two years?',
                'options' => [
                    ['code' => 'YES', 'label' => 'Yes', 'next' => 'K1_ELIGIBLE'],
                    ['code' => 'NO', 'label' => 'No', 'next' => 'K1_NOT_MET'],
                ],
            ],
            // Node 34 (Q3.4) — Relative petition
            34 => [
                'id' => 34,
                'text' => 'Are you a U.S. citizen or lawful permanent resident petitioning to bring a relative (spouse, child, parent, sibling, etc.) for permanent residence?',
                'options' => [
                    ['code' => 'YES', 'label' => 'Yes', 'next' => 341],
                    ['code' => 'NO', 'label' => 'No', 'next' => 'RELATIVE_NOT_ELIGIBLE'],
                ],
            ],
            // Node 6 (Q6.1) — DACA request
            6 => [
                'id' => 6,
                'text' => 'Is the person seeking DACA (initial request or renewal)?',
                'options' => [
                    ['code' => 'YES', 'label' => 'Yes – DACA initial or renewal', 'next' => 'DACA_FORMS'],
                    ['code' => 'NO', 'label' => 'No – not a DACA case', 'next' => 'DACA_NOT_APPLICABLE'],
                ],
            ],
            // Node 7 (Q7.1) — Naturalization eligibility
            7 => [
                'id' => 7,
                'text' => 'Is the person a lawful permanent resident who wants to apply for U.S. citizenship and meets the basic eligibility requirements?',
                'options' => [
                    ['code' => 'YES', 'label' => 'Yes – ready to apply for naturalization (Form N-400)', 'next' => 'N400'],
                    ['code' => 'NO', 'label' => 'No – not eligible / not ready yet', 'next' => 'NATURALIZATION_NOT_ELIGIBLE'],
                ],
            ],
            // Node 341 (Relatives Q1) — Petitioner's Immigration Status
            341 => [
                'id' => 341,
                'text' => 'Are you a U.S. citizen or a lawful permanent resident (Green Card holder)?',
                'options' => [
                    ['code' => 'CITIZEN', 'label' => 'Yes, I am a U.S. citizen', 'next' => 3421],
                    ['code' => 'LPR', 'label' => 'Yes, I am a lawful permanent resident', 'next' => 3422],
                    ['code' => 'NO', 'label' => 'No', 'next' => 'RELATIVE_STATUS_INELIGIBLE'],
                ],
            ],
            // Node 3421 (Relatives Q2 - Citizen) — Relationship options for U.S. citizens
            3421 => [
                'id' => 3421,
                'text' => 'What is your relationship to the person you want to sponsor?',
                'options' => [
                    ['code' => 'SPOUSE', 'label' => 'Spouse', 'next' => 3451],
                    ['code' => 'CHILD_U21', 'label' => 'Child (unmarried and under 21)', 'next' => 3430],
                    ['code' => 'CHILD_UNMARRIED_21_PLUS', 'label' => 'Son/daughter (unmarried, 21 or older)', 'next' => 3451],
                    ['code' => 'CHILD_MARRIED_ANY', 'label' => 'Son/daughter (married, any age)', 'next' => 3451],
                    ['code' => 'PARENT', 'label' => 'Parent (I am 21 or older)', 'next' => 3441],
                    ['code' => 'SIBLING', 'label' => 'Brother or sister (I am a U.S. citizen and 21 or older)', 'next' => 3443],
                    ['code' => 'NONE', 'label' => 'None of the above', 'next' => 'RELATIVE_RELATION_INELIGIBLE'],
                ],
            ],
            // Node 3430 — Child inside U.S. and eligible to adjust? (Citizen path)
            3430 => [
                'id' => 3430,
                'text' => 'Is your child currently in the U.S. and eligible to adjust status (Form I-485)?',
                'options' => [
                    ['code' => 'YES', 'label' => 'Yes – proceed with Child Adjustment of Status', 'next' => 'I485_CHILD'],
                    ['code' => 'NO', 'label' => 'No – proceed with I-130 (consular) path', 'next' => 'I130_CHILD'],
                ],
            ],
            // Node 3422 (Relatives Q2 - LPR) — Relationship options for LPRs (restricted)
            3422 => [
                'id' => 3422,
                'text' => 'What is your relationship to the person you want to sponsor?',
                'options' => [
                    ['code' => 'SPOUSE', 'label' => 'Spouse', 'next' => 3452],
                    ['code' => 'CHILD_U21', 'label' => 'Child (unmarried and under 21)', 'next' => 3432],
                    ['code' => 'CHILD_UNMARRIED_21_PLUS', 'label' => 'Son/daughter (unmarried, 21 or older)', 'next' => 3452],
                    ['code' => 'CHILD_MARRIED_ANY', 'label' => 'Son/daughter (married, any age)', 'next' => 'LPR_CANNOT_PETITION_MARRIED_CHILD'],
                    ['code' => 'NONE', 'label' => 'None of the above', 'next' => 'RELATIVE_RELATION_INELIGIBLE'],
                ],
            ],
            // Node 3432 — Child inside U.S. and eligible to adjust? (LPR path)
            3432 => [
                'id' => 3432,
                'text' => 'Is your child currently in the U.S. and eligible to adjust status (Form I-485)?',
                'options' => [
                    ['code' => 'YES', 'label' => 'Yes – proceed with Child Adjustment of Status', 'next' => 'I485_CHILD'],
                    ['code' => 'NO', 'label' => 'No – proceed with I-130 (consular) path', 'next' => 'I130_CHILD'],
                ],
            ],
            // Node 3441 (Relatives Q4 - Age requirement) — Only for parent/sibling cases
            3441 => [
                'id' => 3441,
                'text' => 'Are you at least 21 years old? (Required to petition parents or siblings)',
                'options' => [
                    ['code' => 'YES', 'label' => 'Yes', 'next' => 3442],
                    ['code' => 'NO', 'label' => 'No', 'next' => 'AGE_REQUIREMENT_NOT_MET'],
                ],
            ],
            // Node 3442 — Parent currently in the U.S. and eligible to adjust?
        3442 => [
                'id' => 3442,
                'text' => 'Is your parent currently in the U.S. and eligible to adjust status (Form I-485)?',
                'options' => [
            ['code' => 'YES', 'label' => 'Yes – proceed with Parent Adjustment of Status', 'next' => 'I485_PARENT'],
            ['code' => 'NO', 'label' => 'No – proceed with consular I-130 process', 'next' => 'I130_PARENT'],
                ],
            ],
            // Node 3443 — Sibling path age confirmation and terminal
            3443 => [
                'id' => 3443,
                'text' => 'Are you at least 21 years old and a U.S. citizen petitioning for your sibling?',
                'options' => [
                    ['code' => 'YES', 'label' => 'Yes', 'next' => 'I130_SIBLING'],
                    ['code' => 'NO', 'label' => 'No', 'next' => 'AGE_REQUIREMENT_NOT_MET'],
                ],
            ],
            // Node 3451 (Relatives Q5 - Criminal history for Citizen path)
            3451 => [
                'id' => 3451,
                'text' => 'Have you ever been convicted of certain crimes involving children, domestic violence, or sexual abuse?',
                'options' => [
                    ['code' => 'NO', 'label' => 'No', 'next' => 3461],
                    ['code' => 'YES', 'label' => 'Yes', 'next' => 'RELATIVE_CRIMINAL_REVIEW'],
                ],
            ],
            // Node 3452 (Relatives Q5 - Criminal history for LPR path)
            3452 => [
                'id' => 3452,
                'text' => 'Have you ever been convicted of certain crimes involving children, domestic violence, or sexual abuse?',
                'options' => [
                    ['code' => 'NO', 'label' => 'No', 'next' => 3462],
                    ['code' => 'YES', 'label' => 'Yes', 'next' => 'RELATIVE_CRIMINAL_REVIEW'],
                ],
            ],
            // Node 3461 (Relatives Q6 - Financial ability citizen path)
            3461 => [
                'id' => 3461,
                'text' => 'Are you able and willing to financially sponsor your relative (or have a joint sponsor ready)?',
                'options' => [
                    ['code' => 'YES', 'label' => 'Yes', 'next' => 'RELATIVE_I130'],
                    ['code' => 'NO', 'label' => 'No', 'next' => 'FINANCIAL_SPONSOR_REQUIRED'],
                ],
            ],
            // Node 3462 (Relatives Q6 - Financial ability LPR path)
            3462 => [
                'id' => 3462,
                'text' => 'Are you able and willing to financially sponsor your relative (or have a joint sponsor ready)?',
                'options' => [
                    ['code' => 'YES', 'label' => 'Yes', 'next' => 'RELATIVE_I130'],
                    ['code' => 'NO', 'label' => 'No', 'next' => 'FINANCIAL_SPONSOR_REQUIRED'],
                ],
            ],
        ];
    }

    /**
     * Terminal outcomes (partial) for Node 2 flows.
     */
    protected function terminals(): array
    {
        return [
            'FORM_I90' => [
                'code' => 'FORM_I90',
                'title' => 'Form I-90 Replacement Guidance',
                'message' => 'You may need to file Form I-90 (Application to Replace Permanent Resident Card). Learn more and apply directly on the USCIS website.',
                'link' => 'https://www.uscis.gov/i-90',
                'forms' => [
                    ['name' => 'Form I-90 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-90.pdf'],
                ],
            ],
            'NOT_ELIGIBLE_REPLACE' => [
                'code' => 'NOT_ELIGIBLE_REPLACE',
                'title' => 'Not Eligible to Replace/Fix',
                'message' => 'Sorry, you are not eligible to replace/fix a Green Card at this time.',
            ],
            // Fiancé (K1) terminals
            'K1_NOT_OUTSIDE' => [
                'code' => 'K1_NOT_OUTSIDE',
                'title' => 'Fiancé Must Be Outside the U.S.',
                'message' => 'Sorry, your fiancé must be outside the United States to apply for a K-1 (fiancé) visa.',
            ],
            'K1_NOT_MET' => [
                'code' => 'K1_NOT_MET',
                'title' => 'In-Person Meeting Requirement Not Met',
                'message' => 'Sorry, you must have met your fiancé in person within the last two years to qualify for a K-1 visa (unless an exemption applies).',
            ],
            'K1_ELIGIBLE' => [
                'code' => 'K1_ELIGIBLE',
                'title' => 'K-1 Fiancé Visa Eligibility',
                'message' => 'You appear eligible to start your K-1 fiancé visa process. File Form I-129F (Petition for Alien Fiancé(e)).',
                'link' => 'https://www.uscis.gov/i-129f',
                'forms' => [
                    ['name' => 'Form I-129F (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-129f.pdf'],
                ],
            ],
            // Relative petition terminals
            'RELATIVE_I130' => [
                'code' => 'RELATIVE_I130',
                'title' => 'Form I-130 Family Petition',
                'message' => 'You may need to file Form I-130 (Petition for Alien Relative). If petitioning for a spouse, also include Form I-130A (Supplemental Information for Spouse Beneficiary).',
                'link' => 'https://www.uscis.gov/i-130',
                'forms' => [
                    ['name' => 'Form I-130 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-130.pdf'],
                    ['name' => 'Form I-130A (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-130a.pdf'],
                ],
            ],
            'I130_PARENT' => [
                'code' => 'I130_PARENT',
                'title' => 'Form I-130 (Parent Abroad)',
                'message' => 'You appear eligible to petition your parent abroad using Form I-130 (consular processing).',
                'link' => 'https://www.uscis.gov/i-130',
                'forms' => [
                    ['name' => 'Form I-130 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-130.pdf'],
                    ['name' => 'Form G-1145 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/g-1145.pdf'],
                ],
            ],
            'I130_CHILD' => [
                'code' => 'I130_CHILD',
                'title' => 'Form I-130 (Child Abroad)',
                'message' => 'You appear eligible to petition your child abroad using Form I-130 (consular processing).',
                'link' => 'https://www.uscis.gov/i-130',
                'forms' => [
                    ['name' => 'Form I-130 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-130.pdf'],
                    ['name' => 'Form G-1145 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/g-1145.pdf'],
                ],
            ],
            'I130_SIBLING' => [
                'code' => 'I130_SIBLING',
                'title' => 'Form I-130 (Sibling Abroad)',
                'message' => 'You appear eligible to petition your sibling abroad using Form I-130 (consular processing).',
                'link' => 'https://www.uscis.gov/i-130',
                'forms' => [
                    ['name' => 'Form I-130 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-130.pdf'],
                    ['name' => 'Form G-1145 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/g-1145.pdf'],
                ],
            ],
            'RELATIVE_NOT_ELIGIBLE' => [
                'code' => 'RELATIVE_NOT_ELIGIBLE',
                'title' => 'Not Eligible to Petition a Relative',
                'message' => 'You must be a U.S. citizen or lawful permanent resident to petition for a qualifying relative for permanent residence.',
            ],
            'RELATIVE_STATUS_INELIGIBLE' => [
                'code' => 'RELATIVE_STATUS_INELIGIBLE',
                'title' => 'Status Not Eligible',
                'message' => 'You must be a U.S. citizen or lawful permanent resident (Green Card holder) to file a family immigration petition.',
            ],
            'RELATIVE_RELATION_INELIGIBLE' => [
                'code' => 'RELATIVE_RELATION_INELIGIBLE',
                'title' => 'Relationship Not Eligible',
                'message' => 'Only certain family relationships qualify (spouse, child, certain sons/daughters, parent (if 21+ and a U.S. citizen), or sibling (if 21+ and a U.S. citizen)).',
            ],
            'AGE_REQUIREMENT_NOT_MET' => [
                'code' => 'AGE_REQUIREMENT_NOT_MET',
                'title' => 'Age Requirement Not Met',
                'message' => 'You must be at least 21 years old to petition for a parent or sibling.',
            ],
            'RELATIVE_CRIMINAL_REVIEW' => [
                'code' => 'RELATIVE_CRIMINAL_REVIEW',
                'title' => 'Criminal History May Impact Petition',
                'message' => 'Certain criminal convictions may affect your ability to petition. A legal review is recommended.',
            ],
            'FINANCIAL_SPONSOR_REQUIRED' => [
                'code' => 'FINANCIAL_SPONSOR_REQUIRED',
                'title' => 'Financial Sponsorship Required',
                'message' => 'A financial sponsor (Form I-864 Affidavit of Support) is required. You may use a joint sponsor if you do not qualify alone.',
            ],
            'LPR_CANNOT_PETITION_MARRIED_CHILD' => [
                'code' => 'LPR_CANNOT_PETITION_MARRIED_CHILD',
                'title' => 'LPR Cannot Petition Married Child',
                'message' => 'Green card holders cannot petition for married sons or daughters. Only U.S. citizens may do so.',
            ],
            // Adjustment of status terminals
            'ADJUST_NOT_ELIGIBLE' => [
                'code' => 'ADJUST_NOT_ELIGIBLE',
                'title' => 'Adjustment Eligibility Uncertain',
                'message' => 'You may not be eligible to adjust status. Review eligibility before filing Form I-485.',
                'link' => 'https://www.uscis.gov/i-485',
            ],
            'I485_ONLY' => [
                'code' => 'I485_ONLY',
                'title' => 'Form I-485 (Adjust Status)',
                'message' => 'I-485 is the main form for adjusting status to lawful permanent residence.',
                'link' => 'https://www.uscis.gov/i-485',
                'forms' => [
                    ['name' => 'Form I-485 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-485.pdf'],
                ],
            ],
            'I485_WITH_I765' => [
                'code' => 'I485_WITH_I765',
                'title' => 'Form I-485 + Optional Form I-765',
                'message' => 'File Form I-485 to adjust status and submit Form I-765 concurrently to request a work permit while waiting.',
                'link' => 'https://www.uscis.gov/i-765',
                'forms' => [
                    ['name' => 'Form I-485 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-485.pdf'],
                    ['name' => 'Form I-765 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-765.pdf'],
                ],
            ],
            // Parent/Child Adjustment terminal (inside U.S.)
            'I485_PARENT' => [
                'code' => 'I485_PARENT',
                'title' => 'Parent Adjustment of Status (I-485)',
                'message' => 'You appear eligible to adjust your parent’s status inside the U.S. (Forms I-130, I-485, I-864; optional I-765/I-131).',
                'link' => 'https://www.uscis.gov/i-485',
                'forms' => [
                    ['name' => 'Form I-130 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-130.pdf'],
                    ['name' => 'Form I-485 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-485.pdf'],
                    ['name' => 'Form I-864 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-864.pdf'],
                    ['name' => 'Form I-765 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-765.pdf'],
                    ['name' => 'Form I-131 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-131.pdf'],
                ],
            ],
            'I485_CHILD' => [
                'code' => 'I485_CHILD',
                'title' => 'Child Adjustment of Status (I-485)',
                'message' => 'Your child appears eligible to adjust status inside the U.S. (Forms I-130, I-485, I-864; optional I-765/I-131).',
                'link' => 'https://www.uscis.gov/i-485',
                'forms' => [
                    ['name' => 'Form I-130 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-130.pdf'],
                    ['name' => 'Form I-485 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-485.pdf'],
                    ['name' => 'Form I-864 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-864.pdf'],
                    ['name' => 'Form I-765 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-765.pdf'],
                    ['name' => 'Form I-131 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-131.pdf'],
                ],
            ],
            // Conditional resident (I-751)
            'I751' => [
                'code' => 'I751',
                'title' => 'Form I-751 (Remove Conditions)',
                'message' => 'You may need to file Form I-751. This removes the 2-year conditional status on your marriage-based Green Card.',
                'link' => 'https://www.uscis.gov/i-751',
                'forms' => [
                    ['name' => 'Form I-751 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-751.pdf'],
                ],
            ],
            'CONDITIONAL_NOT_APPLICABLE' => [
                'code' => 'CONDITIONAL_NOT_APPLICABLE',
                'title' => 'Not a Conditional Resident Case',
                'message' => 'Form I-751 is only for removing conditions from a 2-year marriage-based Green Card. This path does not apply.',
            ],
            // DACA terminals
            'DACA_FORMS' => [
                'code' => 'DACA_FORMS',
                'title' => 'DACA Forms Required',
                'message' => 'For DACA requests, these three forms are required: Form I-821D (Consideration of DACA), Form I-765 (Work Authorization), and Form I-765WS (Worksheet).',
                'link' => 'https://www.uscis.gov/DACA',
                'forms' => [
                    ['name' => 'Form I-821D (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-821d.pdf'],
                    ['name' => 'Form I-765 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-765.pdf'],
                    ['name' => 'Form I-765WS (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/i-765ws.pdf'],
                ],
            ],
            'DACA_NOT_APPLICABLE' => [
                'code' => 'DACA_NOT_APPLICABLE',
                'title' => 'Not a DACA Case',
                'message' => 'This scenario does not involve a DACA initial request or renewal.',
            ],
            // Naturalization terminals
            'N400' => [
                'code' => 'N400',
                'title' => 'Form N-400 (Naturalization)',
                'message' => 'Form N-400 is the main form to apply for U.S. citizenship.',
                'link' => 'https://www.uscis.gov/n-400',
                'forms' => [
                    ['name' => 'Form N-400 (PDF)', 'pdf' => 'https://www.uscis.gov/sites/default/files/document/forms/n-400.pdf'],
                ],
            ],
            'NATURALIZATION_NOT_ELIGIBLE' => [
                'code' => 'NATURALIZATION_NOT_ELIGIBLE',
                'title' => 'Naturalization Eligibility Not Met',
                'message' => 'You may not yet meet the eligibility criteria (residency period, physical presence, good moral character, or other requirements) for Form N-400. Review USCIS guidance before applying.',
                'link' => 'https://www.uscis.gov/citizenship/learn-about-citizenship',
            ],
        ];
    }

    /**
     * Get current node id from session or default to 1.
     */
    protected function currentNodeId(): int|string
    {
        return Session::get('quiz.current', 1);
    }

    /**
     * Advance the quiz state using provided 'choice'.
     */
    public function advance(Request $request)
    {
        $choice = $request->input('choice');
        if (!$choice) {
            return response()->json(['error' => 'Missing choice'], 422);
        }

        $tree = $this->tree();
        $currentId = $this->currentNodeId();
        $node = $tree[$currentId] ?? null;
        if (!$node) {
            // Reset if invalid
            Session::put('quiz.current', 1);
            Session::put('quiz.history', []);
            $node = $tree[1];
        }

        $selected = null;
        foreach ($node['options'] as $opt) {
            if (strcasecmp($opt['code'], $choice) === 0) { $selected = $opt; break; }
        }
        if (!$selected) {
            return response()->json(['error' => 'Invalid choice'], 422);
        }

        $history = Session::get('quiz.history', []);
        $history[] = [
            'node' => $node['id'],
            'choice' => $selected['code'],
            'timestamp' => now()->toIso8601String(),
        ];
        Session::put('quiz.history', $history);

        $next = $selected['next'];
        // If terminal code
        $terminals = $this->terminals();
        if (is_string($next) && isset($terminals[$next])) {
            Session::put('quiz.current', $next); // store terminal code
            $terminal = $terminals[$next];
            $visaMap = $this->mapTerminalToVisa($terminal['code']);
            if ($visaMap) {
                // Persist resolved visa_type explicitly for downstream flows (pricing, application wizard)
                Session::put('quiz.visa_type', $visaMap);
            } else {
                Session::forget('quiz.visa_type');
            }
            return response()->json([
                'done' => true,
                'terminal' => $terminal + [
                    'can_create_application' => (bool) $visaMap,
                    'visa_type' => $visaMap,
                ],
                'history' => $history,
            ]);
        }

        // Otherwise next is a numeric node id
        Session::put('quiz.current', $next);
        $nextNode = $tree[$next] ?? null;
        if (!$nextNode) {
            return response()->json([
                'error' => 'Next node undefined',
                'history' => $history,
            ], 500);
        }

        return response()->json([
            'done' => false,
            'node' => [
                'id' => $nextNode['id'],
                'text' => $nextNode['text'],
                'options' => array_map(fn($o) => [
                    'code' => $o['code'],
                    'label' => $o['label'],
                ], $nextNode['options']),
            ],
            'history' => $history,
        ]);
    }

    /**
     * Return current quiz state.
     */
    public function state()
    {
        $current = $this->currentNodeId();
        $tree = $this->tree();
        $terminals = $this->terminals();
        $history = Session::get('quiz.history', []);

        // If no session exists, initialize to node 1
        if (!Session::has('quiz.current')) {
            Session::put('quiz.current', 1);
            $current = 1;
        }

        if (is_string($current) && isset($terminals[$current])) {
            $terminal = $terminals[$current];
            $visaMap = $this->mapTerminalToVisa($terminal['code']);
            if ($visaMap) {
                Session::put('quiz.visa_type', $visaMap);
            } else {
                Session::forget('quiz.visa_type');
            }
            return response()->json([
                'done' => true,
                'terminal' => $terminal + [
                    'can_create_application' => (bool) $visaMap,
                    'visa_type' => $visaMap,
                ],
                'history' => $history,
            ]);
        }

        $node = $tree[$current] ?? $tree[1];
        return response()->json([
            'done' => false,
            'node' => [
                'id' => $node['id'],
                'text' => $node['text'],
                'options' => array_map(fn($o) => [
                    'code' => $o['code'],
                    'label' => $o['label'],
                ], $node['options']),
            ],
            'history' => $history,
        ]);
    }

    /**
     * Reset quiz to initial state (Node 1).
     */
    public function reset()
    {
        Session::forget('quiz.current');
        Session::forget('quiz.history');
    Session::forget('quiz.visa_type');
        Session::put('quiz.current', 1);
        $tree = $this->tree();
        $node = $tree[1];
        return response()->json([
            'done' => false,
            'node' => [
                'id' => $node['id'],
                'text' => $node['text'],
                'options' => array_map(fn($o) => [
                    'code' => $o['code'],
                    'label' => $o['label'],
                ], $node['options']),
            ],
            'history' => [],
        ]);
    }

    protected function mapTerminalToVisa(string $terminal): ?string
    {
    return VisaTypeMapper::map($terminal);
    }
    
    /**
     * Show the new eligibility quiz interface
     */
    public function newQuiz()
    {
        $quiz_spec = app(\App\Services\QuizGraphService::class)->buildSpec();
        return view('eligibility-quiz-new', compact('quiz_spec'));
    }

    /**
     * JSON spec for public & admin consumers to avoid drift.
     */
    public function specJson()
    {
        $quiz_spec = app(\App\Services\QuizGraphService::class)->buildSpec();
        return response()->json($quiz_spec);
    }

    /**
     * Store a terminal code from the front-end only quiz (eligibility-quiz-new) into session
     * so that pricing & subsequent flows can detect visa_type without relying solely on URL params.
     */
    public function tagTerminal(Request $request)
    {
        $data = $request->validate([
            'terminal' => ['required','string','max:120']
        ]);
        $terminal = strtoupper($data['terminal']);
        $all = config('quiz.terminals', []);
        if(!in_array($terminal, $all, true)) {
            return response()->json(['error'=>'Unknown terminal'],422);
        }
        \Illuminate\Support\Facades\Session::put('quiz.current',$terminal);
        $visa = VisaTypeMapper::map($terminal);
        if($visa){ \Illuminate\Support\Facades\Session::put('quiz.visa_type',$visa); }
        return response()->json([
            'stored' => true,
            'terminal' => $terminal,
            'visa_type' => $visa,
        ]);
    }
}
