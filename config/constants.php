<?php

return [
    'ACTIVE' => 1,
    'INACTIVE' => 0,
    'ORDER_STATUS_SUCCESS' => 1,
    'ORDER_STATUS_PENDING' => 2,
    'ORDER_STATUS_FAILED' => 3,
    'ORDER_STATUS_CANCELLED' => 4,
    'ORDER_STATUS_TEXT' => [
        1 => 'Success',
        2 => 'Pending',
        3 => 'Failed',
        4 => 'Cancelled',
    ],

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

    'PAYOS' => [
        'SUCCESS_CODE' => '00',
    ],

    'ENV' => [
        'VNP_HASH_SECRET' => env('VNP_HASH_SECRET'),
        'VNP_TMN_CODE' => env('VNP_TMN_CODE'),
        'VNP_URL' => env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    ],
];