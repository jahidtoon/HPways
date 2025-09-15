<?php

return [
    // All terminal codes defined in QuizController::terminals()
    'terminals' => [
        'FORM_I90','NOT_ELIGIBLE_REPLACE',
        'K1_NOT_OUTSIDE','K1_NOT_MET','K1_ELIGIBLE',
    'RELATIVE_I130','I130_PARENT','I130_CHILD','I130_SIBLING','RELATIVE_NOT_ELIGIBLE','RELATIVE_STATUS_INELIGIBLE','RELATIVE_RELATION_INELIGIBLE','AGE_REQUIREMENT_NOT_MET','RELATIVE_CRIMINAL_REVIEW','FINANCIAL_SPONSOR_REQUIRED','LPR_CANNOT_PETITION_MARRIED_CHILD',
        'ADJUST_NOT_ELIGIBLE','I485_ONLY','I485_WITH_I765',
        'I485_PARENT','I485_CHILD',
        'I751','CONDITIONAL_NOT_APPLICABLE',
        'DACA_FORMS','DACA_NOT_APPLICABLE',
        'N400','NATURALIZATION_NOT_ELIGIBLE',
    ],

    // Terminals that produce an actionable application / package flow.
    // (Count should match available visa_type based package groupings.)
    'actionable_terminals' => [
        'FORM_I90',      // I-90 replacement
        'K1_ELIGIBLE',   // K-1 fiancé
    'RELATIVE_I130', // Family petition
    'I130_PARENT',   // Parent abroad consular I-130
    'I130_CHILD',    // Child abroad consular I-130
    'I130_SIBLING',  // Sibling abroad consular I-130
        'I485_ONLY',     // Adjustment
        'I485_WITH_I765',// Adjustment + EAD (still maps to I485)
        'I751',          // Remove conditions
        'I485_PARENT',   // Parent AOS inside US
        'I485_CHILD',    // Child AOS inside US
        'DACA_FORMS',    // DACA tri-form set
        'N400',          // Naturalization
        // Add more actionable terminals here as quiz expands
    ],

    // Mapping from terminal code => internal visa_type code.
    // Non-listed or ineligible terminals intentionally map to null.
    'terminal_to_visa_type' => [
        'FORM_I90' => 'I90',
        'K1_ELIGIBLE' => 'K1',
    'RELATIVE_I130' => 'I130',
    'I130_PARENT' => 'I130_PARENT',
    'I130_CHILD' => 'I130_CHILD',
    'I130_SIBLING' => 'I130_SIBLING',
        'I485_ONLY' => 'I485',
        'I485_WITH_I765' => 'I485',
        'I485_PARENT' => 'I485_PARENT',
        'I485_CHILD' => 'I485_CHILD',
        'I751' => 'I751',
        'DACA_FORMS' => 'DACA',
        'N400' => 'N400',
    ],
];
