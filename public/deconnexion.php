<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\SecurityController;

$controller = new SecurityController();
$controller->logout();