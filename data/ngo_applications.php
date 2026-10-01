<?php

return [
    [
        'id' => 1,
        'application_id' => 'NGO-2026-00124',
        'organization_name' => 'Nari Shakti Sangathan',
        'application_date' => '12 September 2026',
        'status' => 'pending',
        'ngo_type' => 'Non-Profit Organization',
        'primary_cause' => 'Women & Children',
        'contact_person' => 'Priya Sharma',
        'designation' => 'Founder',
        'email' => 'contact@example.org',
        'phone' => '+91 98765 43210',
        'website' => 'https://example.org',

        'registration_number' => 'REG-123456',
        'established_date' => '2014',
        'address' => '123 Main Street',
        'city' => 'Mumbai',
        'state' => 'Maharashtra',
        'pincode' => '400001',

        'mission' => 'Empowering women and children through education and healthcare support.',
        'service_areas' => [
            'Mumbai',
            'Pune',
            'Nashik',
        ],

        'impact' => 'Supported over 500 women and 200 children in the past year.',

        'documents' => [
            [
                'name' => 'Registration Certificate',
                'type' => 'PDF Document',
                'status' => 'submitted',
            ],
            [
                'name' => 'Tax Document',
                'type' => 'PDF Document',
                'status' => 'submitted',
            ],
            [
                'name' => 'Address Proof',
                'type' => 'PDF Document',
                'status' => 'submitted',
            ],
            [
                'name' => 'Supporting Document',
                'type' => 'PDF Document',
                'status' => 'submitted',
            ],
        ],
    ],
    [
        'id' => 2,
        'application_id' => 'NGO-2026-00125',
        'organization_name' => 'Green Earth Care',
        'application_date' => '10 September 2026',
        'status' => 'approved',
        'ngo_type' => 'Trust',
        'primary_cause' => 'Environment',
        'contact_person' => 'Ravi Patel',
        'designation' => 'Director',
        'email' => 'contact@greenearthcare.org',
        'phone' => '+91 9800000002',
        'website' => 'https://greenearthcare.org',

        'registration_number' => 'REG-987654',
        'established_date' => '2015',
        'address' => '45 Green Avenue',
        'city' => 'Surat',
        'state' => 'Gujarat',
        'pincode' => '395001',

        'mission' => 'To promote environmental sustainability through urban afforestation and active community participation in waste management.',
        'service_areas' => [
            'Surat',
            'Navsari',
        ],

        'impact' => 'Planted 10,000 trees and cleared 50 tons of waste.',

        'documents' => [
            [
                'name' => 'Registration Certificate',
                'type' => 'PDF Document',
                'status' => 'submitted',
            ],
            [
                'name' => 'Tax Document',
                'type' => 'PDF Document',
                'status' => 'submitted',
            ],
        ],
    ],
    [
        'id' => 3,
        'application_id' => 'NGO-2026-00126',
        'organization_name' => 'Hope Health Society',
        'application_date' => '05 September 2026',
        'status' => 'rejected',
        'rejection_reason' => 'Missing valid FCRA registration documents and incomplete impact assessment report.',
        'ngo_type' => 'Society',
        'primary_cause' => 'Healthcare',
        'contact_person' => 'Dr. Amit Desai',
        'designation' => 'Secretary',
        'email' => 'contact@hopehealth.org',
        'phone' => '+91 9800000003',
        'website' => 'https://hopehealth.org',

        'registration_number' => 'REG-112233',
        'established_date' => '2008',
        'address' => 'Health Clinic Rd',
        'city' => 'Ahmedabad',
        'state' => 'Gujarat',
        'pincode' => '380001',

        'mission' => 'To ensure accessible and quality healthcare for marginalized communities in remote regions.',
        'service_areas' => [
            'Ahmedabad',
            'Rural Gujarat',
        ],

        'impact' => 'Treated over 15,000 patients in rural camps.',

        'documents' => [
            [
                'name' => 'Registration Certificate',
                'type' => 'PDF Document',
                'status' => 'submitted',
            ],
            [
                'name' => 'Address Proof',
                'type' => 'PDF Document',
                'status' => 'submitted',
            ],
        ],
    ]
];
