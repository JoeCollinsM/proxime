<?php
return [
    'sms_verification' => true,
    'email_verification' => true,
    'sms_notification' => true,
    'sms_via' => 'twilio', // twilio|other
    'email_notification' => true,
    'fcm_notification' => true,
    'forgot_password_via' => 'email', // email or sms
    'decimals' => 2, // Digits after decimal point
    'default_user_status' => 1,
    'default_vendor_status' => 0,
    'faq_text' => 'Write Something...',
    'toc_text' => 'Write Something...',
    'delivery' => [
        'type' => 'fixed', // Fixed Or Custom
        'custom_percentage' => 5, // If type is fixed then use this custom_percentage of order amount
    ],
    'app' => [
        'app_version' => [
            'android' => 1,
            'ios' => 1,
        ],
        'color' => [
            'color_primary' => '',
            'color_primary_dark' => '',
            'color_accent' => '',
            'button_color_1' => '',
            'button_color_2' => '',
        ],
        'api_key' => [
            'google_map_api_key' => '',
            'direction_api_key' => '',
        ],
        'splash' => [
            'logo' => '',
        ],
    ]
];
