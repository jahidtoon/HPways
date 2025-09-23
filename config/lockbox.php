<?php

return [
    // Visa type => mapping
    // Use project visa types: I90, I130, I485 (AOS), I751, K1 (I-129F), DACA, N400
    'I485' => [
        'attn' => 'AOS',
        'groups' => [
            // Texas only
            'TX' => [
                'states' => ['TX'],
                'usps' => [
                    'recipient' => 'USCIS Attn: AOS',
                    'address' => 'P.O. Box 650288',
                    'city' => 'Dallas', 'state' => 'TX', 'zip' => '75265-0288'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: AOS (Box 650288)',
                    'address' => '2501 S. State Hwy. 121 Business Suite 400',
                    'city' => 'Lewisville', 'state' => 'TX', 'zip' => '75067-8003'
                ],
            ],
            // Carol Stream IL group
            'IL-CAROL' => [
                'states' => ['DE','GA','IN','IA','KS','MD','MI','MS','MO','NE','NY','NC','ND','PA','SD','VA','WV','WI'],
                'usps' => [
                    'recipient' => 'USCIS Attn: AOS',
                    'address' => 'P.O Box 4109',
                    'city' => 'Carol Stream', 'state' => 'IL', 'zip' => '60197-4109'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: AOS (Box 4109)',
                    'address' => '2500 Westfield Drive',
                    'city' => 'Elgin', 'state' => 'IL', 'zip' => '60124-7836'
                ],
            ],
            // Phoenix AZ group
            'AZ-PHOENIX' => [
                'states' => ['AL','AK','AZ','AR','CO','HI','ID','KY','LA','MT','NV','NM','OK','OR','TN','UT','WA','WY'],
                'usps' => [
                    'recipient' => 'USCIS Attn: AOS',
                    'address' => 'P.O.Box 20500',
                    'city' => 'Phoenix', 'state' => 'AZ', 'zip' => '85036-0500'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: AOS (Box 20500)',
                    'address' => '2108 E. Elliot Rd.',
                    'city' => 'Tempe', 'state' => 'AZ', 'zip' => '85284-1806'
                ],
            ],
            // Chicago IL group
            'IL-CHICAGO' => [
                'states' => ['CA','MP','CT','DC','FL','GU','IL','ME','MA','MN','NH','NJ','OH','PR','RI','SC','VI','VT'],
                'usps' => [
                    'recipient' => 'USCIS Attn: AOS',
                    'address' => 'P.O. Box 805887',
                    'city' => 'Chicago', 'state' => 'IL', 'zip' => '60680'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: AOS (Box 805887)',
                    'address' => '131 S. Dearborn St., 3rd Floor',
                    'city' => 'Chicago', 'state' => 'IL', 'zip' => '60603-5517'
                ],
            ],
        ],
    ],

    'K1' => [ // I-129F
        'attn' => 'I-129F',
        'groups' => [
            'ALL' => [
                'usps' => [
                    'recipient' => 'USCIS Attn: I-129F',
                    'address' => 'P.O. Box 660151',
                    'city' => 'Dallas', 'state' => 'TX', 'zip' => '75266-0151'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: I-129F (Box 660151)',
                    'address' => '2501 South State Hwy 121 Business Suite 400',
                    'city' => 'Lewisville', 'state' => 'TX', 'zip' => '75067-8003'
                ],
            ]
        ]
    ],

    'I751' => [
        'attn' => 'I-751',
        'groups' => [
            'IL-CAROL' => [
                'states' => ['CT','FL','GA','GU','IL','IN','ME','MD','MA','MI','NH','NJ','NY','OH','PA','RI','SC','VT','WI'],
                'usps' => [
                    'recipient' => 'USCIS Attn: I-751',
                    'address' => 'P.O. Box 4072',
                    'city' => 'Carol Stream', 'state' => 'IL', 'zip' => '60197-4072'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: I-751 (Box 4072)',
                    'address' => '2500 Westfield Drive',
                    'city' => 'Elgin', 'state' => 'IL', 'zip' => '60124-7836'
                ],
            ],
            'AZ-PHOENIX' => [
                'states' => ['AL','AK','AS','AZ','AR','AE','AP','AA','CA','CO','MP','DC','HI','ID','IA','KS','KY','LA','MH','FM','MN','MS','MO','MT','NE','NV','NM','NC','ND','OK','OR','PW','PR','SD','TN','TX','VI','UT','VA','WA','WV','WY'],
                'usps' => [
                    'recipient' => 'USCIS Attn: I-751',
                    'address' => 'P.O. Box 21200',
                    'city' => 'Phoenix', 'state' => 'AZ', 'zip' => '85036-1200'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: I-751 (Box 21200)',
                    'address' => '2108 E.Elliot Rd',
                    'city' => 'Tempe', 'state' => 'AZ', 'zip' => '85284-1806'
                ],
            ],
        ],
    ],

    'DACA' => [
        'attn' => 'DACA',
        'groups' => [
            'AZ-PHOENIX' => [
                'states' => ['AZ','CA'],
                'usps' => [
                    'recipient' => 'USCIS Attn: DACA',
                    'address' => 'P.O. Box 20700',
                    'city' => 'Phoenix', 'state' => 'AZ', 'zip' => '85036-0700'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: DACA (Box 20700)',
                    'address' => '2108 E. Elliot Rd',
                    'city' => 'Tempe', 'state' => 'AZ', 'zip' => '85284-1806'
                ],
            ],
            'TX-DALLAS' => [
                'states' => ['AK','AL','AR','FL','GU','HI','ID','IA','KS','LA','MN','MO','MS','MT','ND','NE','NM','OK','PR','SD','TN','TX','UT','VI','WY'],
                'usps' => [
                    'recipient' => 'USCIS Attn: DACA',
                    'address' => 'P.O. Box 660045',
                    'city' => 'Dallas', 'state' => 'TX', 'zip' => '75266-0045'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: DACA (Box 660045)',
                    'address' => '2501 S. State Hwy.121, Business Suite 400',
                    'city' => 'Lewisville', 'state' => 'TX', 'zip' => '75067-8003'
                ],
            ],
            'IL-CHICAGO' => [
                'states' => ['CO','CT','DE','DC','GA','IL','IN','KY','MA','MD','ME','MI','NV','NC','NH','NJ','NY','OH','OR','PA','RI','SC','VA','VT','WA','WI','WV'],
                'usps' => [
                    'recipient' => 'USCIS Attn: DACA',
                    'address' => 'P.O. Box 5757',
                    'city' => 'Chicago', 'state' => 'IL', 'zip' => '60680-5757'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: DACA (Box 5757)',
                    'address' => '131 S. Dearborn – 3rd Floor',
                    'city' => 'Chicago', 'state' => 'IL', 'zip' => '60603-5517'
                ],
            ],
        ],
    ],

    'N400' => [
        'attn' => 'N-400',
        'groups' => [
            'IL-CAROL' => [
                'states' => ['CT','DE','DC','FL','GA','ME','MD','MA','NH','NJ','NY','NC','PA','RI','SC','VT','VA','WV'],
                'usps' => [
                    'recipient' => 'USCIS Attn: N-400',
                    'address' => 'P.O. Box 4060',
                    'city' => 'Carol Stream', 'state' => 'IL', 'zip' => '60197-4060'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: N-400 (Box 4060)',
                    'address' => '2500 Westfield Drive',
                    'city' => 'Elgin', 'state' => 'IL', 'zip' => '60124-7836'
                ],
            ],
            'AZ-PHOENIX' => [
                'states' => ['AL','AK','AS','AZ','AA','AE','AP','CA','CO','MP','GU','HI','ID','KS','KY','MH','FM','MN','MS','MT','NE','NV','NM','ND','OR','PW','PR','SD','TN','UT','VI','WA','WY'],
                'usps' => [
                    'recipient' => 'USCIS Attn: N-400',
                    'address' => 'P.O. Box 21251',
                    'city' => 'Phoenix', 'state' => 'AZ', 'zip' => '85036-1251'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: N-400 (Box 21251)',
                    'address' => '2108 E.Elliot Rd',
                    'city' => 'Tempe', 'state' => 'AZ', 'zip' => '85284-1806'
                ],
            ],
            'TX-DALLAS' => [
                'states' => ['AR','LA','OK','TX'],
                'usps' => [
                    'recipient' => 'USCIS Attn: N-400',
                    'address' => 'P.O. Box 660060',
                    'city' => 'Dallas', 'state' => 'TX', 'zip' => '75266-0060'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: N-400 (Box 660060)',
                    'address' => '2501 S State Hwy 121 Business, Suite 400',
                    'city' => 'Lewisville', 'state' => 'TX', 'zip' => '75067-8003'
                ],
            ],
            'IL-CHICAGO' => [
                'states' => ['IL','IN','IA','MI','MO','OH','WI'],
                'usps' => [
                    'recipient' => 'USCIS Attn: N-400',
                    'address' => 'P.O. Box 4380',
                    'city' => 'Chicago', 'state' => 'IL', 'zip' => '60680-4380'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: N-400 (Box 4380)',
                    'address' => '131 S. Dearborn, 3rd Floor',
                    'city' => 'Chicago', 'state' => 'IL', 'zip' => '60603-5517'
                ],
            ],
            'MILITARY' => [
                'states' => [], // For current or former military members
                'usps' => [
                    'recipient' => 'USCIS Attn: Military N-400',
                    'address' => 'P.O. Box 4446',
                    'city' => 'Chicago', 'state' => 'IL', 'zip' => '60680-4446'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: Military N-400 (Box 4446)',
                    'address' => '131 S. Dearborn, 3rd Floor',
                    'city' => 'Chicago', 'state' => 'IL', 'zip' => '60603-5517'
                ],
            ],
        ],
    ],

    'I90' => [
        'attn' => 'I-90',
        'groups' => [
            'ALL' => [
                'usps' => [
                    'recipient' => 'USCIS Attn: I-90',
                    'address' => 'P.O. Box 21262',
                    'city' => 'Phoenix', 'state' => 'AZ', 'zip' => '85036-1262'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: I-90 (Box 21262)',
                    'address' => '2108 E. Elliot Rd',
                    'city' => 'Tempe', 'state' => 'AZ', 'zip' => '85284-1806'
                ],
            ]
        ],
    ],

    'I130' => [
        'attn' => 'I-130',
        'groups' => [
            'AZ-PHOENIX' => [
                'states' => ['AK','AS','AZ','CA','CO','MP','FL','GU','HI','ID','KS','MT','NE','NV','NM','ND','OK','OR','PR','SD','TX','UT','VI','WA','WY'],
                'usps' => [
                    'recipient' => 'USCIS Attn: I-130',
                    'address' => 'P.O. Box 21700',
                    'city' => 'Phoenix', 'state' => 'AZ', 'zip' => '85036-1700'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: I-130 (Box 21700)',
                    'address' => '2108 E. Elliot Rd.',
                    'city' => 'Tempe', 'state' => 'AZ', 'zip' => '85284-1806'
                ],
            ],
            'IL-CAROL' => [
                'states' => ['AL','AR','AA','AE','AP','CT','DE','GA','IL','IN','IA','KY','LA','ME','MH','FM','MD','MA','MI','MN','MS','MO','NH','NJ','NY','NC','OH','PW','PA','RI','SC','TN','VT','VA','DC','WV','WI'],
                'usps' => [
                    'recipient' => 'USCIS Attn: I-130',
                    'address' => 'P.O Box 4053',
                    'city' => 'Carol Stream', 'state' => 'IL', 'zip' => '60197-4053'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: I-130 (Box 4053)',
                    'address' => '2500 Westfield Drive',
                    'city' => 'Elgin', 'state' => 'IL', 'zip' => '60124-7836'
                ],
            ],
            'OUTSIDE_US' => [
                'states' => ['OUTSIDE_US'],
                'usps' => [
                    'recipient' => 'USCIS Attn: I-130',
                    'address' => 'P.O. Box 4053',
                    'city' => 'Carol Stream', 'state' => 'IL', 'zip' => '60197-4053'
                ],
                'courier' => [
                    'recipient' => 'USCIS Attn: I-130 (Box 4053)',
                    'address' => '2500 Westfield Drive',
                    'city' => 'Elgin', 'state' => 'IL', 'zip' => '60124-7836'
                ],
            ],
        ],
    ],
];
