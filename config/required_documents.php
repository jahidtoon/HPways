<?php

return [
    // Fallback default required documents per visa_type
    // Each item: code, label, required, translation_possible
    'I90' => [
        ['code' => 'GREEN_CARD', 'label' => 'Current Green Card (front and back)', 'required' => true, 'translation_possible' => false],
        ['code' => 'GOVT_ID', 'label' => 'Government-issued photo ID', 'required' => true, 'translation_possible' => false],
        ['code' => 'SSN', 'label' => 'Social Security Number or Social Security Card', 'required' => true, 'translation_possible' => false],
        ['code' => 'PHOTO', 'label' => 'Passport-style photo', 'required' => true, 'translation_possible' => false],
        ['code' => 'PASSPORT', 'label' => 'Passport biographical page', 'required' => true, 'translation_possible' => false],
        ['code' => 'PROOF_STATUS', 'label' => 'Additional proof of status (if any)', 'required' => false, 'translation_possible' => false],
    ],
    'I130' => [
        ['code' => 'PETITIONER_ID', 'label' => 'Petitioner ID (passport or ID)', 'required' => true, 'translation_possible' => false],
        ['code' => 'BENEFICIARY_ID', 'label' => 'Beneficiary passport/ID', 'required' => true, 'translation_possible' => false],
        ['code' => 'MARRIAGE_CERT', 'label' => 'Marriage certificate (if spouse case)', 'required' => false, 'translation_possible' => true],
        ['code' => 'BIRTH_CERT', 'label' => 'Birth certificate (as applicable)', 'required' => false, 'translation_possible' => true],
        ['code' => 'RELATION_PROOF', 'label' => 'Evidence of relationship', 'required' => true, 'translation_possible' => true],
    ],
    'I485' => [
        ['code' => 'PHOTO', 'label' => 'Passport-style photos', 'required' => true, 'translation_possible' => false],
        ['code' => 'BIRTH_CERT', 'label' => 'Birth certificate', 'required' => true, 'translation_possible' => true],
        ['code' => 'I693', 'label' => 'Form I-693 (Medical Exam) if available', 'required' => false, 'translation_possible' => false],
        ['code' => 'PASSPORT', 'label' => 'Passport biographical page', 'required' => true, 'translation_possible' => false],
        ['code' => 'I94', 'label' => 'I-94 record (if any)', 'required' => true, 'translation_possible' => false],
    ],
    'I751' => [
        ['code' => 'GREEN_CARD', 'label' => 'Front and back of conditional Green Card', 'required' => true, 'translation_possible' => false],
        ['code' => 'MARRIAGE_CERT', 'label' => 'Marriage certificate', 'required' => true, 'translation_possible' => true],
        ['code' => 'JOINT_EVIDENCE', 'label' => 'Joint financial evidence (bank statements)', 'required' => true, 'translation_possible' => false],
        ['code' => 'LEASE_AGREEMENT', 'label' => 'Joint lease/mortgage agreements', 'required' => false, 'translation_possible' => false],
        ['code' => 'INSURANCE_DOCS', 'label' => 'Joint insurance policies', 'required' => false, 'translation_possible' => false],
        ['code' => 'UTILITY_BILLS', 'label' => 'Joint utility bills', 'required' => false, 'translation_possible' => false],
        ['code' => 'TAX_RETURNS', 'label' => 'Joint tax returns', 'required' => false, 'translation_possible' => false],
        ['code' => 'BIRTH_CERT_CHILDREN', 'label' => 'Birth certificates of children (if any)', 'required' => false, 'translation_possible' => true],
        ['code' => 'PHOTOS', 'label' => 'Family photos as evidence', 'required' => false, 'translation_possible' => false],
        ['code' => 'AFFIDAVITS', 'label' => 'Affidavits from friends/family', 'required' => false, 'translation_possible' => false],
    ],
    'K1' => [
        ['code' => 'PETITIONER_ID', 'label' => 'Petitioner ID', 'required' => true, 'translation_possible' => false],
        ['code' => 'BENEFICIARY_PASSPORT', 'label' => 'Beneficiary passport', 'required' => true, 'translation_possible' => false],
        ['code' => 'MEETING_PROOF', 'label' => 'Proof of in-person meeting (past 2 years)', 'required' => true, 'translation_possible' => true],
    ],
    'DACA' => [
        ['code' => 'PHOTO', 'label' => 'Passport-style photos', 'required' => true, 'translation_possible' => false],
        ['code' => 'CONTINUOUS_RESIDENCE', 'label' => 'Evidence of continuous residence', 'required' => true, 'translation_possible' => true],
        ['code' => 'SCHOOL_RECORDS', 'label' => 'School or military records', 'required' => false, 'translation_possible' => true],
    ],
    'N400' => [
        ['code' => 'GREEN_CARD', 'label' => 'Front and back of Green Card', 'required' => true, 'translation_possible' => false],
        ['code' => 'PHOTO', 'label' => 'Passport-style photos (if required)', 'required' => false, 'translation_possible' => false],
        ['code' => 'TRAVEL_HISTORY', 'label' => 'Travel history evidence', 'required' => false, 'translation_possible' => false],
    ],
];
