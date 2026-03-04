<?php

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['message'] = 'test';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest'; // Simulate AJAX

// We'll mimic the index.php setup to run the request
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
chdir(FCPATH);

// Boot
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

require_once SYSTEMPATH . 'Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv(ROOTPATH))->load();

$app = Config\Services::codeigniter();
$app->initialize();
$app->setContext('web');

// Mock a Request
$request = \Config\Services::request();
$request->setMethod('post');
$request->setGlobal('post', ['message' => 'test']);

// Mock the route string
$_SERVER['REQUEST_URI'] = '/client/chat/handleBotQuery/7';

try {
    $response = $app->run($routes, true);
    echo "RESPONSE BODY:\n--------------\n";
    echo $response->getBody();
} catch (\Throwable $e) {
    echo "EXCEPTION CAUGHT:\n--------------\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
