<?php
declare(strict_types=1);

use App\Core\Kernel;
use App\Entity\CommentEntity;
use App\Entity\UserEntity;
use App\Repository\UserRepository;

date_default_timezone_set('Europe/Paris');

require_once 'vendor/autoload.php';

// $commentEntity->setAuthorId($userEntity->getId())
// ->setPostId($postEntity->getId())
// ->setContent($faker->paragraph(2))
// ->setStatus(self::COMMENT_STATUS[mt_rand(0, 2)])
// ->setId(mt_rand(1, 1000));
$comment = [
    'authorId' => 10,
    'content' => 'blablablabla',
    'status' => 'waiting',
    'id' => 12
];

$commentEntity = new CommentEntity();
$commentEntity->normalize($comment);
var_dump($commentEntity);

$commentArray = $commentEntity->denormalize();
var_dump($commentArray);
die();


// $kernel = new Kernel();
// $kernel->process();