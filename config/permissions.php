<?php

return [

    'roles' => [
        'admin'      => 'مدير',
        'doctor'     => 'طبيب',
        'secretary'  => 'سكرتير/ة',
        'accountant' => 'محاسب',
    ],

    'permissions' => [
        'admin' => [
            'patients.view', 'patients.manage', 'appointments',
            'visits.view', 'visits.manage', 'chart', 'billing', 'users',
        ],
        'doctor' => [
            'patients.view', 'patients.manage', 'appointments',
            'visits.view', 'visits.manage', 'chart',
        ],
        'secretary' => [
            'patients.view', 'patients.manage', 'appointments',
        ],
        'accountant' => [
            'patients.view', 'visits.view', 'billing',
        ],
    ],
];