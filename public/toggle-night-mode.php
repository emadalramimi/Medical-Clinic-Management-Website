<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\NightModeController;

$nightModeController = new NightModeController();
$nightModeController->toggleNightMode();