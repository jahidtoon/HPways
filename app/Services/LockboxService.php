<?php

namespace App\Services;

class LockboxService
{
    protected $lockboxData = [
        'I485' => [
            'TX' => [
                'usps' => "USCIS\nAttn: AOS\nP.O. Box 650288\nDallas, TX 75265-0288",
                'courier' => "USCIS\nAttn: AOS (Box 650288)\n2501 S. State Hwy. 121 Business\nSuite 400\nLewisville, TX 75067-8003"
            ],
            'DE,GA,IN,IA,KS,MD,MI,MS,MO,NE,NY,NC,ND,PA,SD,VA,WV,WI' => [
                'usps' => "USCIS\nAttn: AOS\nP.O Box 4109\nCarol Stream, IL 60197-4109",
                'courier' => "USCIS\nAttn: AOS (Box 4109)\n2500 Westfield Drive\nElgin, IL 60124-7836"
            ],
            'AL,AK,AZ,AR,CO,HI,ID,KY,LA,MT,NV,NM,OK,OR,TN,UT,WA,WY' => [
                'usps' => "USCIS\nAttn: AOS\nP.O.Box 20500\nPhoenix, AZ 85036-0500",
                'courier' => "USCIS\nAttn: AOS (Box 20500)\n2108 E. Elliot Rd.\nTempe, AZ 85284-1806"
            ],
            'CA,MP,CT,DC,FL,GU,IL,ME,MA,MN,NH,NJ,OH,PR,RI,SC,VI,VT' => [
                'usps' => "USCIS\nAttn: AOS\nP.O. Box 805887\nChicago, IL 60680",
                'courier' => "USCIS\nAttn: AOS (Box 805887)\n131 S. Dearborn St., 3rd Floor\nChicago, IL 60603-5517"
            ]
        ],
        'K1' => [
            'ALL' => [
                'usps' => "USCIS\nAttn: I-129F\nP.O. Box 660151\nDallas, TX 75266-0151",
                'courier' => "USCIS\nAttn: I-129F (Box 660151)\n2501 South State Hwy 121 Business\nSuite 400\nLewisville, TX 75067-8003"
            ]
        ]
    ];

    public function getLockboxAddress(string $visaType, string $state): array
    {
        if (!isset($this->lockboxData[$visaType])) {
            throw new \InvalidArgumentException("Unsupported visa type: {$visaType}");
        }
        
        $visaData = $this->lockboxData[$visaType];
        
        if (isset($visaData['ALL'])) {
            return $visaData['ALL'];
        }
        
        foreach ($visaData as $stateGroup => $addresses) {
            $states = explode(',', $stateGroup);
            if (in_array($state, $states)) {
                return $addresses;
            }
        }
        
        throw new \InvalidArgumentException("No lockbox found for state {$state} and visa type {$visaType}");
    }

    public function getSupportedVisaTypes(): array
    {
        return ['I485', 'K1', 'I751', 'DACA', 'N400', 'I90', 'I130'];
    }
    
    public function getAllStates(): array
    {
        return [
            'AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas',
            'CA' => 'California', 'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware',
            'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawaii', 'ID' => 'Idaho',
            'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' => 'Iowa', 'KS' => 'Kansas',
            'KY' => 'Kentucky', 'LA' => 'Louisiana', 'ME' => 'Maine', 'MD' => 'Maryland',
            'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi',
            'MO' => 'Missouri', 'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada',
            'NH' => 'New Hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico', 'NY' => 'New York',
            'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio', 'OK' => 'Oklahoma',
            'OR' => 'Oregon', 'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina',
            'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah',
            'VT' => 'Vermont', 'VA' => 'Virginia', 'WA' => 'Washington', 'WV' => 'West Virginia',
            'WI' => 'Wisconsin', 'WY' => 'Wyoming',
            'DC' => 'District of Columbia', 'PR' => 'Puerto Rico', 'VI' => 'U.S. Virgin Islands',
            'GU' => 'Guam', 'AS' => 'American Samoa', 'MP' => 'Northern Mariana Islands',
            'MILITARY' => 'Military (APO/FPO)', 'OUTSIDE_US' => 'Outside US'
        ];
    }
}
