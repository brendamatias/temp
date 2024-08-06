<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

$routes = Services::routes();

if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

$routes->setDefaultNamespace('App\\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// ========================================
// ROTAS WEB (MVC)
// ========================================


// Autenticação
$routes->get('login', 'Auth::login');
$routes->get('sso', 'Auth::sso');
$routes->post('login', 'Auth::doLogin');
$routes->get('logout', 'Auth::logout');

$routes->group('', ['namespace' => 'App\\Controllers', 'filter' => 'auth2'], function($routes) {
    // Página inicial
    $routes->get('/', 'Home::index');

    // Negociações
    $routes->get('deals', 'Deals::index');
    $routes->get('deals/search', 'Deals::search');
    $routes->get('deals/create', 'Deals::create');
    $routes->post('deals/create', 'Deals::store');
    $routes->get('deals/(:num)', 'Deals::show/$1');
    $routes->get('deals/(:num)/edit', 'Deals::edit/$1');
    $routes->post('deals/(:num)/edit', 'Deals::update/$1');

    // Mensagens
    $routes->post('deals/(:num)/message', 'Messages::create/$1');
    $routes->get('deals/(:num)/message/(:num)/delete', 'Messages::delete/$1/$2');

    // Ofertas
    $routes->post('deals/(:num)/bid', 'Bids::create/$1');
    $routes->get('deals/(:num)/bid/(:num)/accept', 'Bids::accept/$1/$2');
    $routes->get('deals/(:num)/bid/(:num)/reject', 'Bids::reject/$1/$2');
    $routes->get('deals/(:num)/bid/(:num)/cancel', 'Bids::cancel/$1/$2');

    // Minhas negociações
    $routes->get('my-deals', 'MyDeals::index');
    $routes->get('my-bids', 'MyDeals::myBids');

    // Convites
    $routes->get('invites', 'Invites::index');
    $routes->get('invites/create', 'Invites::create');
    $routes->post('invites/create', 'Invites::store');
    
    // API endpoints para convites (AJAX)
    $routes->get('invites/details/(:num)', 'Invites::details/$1');
    $routes->post('invites/resend/(:num)', 'Invites::resend/$1');
    $routes->post('invites/cancel/(:num)', 'Invites::cancel/$1');
});

// ========================================
// ROTAS DA API (com prefixo /api)
// ========================================

// Autenticação da API
$routes->post('api/authenticate', 'Api\Authenticate::login');
$routes->post('api/authenticate/sso', 'Api\Authenticate::sso');

// Endpoints da API (protegidos por autenticação)
$routes->group('api', ['namespace' => 'App\\Controllers\\Api', 'filter' => 'auth'], function($routes) {
    $routes->post('user', 'User::create');
    $routes->get('user/(:num)', 'User::show/$1');
    $routes->put('user/(:num)', 'User::update/$1');
    $routes->post('deal', 'Deal::create');
    $routes->get('deal/(:num)', 'Deal::show/$1');
    $routes->put('deal/(:num)', 'Deal::update/$1');
    $routes->post('deal/search', 'Deal::search');
    $routes->post('deal/(:num)/bid', 'Bid::create/$1');
    $routes->get('deal/(:num)/bid', 'Bid::listBids/$1');
    $routes->get('deal/(:num)/bid/(:num)', 'Bid::show/$1/$2');
    $routes->put('deal/(:num)/bid/(:num)', 'Bid::update/$1/$2');
    $routes->post('deal/(:num)/message', 'Message::create/$1');
    $routes->get('deal/(:num)/message', 'Message::listMessages/$1');
    $routes->get('deal/(:num)/message/(:num)', 'Message::show/$1/$2');
    $routes->put('deal/(:num)/message/(:num)', 'Message::update/$1/$2');
    $routes->post('deal/(:num)/delivery', 'Delivery::create/$1');
    $routes->get('deal/(:num)/delivery', 'Delivery::show/$1');
    $routes->post('user/(:num)/invite', 'Invite::create/$1');
    $routes->get('user/(:num)/invite', 'Invite::listInvites/$1');
    $routes->get('user/(:num)/invite/(:num)', 'Invite::show/$1/$2');
    $routes->put('user/(:num)/invite/(:num)', 'Invite::update/$1/$2');
});

if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
