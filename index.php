<?php
declare(strict_types=1);

use App\Core\Kernel;
use App\Repository\UserRepository;

date_default_timezone_set('Europe/Paris');

require_once 'vendor/autoload.php';

$UserRepository = new UserRepository();
$UserRepository->run();

// $kernel = new Kernel();
// $kernel->process();