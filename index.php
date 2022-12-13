<?php
declare(strict_types=1);

use App\Core\Kernel;
use App\Repository\PostRepository;
use App\Repository\UserRepository;

date_default_timezone_set('Europe/Paris');

require_once 'vendor/autoload.php';

$userRepository = new UserRepository();

$postRepository = new PostRepository();

var_dump($userRepository->findAll());die();

// $kernel = new Kernel();
// $kernel->process();