<?php
return [
    'siteView' => [
        'type' => 2,
        'description' => 'Site View',
    ],
    'accountView' => [
        'type' => 2,
        'description' => 'Account View',
    ],
    'accountUpdate' => [
        'type' => 2,
        'description' => 'Account Update',
    ],
    'accountDelete' => [
        'type' => 2,
        'description' => 'Account Delete',
    ],
    'accountTransactionView' => [
        'type' => 2,
        'description' => 'Account Transaction View',
    ],
    'accountTransactionUpdate' => [
        'type' => 2,
        'description' => 'Account Transaction Update',
    ],
    'accountTransactionDelete' => [
        'type' => 2,
        'description' => 'Account Transaction Delete',
    ],
    'userView' => [
        'type' => 2,
        'description' => 'User View',
    ],
    'userUpdate' => [
        'type' => 2,
        'description' => 'User Update',
    ],
    'userDelete' => [
        'type' => 2,
        'description' => 'User Delete',
    ],
    'houseView' => [
        'type' => 2,
        'description' => 'House View',
    ],
    'houseUpdate' => [
        'type' => 2,
        'description' => 'House Update',
    ],
    'houseDelete' => [
        'type' => 2,
        'description' => 'House Delete',
    ],
    'flatView' => [
        'type' => 2,
        'description' => 'Flat View',
    ],
    'flatUpdate' => [
        'type' => 2,
        'description' => 'Flat Update',
    ],
    'flatDelete' => [
        'type' => 2,
        'description' => 'Flat Delete',
    ],
    'messageView' => [
        'type' => 2,
        'description' => 'Message View',
    ],
    'messageUpdate' => [
        'type' => 2,
        'description' => 'Message Update',
    ],
    'messageDelete' => [
        'type' => 2,
        'description' => 'Message Delete',
    ],
    'masterRequestView' => [
        'type' => 2,
        'description' => 'Master Request View',
    ],
    'masterRequestUpdate' => [
        'type' => 2,
        'description' => 'Master Request Update',
    ],
    'masterRequestDelete' => [
        'type' => 2,
        'description' => 'Master Request Delete',
    ],
    'invoiceView' => [
        'type' => 2,
        'description' => 'Invoice View',
    ],
    'invoiceUpdate' => [
        'type' => 2,
        'description' => 'Invoice Update',
    ],
    'invoiceDelete' => [
        'type' => 2,
        'description' => 'Invoice Delete',
    ],
    'counterDataView' => [
        'type' => 2,
        'description' => 'Counter Data View',
    ],
    'counterDataUpdate' => [
        'type' => 2,
        'description' => 'Counter Data Update',
    ],
    'counterDataDelete' => [
        'type' => 2,
        'description' => 'Counter Data Delete',
    ],
    'websiteUpdate' => [
        'type' => 2,
        'description' => 'Website Update',
    ],
    'serviceView' => [
        'type' => 2,
        'description' => 'Service View',
    ],
    'serviceUpdate' => [
        'type' => 2,
        'description' => 'Service Update',
    ],
    'serviceDelete' => [
        'type' => 2,
        'description' => 'Service Delete',
    ],
    'tariffView' => [
        'type' => 2,
        'description' => 'Tariff View',
    ],
    'tariffUpdate' => [
        'type' => 2,
        'description' => 'Tariff Update',
    ],
    'tariffDelete' => [
        'type' => 2,
        'description' => 'Tariff Delete',
    ],
    'roleUpdate' => [
        'type' => 2,
        'description' => 'Role Update',
    ],
    'userAdminView' => [
        'type' => 2,
        'description' => 'User Admin View',
    ],
    'userAdminUpdate' => [
        'type' => 2,
        'description' => 'User Admin Update',
    ],
    'userAdminDelete' => [
        'type' => 2,
        'description' => 'User Admin Delete',
    ],
    'payCompanyUpdate' => [
        'type' => 2,
        'description' => 'Pay Company Update',
    ],
    'site' => [
        'type' => 2,
        'description' => 'Site',
        'children' => [
            'siteView',
        ],
    ],
    'account' => [
        'type' => 2,
        'description' => 'Account',
        'children' => [
            'accountView',
            'accountUpdate',
            'accountDelete',
        ],
    ],
    'accountTransaction' => [
        'type' => 2,
        'description' => 'Account Transaction',
        'children' => [
            'accountTransactionView',
            'accountTransactionUpdate',
            'accountTransactionDelete',
        ],
    ],
    'user' => [
        'type' => 2,
        'description' => 'User',
        'children' => [
            'userView',
            'userUpdate',
            'userDelete',
        ],
    ],
    'house' => [
        'type' => 2,
        'description' => 'House',
        'children' => [
            'houseView',
            'houseUpdate',
            'houseDelete',
        ],
    ],
    'flat' => [
        'type' => 2,
        'description' => 'Flat',
        'children' => [
            'flatView',
            'flatUpdate',
            'flatDelete',
        ],
    ],
    'message' => [
        'type' => 2,
        'description' => 'Message',
        'children' => [
            'messageView',
            'messageUpdate',
            'messageDelete',
        ],
    ],
    'masterRequest' => [
        'type' => 2,
        'description' => 'Master Request',
        'children' => [
            'masterRequestView',
            'masterRequestUpdate',
            'masterRequestDelete',
        ],
    ],
    'invoice' => [
        'type' => 2,
        'description' => 'Invoice',
        'children' => [
            'invoiceView',
            'invoiceUpdate',
            'invoiceDelete',
        ],
    ],
    'counterData' => [
        'type' => 2,
        'description' => 'Counter Data',
        'children' => [
            'counterDataView',
            'counterDataUpdate',
            'counterDataDelete',
        ],
    ],
    'website' => [
        'type' => 2,
        'description' => 'Website',
        'children' => [
            'websiteUpdate',
        ],
    ],
    'system' => [
        'type' => 2,
        'description' => 'System',
        'children' => [
            'serviceView',
            'serviceUpdate',
            'serviceDelete',
            'tariffView',
            'tariffUpdate',
            'tariffDelete',
            'roleUpdate',
            'userAdminView',
            'userAdminUpdate',
            'userAdminDelete',
            'payCompanyUpdate',
        ],
    ],
    'service' => [
        'type' => 2,
        'description' => 'Service',
        'children' => [
            'serviceView',
            'serviceUpdate',
            'serviceDelete',
        ],
    ],
    'tariff' => [
        'type' => 2,
        'description' => 'Tariff',
        'children' => [
            'tariffView',
            'tariffUpdate',
            'tariffDelete',
        ],
    ],
    'role' => [
        'type' => 2,
        'description' => 'Role',
        'children' => [
            'roleUpdate',
        ],
    ],
    'userAdmin' => [
        'type' => 2,
        'description' => 'User Admin',
        'children' => [
            'userAdminView',
            'userAdminUpdate',
            'userAdminDelete',
        ],
    ],
    'payCompany' => [
        'type' => 2,
        'description' => 'Pay Company',
        'children' => [
            'payCompanyUpdate',
        ],
    ],
    'electrician' => [
        'type' => 1,
        'children' => [
            'masterRequest',
            'counterData',
        ],
    ],
    'plumber' => [
        'type' => 1,
        'children' => [
            'masterRequest',
            'counterData',
            'role',
            'userAdmin',
        ],
    ],
    'accountant' => [
        'type' => 1,
        'children' => [
            'account',
            'accountTransaction',
            'user',
            'house',
            'flat',
            'message',
            'invoice',
            'counterData',
            'service',
            'tariff',
            'payCompany',
        ],
    ],
    'manager' => [
        'type' => 1,
        'children' => [
            'site',
            'account',
            'accountTransaction',
            'user',
            'house',
            'flat',
            'message',
            'masterRequest',
            'invoice',
            'counterData',
            'website',
            'service',
            'tariff',
            'userAdmin',
            'payCompany',
        ],
    ],
    'admin' => [
        'type' => 1,
        'children' => [
            'site',
            'account',
            'accountTransaction',
            'user',
            'house',
            'flat',
            'message',
            'masterRequest',
            'invoice',
            'counterData',
            'website',
            'service',
            'tariff',
            'role',
            'userAdmin',
            'payCompany',
        ],
    ],
];
