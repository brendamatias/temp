<?php
// config/config.php

return [
    // Email configuration settings
    'email' => [
        'smtp_host' => 'mail.primerbrasil.com.br', // SMTP host
        'smtp_port' => 587, // SMTP port
        'smtp_username' => 'notificageral@primerbrasil.com.br', // Your email username
        'smtp_password' => 'Senh@2024', // Your email password
        'from_email' => 'notificageral@primerbrasil.com.br', // Default sender email
        'from_name' => 'NotificaGeral', // Default sender name
    ],

    // SMS configuration settings
    'sms' => [
        'provider' => 'YourSMSProvider', // Name of your SMS provider
        'api_key' => 'your-sms-api-key', // API key for the SMS provider
        'api_url' => 'https://api.yoursmsprovider.com/send', // URL for sending SMS
    ],

    // Web Push configuration settings
    'web_push' => [
        'public_key' => 'your-web-push-public-key', // Public key for Web Push
        'private_key' => 'your-web-push-private-key', // Private key for Web Push
        'vapid_email' => 'notificageral@primerbrasil.com.br', // Email associated with VAPID keys
    ],

    // General system settings
    'system' => [
        'app_name' => 'NotificaGeral',
        'default_language' => 'pt-BR', // Default language of the system
        'timezone' => 'America/Sao_Paulo', // Default timezone
    ],
];

