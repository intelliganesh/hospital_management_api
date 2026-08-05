<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark'     => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses'          => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend'       => [
        'key' => env('RESEND_KEY'),
    ],

    'slack'        => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'hospital_app' => [
        'reset_password_url' => env('RESET_PASSWORD_URL'),
        'encrypt_key'        => env('ENCRYPT_KEY'),
    ],

    'daily'=>[
        'key'=>env('DAILY_API_KEY'),
        'domain'=>env('DAILY_DOMAIN')
    ],

    'whatsapp' => [
        'token' => env('WHATSAPP_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'verify_token' => env('WHATSAPP_VERIFY_TOKEN'),
        'templates' => [
            'patient_appointment_confirmation' => [
                'name' => env('WHATSAPP_PATIENT_APPOINTMENT_CONFIRMATION_TEMPLATE', 'patient_appointment_confirmation'),
                'params' => [
                    'patient_name',
                    'doctor_name',
                    'date',
                    'time',
                    'meeting_link',
                ],
            ],
            'doctor_appointment_confirmation' => [
                'name' => env('WHATSAPP_DOCTOR_APPOINTMENT_CONFIRMATION_TEMPLATE', 'doctor_appointment_confirmation'),
                'params' => [
                    'doctor_name',
                    'patient_name',
                    'patient_age',
                    'patient_gender',
                    'country_of_residence',
                    'visit_type',
                    'reason_for_visit',
                    'date',
                    'time',
                    'meeting_link',
                ],
            ],
        ],
     ],

];
