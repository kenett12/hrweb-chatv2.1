<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
chdir(FCPATH);
$pathsConfig = FCPATH . '../app/Config/Paths.php';
require $pathsConfig;
$paths = new Config\Paths();
$app = require rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';

$model = model('App\Models\TicketModel');
echo "All tickets: " . count($model->findAll()) . "\n";
echo "With search 'a': " . count($model->getFilteredTickets(['search' => 'a'])) . "\n";
echo "With status 'Open': " . count($model->getFilteredTickets(['status' => 'Open'])) . "\n";
