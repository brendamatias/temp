<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rotas de autenticação
$routes->get('/', 'AuthController::login');
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::login');
$routes->get('/register', 'AuthController::register');
$routes->post('/register', 'AuthController::register');
$routes->get('/logout', 'AuthController::logout');

// Rotas protegidas por autenticação
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/dashboard', 'DashboardController::index');
    
    // Rotas de aplicativos
    $routes->get('/applications', 'ApplicationController::index');
    $routes->get('/applications/create', 'ApplicationController::create');
    $routes->post('/applications', 'ApplicationController::store');
    $routes->get('/applications/(:num)', 'ApplicationController::show/$1');
    $routes->get('/applications/(:num)/edit', 'ApplicationController::edit/$1');
    $routes->post('/applications/(:num)', 'ApplicationController::update/$1');
    $routes->delete('/applications/(:num)', 'ApplicationController::delete/$1');
    
    // Rotas de configuração de canais
    // Web Push
    $routes->get('/channels/webpush/(:num)', 'WebPushController::configure/$1');
    $routes->post('/channels/webpush/(:num)/save', 'WebPushController::save/$1');
    $routes->post('/channels/webpush/generate-vapid-keys', 'WebPushController::generateVapidKeys');
    $routes->post('/channels/webpush/(:num)/test', 'WebPushController::testNotification/$1');
    $routes->get('/channels/webpush/(:num)/disable', 'WebPushController::disable/$1');
    
    // Email
    $routes->get('/channels/email/(:num)', 'EmailController::configure/$1');
    $routes->post('/channels/email/(:num)/save', 'EmailController::save/$1');
    $routes->post('/channels/email/(:num)/test-connection', 'EmailController::testConnection/$1');
    $routes->post('/channels/email/(:num)/send-test', 'EmailController::sendTestEmail/$1');
    $routes->get('/channels/email/(:num)/preview-template', 'EmailController::previewTemplate/$1');
    $routes->get('/channels/email/(:num)/disable', 'EmailController::disable/$1');
    
    // SMS
    $routes->get('/channels/sms/(:num)/configure', 'SmsController::configure/$1');
    $routes->post('/channels/sms/(:num)/save', 'SmsController::save/$1');
    $routes->post('/channels/sms/(:num)/test-connection', 'SmsController::testConnection/$1');
    $routes->post('/channels/sms/(:num)/send-test', 'SmsController::sendTestSms/$1');
    $routes->get('/channels/sms/(:num)/disable', 'SmsController::disable/$1');
    $routes->get('/channels/sms/provider-info', 'SmsController::getProviderInfo');
    
    // Rotas de histórico
    $routes->get('/applications/(:num)/history', 'NotificationController::history/$1');
    $routes->get('/applications/(:num)/history/export/pdf', 'NotificationController::exportPdf/$1');
    $routes->get('/applications/(:num)/history/export/excel', 'NotificationController::exportExcel/$1');
    
    // Rotas de envio manual
    $routes->get('/applications/(:num)/send', 'NotificationController::sendForm/$1');
    $routes->post('/applications/(:num)/send', 'NotificationController::send/$1');
});

// API Routes
$routes->group('api', function($routes) {
    // Documentação da API
    $routes->get('docs', 'Api\NotificationApiController::docs');
    
    // Envio de notificações
    $routes->post('notifications/send', 'Api\NotificationApiController::send');
    $routes->post('notifications/send/bulk', 'Api\NotificationApiController::sendBulk');
    
    // Histórico e estatísticas
    $routes->get('notifications/history', 'Api\NotificationApiController::history');
    $routes->get('notifications/stats', 'Api\NotificationApiController::stats');
    $routes->get('notifications/(:num)', 'Api\NotificationApiController::details/$1');
    
    // Reenvio de notificações
    $routes->post('notifications/(:num)/retry', 'Api\NotificationApiController::retry/$1');
    
    // Configurações de canais
    $routes->get('channels/config', 'Api\NotificationApiController::channelConfig');
    
    // Webhooks
    $routes->post('webhooks/status', 'Api\NotificationApiController::webhook');
});
