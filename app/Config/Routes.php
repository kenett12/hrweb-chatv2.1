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
$routes->group('superadmin', ['filter' => 'auth:superadmin,admin'], function($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('tsr-management', 'Admin\TsrController::index');
    $routes->post('tsr-management/store', 'Admin\TsrController::store'); 
    $routes->post('tsr-management/update/(:num)', 'Admin\TsrController::update/$1');
    $routes->get('tsr-management/delete/(:num)', 'Admin\TsrController::delete/$1');
    $routes->get('client-management', 'Admin\ClientController::index');
    $routes->post('client-management/store', 'Admin\ClientController::store');
    $routes->post('client-management/update/(:num)', 'Admin\ClientController::update/$1');
    $routes->get('client-management/delete/(:num)', 'Admin\ClientController::delete/$1');

    // Sub-Account Routes
    $routes->get('client-management/accounts/(:num)', 'Admin\ClientController::getAccounts/$1');
    $routes->post('client-management/store-account', 'Admin\ClientController::storeAccount');
    $routes->post('client-management/update-account/(:num)', 'Admin\ClientController::updateAccount/$1');
    $routes->get('client-management/delete-account/(:num)', 'Admin\ClientController::deleteAccount/$1');
    
    $routes->get('tickets', 'Admin\TicketController::index');
    $routes->get('tickets/view/(:num)', 'Admin\TicketController::view/$1');
    $routes->post('tickets/reply/(:num)', 'Admin\TicketController::reply/$1');
    $routes->post('tickets/storeCategory', 'Admin\TicketController::storeCategory');
    $routes->get('tickets/deleteCategory/(:num)', 'Admin\TicketController::deleteCategory/$1');
    $routes->post('tickets/updateTicket', 'Admin\TicketController::updateTicket');
    $routes->post('tickets/resolveTicket/(:num)', 'Admin\TicketController::resolveTicket/$1');

    // ── KNOWLEDGE BASE MANAGEMENT ROUTES ───────────────────────
    $routes->get('kb', 'Admin\KBController::index');               // View Manager & Article List
    $routes->post('kb/store', 'Admin\KBController::store');        // Save New Article + Image
    $routes->post('kb/storeCategory', 'Admin\KBController::storeCategory'); // Save New Category
    $routes->get('kb/delete/(:num)', 'Admin\KBController::delete/$1');
    // ────────────────────────────────────────────────────────────

    // ── SYSTEM MANAGEMENT ROUTES ────────────────────────────────
    $routes->get('system-management', 'Admin\SystemController::index');
    $routes->post('system-management/update', 'Admin\SystemController::update');
    // ────────────────────────────────────────────────────────────

    // ────────────────────────────────────────────────────────────
});

    // --- Staff Routes (Previously TSR only) ---
    // Anyone in the employee flowchart structure uses these views
    $routes->group('tsr', ['filter' => 'auth:tsr,tsr_level_1,tl,supervisor,manager,dev,tsr_level_2,it,admin'], function($routes) {
        $routes->get('dashboard', 'Tsr\Dashboard::index');
        $routes->get('chat', 'Tsr\TicketHandler::chats');
        $routes->get('tickets', 'Tsr\TicketHandler::index');
        $routes->get('tickets/live-queue', 'Tsr\TicketHandler::liveQueue'); // New Live Queue Endpoint
        $routes->get('tickets/view/(:num)', 'Tsr\TicketHandler::view/$1');
        $routes->get('tickets/claim/(:num)', 'Tsr\TicketHandler::claim/$1');
        $routes->post('tickets/reply/(:num)', 'Tsr\TicketHandler::reply/$1');
        $routes->post('tickets/update-status/(:num)', 'Tsr\TicketHandler::updateStatus/$1');
        $routes->post('tickets/forward/(:num)', 'Tsr\TicketHandler::forwardTicket/$1');
        $routes->post('tickets/request-closure/(:num)', 'Tsr\TicketHandler::requestClosure/$1');
        $routes->post('tickets/update-remarks/(:num)', 'Tsr\TicketHandler::updateRemarks/$1');
    });

// --- Client Routes ---
$routes->group('client', ['filter' => 'auth:client'], function($routes) {
    $routes->get('dashboard', 'Client\Dashboard::index');
    $routes->get('settings', 'Client\Dashboard::settings');
    $routes->post('settings/update', 'Client\Dashboard::updateSettings');
    
    // ── FIXED CHAT ROUTES ───────────────────────────────────────
    // 1. Match the base 'client/chat' to the new directory view
    $routes->get('chat', 'Client\ChatController::directory'); 
    
    // 2. Match 'client/chat/123' to the actual chat session
    $routes->get('chat/(:num)', 'Client\ChatController::index/$1'); 
    
    // 3. Match the Bot Query with ID
    $routes->post('chat/handleBotQuery/(:num)', 'Client\ChatController::handleBotQuery/$1');

    // 4. NEW: Handle the Thumbs Up / Thumbs Down feedback submission
    $routes->post('chat/submitFeedback', 'Client\ChatController::submitFeedback');

    // 5. NEW: Handle the Talk to Agent request
    $routes->post('chat/requestAgent/(:num)', 'Client\ChatController::requestAgent/$1');
    $routes->post('submit-feedback/(:num)', 'Client\Dashboard::submitFeedback/$1');
    // ────────────────────────────────────────────────────────────

    $routes->get('chat/searchKB', 'Client\ChatController::searchKB');
    $routes->post('chat/send/(:num)', 'Client\ChatController::send/$1');

    // Ticketing System for Clients
    $routes->get('tickets', 'Client\TicketController::index');
    $routes->get('tickets/create', 'Client\TicketController::create');
    $routes->post('tickets/store', 'Client\TicketController::store');
    $routes->get('tickets/view/(:num)', 'Client\TicketController::view/$1');
    $routes->post('tickets/request-closure/(:num)', 'Client\TicketController::requestClosure/$1');
    
    // Duplicate match for safety (from your original code)
    $routes->match(['get', 'post'], 'chat/handleBotQuery/(:num)', 'Client\ChatController::handleBotQuery/$1');
});

// ── UNIFIED GROUP CHAT ROUTES (Accessible by all logged in users) ──
$routes->group('group-chat', ['filter' => 'auth:client,tsr_level_1,tl,supervisor,manager,dev,tsr_level_2,it,admin,superadmin'], function($routes) {
    $routes->get('/', 'Shared\GroupChatController::index');
    $routes->get('room/(:num)', 'Shared\GroupChatController::room/$1');
    $routes->get('members/(:num)', 'Shared\GroupChatController::getMembers/$1');
    $routes->post('create', 'Shared\GroupChatController::create');
    $routes->post('send', 'Shared\GroupChatController::send');
    $routes->post('approve', 'Shared\GroupChatController::approve');
    $routes->post('reject', 'Shared\GroupChatController::reject');
    $routes->post('delete', 'Shared\GroupChatController::delete');
});

// ── SECURE FILE SERVING ───────────────────────────────────────
$routes->get('uploads/tickets/(:any)', 'Shared\FileController::serveTicketAttachment/$1');
// ──────────────────────────────────────────────────────────────    

// APIs (Globally accessible to logged in users via session)
$routes->group('api/notifications', function($routes) {
    $routes->get('fetch', 'NotificationController::fetch');
    $routes->post('read/(:num)', 'NotificationController::markAsRead/$1');
    $routes->post('readAll', 'NotificationController::markAllAsRead');
    $routes->post('clearAll', 'NotificationController::clearAll');
});