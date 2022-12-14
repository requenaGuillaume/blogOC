<?php
declare(strict_types=1);

use App\Core\Kernel;

date_default_timezone_set('Europe/Paris');

require_once 'vendor/autoload.php';

session_start();

$kernel = new Kernel();
$kernel->process();