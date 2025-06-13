<?php

return [
    'ACTIVE' => 1,
    'INACTIVE' => 0,

    'VNP' => [
        'PREFIX' => 'vnp_',
        'FIELD' => [
            'HASH' => 'vnp_SecureHash',
            'RESPONSE_CODE' => 'vnp_ResponseCode',
            'TRANSACTION_ID' => 'vnp_TransactionNo',
            'MESSAGE' => 'vnp_Message',
        ],
        'RESPONSE_CODE' => [
            'SUCCESS' => '00',
            'INVALID_SIGNATURE' => '97',
        ],
        'ORDER_TYPE' => [
            'BILL_PAYMENT' => 'billpayment',
            'TOP_UP' => 'topup',
            'OTHER' => 'other',
        ],
        'LOCALE' => [
            'VIETNAMESE' => 'vn',
            'ENGLISH' => 'en',
        ],
    ],

    'ENV' => [
        'VNP_HASH_SECRET' => env('VNP_HASH_SECRET', 'G54RJ363DO7T0OH4I5JS7AKJAEWVOC07'),
        'VNP_TMN_CODE' => env('VNP_TMN_CODE', 'D1M7CNW3'),
        'VNP_URL' => env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    ],
];