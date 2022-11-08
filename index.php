<?php
declare(strict_types=1);

use App\Controller\RegisterController;
use App\Core\Kernel;

date_default_timezone_set('Europe/Paris');

require_once 'vendor/autoload.php';

$testTemplateController = new RegisterController();
$testTemplateController->run();

// $kernel = new Kernel();
// $kernel->process();