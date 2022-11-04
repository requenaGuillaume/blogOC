<?php
declare(strict_types=1);

use App\model\UserModel;

date_default_timezone_set('Europe/Paris');

require_once 'vendor/autoload.php';

new UserModel(); // test ok
