<?php

namespace App\Controller;

use Faker\Factory;
use App\Entity\PostEntity;
use App\Entity\UserEntity;
use App\Entity\CommentEntity;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use DateTime;

class FixturesController
{
    private const POST_STATUS = ['draft', 'online'];
    private const COMMENT_STATUS = ['waiting', 'valid', 'invalid'];

    public function run(): void
    {
        echo 'Test Router Fixtures';

        $faker = Factory::create();

        $userRepository = new UserRepository();
        $postRepository = new PostRepository();
        $commentRepository = new CommentRepository();        

        // FAIRE AUTREMENT !

        for($a = 1; $a < 3; $a++){
            $adminEntity = new UserEntity();
            $adminEntity->setMail($faker->email())
                        ->setPseudo($faker->username())
                        ->setPassword($faker->password())
                        ->setRole('admin')
                        ->setId(mt_rand(1, 1000));
                       
            $admin = $adminEntity->denormalize();
            $userRepository->create($admin);
            
            for($p = 1; $p < 5; $p++){
                $postEntity = new PostEntity();
                $postEntity->setAuthorId($adminEntity->getId())
                           ->setContent($faker->paragraph(5))
                           ->setHead($faker->paragraph(2))
                           ->setTitle($faker->words(3, true))
                           ->setStatus(self::POST_STATUS[mt_rand(0, 1)])
                           ->setSlug($faker->slug())
                           ->setId(mt_rand(1, 1000));

                $post = $postEntity->denormalize();
                $postRepository->create($post);

                for($u = 1; $u < 2; $u++){
                    $userEntity = new UserEntity();
                    $userEntity->setMail($faker->email())
                               ->setPseudo($faker->username())
                               ->setPassword($faker->password())
                               ->setRole('user')
                               ->setId(mt_rand(1, 1000));

                    $user = $userEntity->denormalize();
                    $userRepository->create($user);

                    for($c = 1; $c < mt_rand(0, 4); $c++){
                        $commentEntity = new CommentEntity();
                        $commentEntity->setAuthorId($userEntity->getId())
                                      ->setPostId($postEntity->getId())
                                      ->setContent($faker->paragraph(2))
                                      ->setStatus(self::COMMENT_STATUS[mt_rand(0, 2)])
                                      ->setId(mt_rand(1, 1000));

                        $comment = $commentEntity->denormalize();
                        $commentRepository->create($comment);
                    }
                }                
            }
        }

        echo '<br>Fixtures done !';
    }

}