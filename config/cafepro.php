<?php

return [
    'business_name'         => env('CAFE_BUSINESS_NAME', 'Cafe Pro'),
    'branch_name'           => env('CAFE_BRANCH_NAME', 'Main Branch'),
    'address'               => env('CAFE_ADDRESS', ''),
    'phone'                 => env('CAFE_PHONE', ''),
    'tax_number'            => env('CAFE_TAX_NUMBER', ''),
    'receipt_footer'        => env('CAFE_RECEIPT_FOOTER', 'Thank you for your visit!'),

    // Toggle stock validation before checkout
    'stock_check_enabled'   => env('CAFE_STOCK_CHECK', true),

    // Low stock notification threshold (used in dashboard)
    'low_stock_alert'       => env('CAFE_LOW_STOCK_ALERT', true),
];
