<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --- Public Routes ---
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('auth/authenticate', 'Auth::authenticate');
$routes->get('logout', 'Auth::logout');
$routes->get('seed', 'Auth::seed');
$routes->get('privacy-terms', 'Auth::privacyTerms');
$routes->get('support', 'Auth::support');
$routes->get('forgot-password', 'Auth::forgotPassword');

// --- Standalone TV Queue Display ---
$routes->get('queue-display', 'QueueDisplayController::index');
$routes->get('queue-display/data', 'QueueDisplayController::data');

// --- Superadmin Routes ---
$routes->group('superadmin', ['filter' => 'auth:superadmin'], function($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('tsr-management', 'Admin\TsrController::index');
    $routes->post('tsr-management/store', 'Admin\TsrController::store'); 
    $routes->get('client-management', 'Admin\ClientController::index');
    $routes->post('client-management/store', 'Admin\ClientController::store');
    $routes->post('client-management/update/(:num)', 'Admin\ClientController::update/$1');
    $routes->get('client-management/delete/(:num)', 'Admin\ClientController::delete/$1');
    $routes->get('tickets', 'Admin\TicketController::index');
    $routes->get('tickets/view/(:num)', 'Admin\TicketController::view/$1');
    $routes->post('tickets/reply/(:num)', 'Admin\TicketController::reply/$1');

    // ── KNOWLEDGE BASE MANAGEMENT ROUTES ───────────────────────
    $routes->get('kb', 'Admin\KBController::index');               // View Manager & Article List
    $routes->post('kb/store', 'Admin\KBController::store');        // Save New Article + Image
    $routes->post('kb/storeCategory', 'Admin\KBController::storeCategory'); // Save New Category
    $routes->get('kb/delete/(:num)', 'Admin\KBController::delete/$1');
    // ────────────────────────────────────────────────────────────
});

    // --- TSR Routes ---
    $routes->group('tsr', ['filter' => 'auth:tsr'], function($routes) {
        $routes->get('dashboard', 'Tsr\Dashboard::index');
        $routes->get('tickets', 'Tsr\TicketHandler::index');
        $routes->get('tickets/live-queue', 'Tsr\TicketHandler::liveQueue'); // New Live Queue Endpoint
        $routes->get('tickets/view/(:num)', 'Tsr\TicketHandler::view/$1');
        $routes->get('tickets/claim/(:num)', 'Tsr\TicketHandler::claim/$1');
        $routes->post('tickets/reply/(:num)', 'Tsr\TicketHandler::reply/$1');
        $routes->post('tickets/update-status/(:num)', 'Tsr\TicketHandler::updateStatus/$1');
    });

// --- Client Routes ---
$routes->group('client', ['filter' => 'auth:client'], function($routes) {
    $routes->get('dashboard', 'Client\Dashboard::index');
    
    // ── FIXED CHAT ROUTES ───────────────────────────────────────
    // 1. Match the base 'client/chat' to the new directory view
    $routes->get('chat', 'Client\ChatController::directory'); 
    
    // 2. Match 'client/chat/123' to the actual chat session
    $routes->get('chat/(:num)', 'Client\ChatController::index/$1'); 
    
    // 3. Match the Bot Query with ID
    $routes->post('chat/handleBotQuery/(:num)', 'Client\ChatController::handleBotQuery/$1');

    // 4. NEW: Handle the Thumbs Up / Thumbs Down feedback submission
    $routes->post('chat/submitFeedback', 'Client\ChatController::submitFeedback');
    // ────────────────────────────────────────────────────────────

    $routes->get('chat/searchKB', 'Client\ChatController::searchKB');
    $routes->post('chat/send/(:num)', 'Client\ChatController::send/$1');

    // Ticketing System for Clients
    $routes->get('tickets', 'Client\TicketController::index');
    $routes->get('tickets/create', 'Client\TicketController::create');
    $routes->post('tickets/store', 'Client\TicketController::store');
    $routes->get('tickets/view/(:num)', 'Client\TicketController::view/$1');
    
    // Duplicate match for safety (from your original code)
    $routes->match(['get', 'post'], 'chat/handleBotQuery/(:num)', 'Client\ChatController::handleBotQuery/$1');
});

// APIs (Globally accessible to logged in users via session)
$routes->group('api/notifications', function($routes) {
    $routes->get('fetch', 'NotificationController::fetch');
    $routes->post('read/(:num)', 'NotificationController::markAsRead/$1');
    $routes->post('readAll', 'NotificationController::markAllAsRead');
    $routes->post('clearAll', 'NotificationController::clearAll');
});