<?php

namespace App\Controller;

use PDO;
use Faker\Factory;
use App\Entity\PostEntity;
use App\Entity\UserEntity;
use App\Entity\CommentEntity;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use App\Database\PDOConnection;
use App\Service\NormalizerService;

class FixturesController
{
    protected ?PDO $pdo = null;

    private const POST_STATUS = [
        PostEntity::STATUS_ONLINE, 
        PostEntity::STATUS_DRAFT
    ];

    private const COMMENT_STATUS = [
        CommentEntity::STATUS_VALID, 
        CommentEntity::STATUS_WAITING, 
        CommentEntity::STATUS_INVALID
    ];

    public function __construct() 
    {
        $pdoConnection = new PDOConnection();
        $this->pdo = $pdoConnection->getPdo();
        $this->faker = Factory::create();
        $this->userRepository = new UserRepository();
        $this->postRepository = new PostRepository();
        $this->commentRepository = new CommentRepository();
        $this->normalizer = new NormalizerService();

        $this->insertUserSql = "INSERT INTO user (role, pseudo, mail, password) VALUES (:role, :pseudo, :mail, :password)";

        $this->insertPostSql = "INSERT INTO post (author_id, title, head, content, slug, status, last_update) 
                                VALUES (:author_id, :title, :head, :content, :slug, :status, NOW())";

        $this->updatePostSql = "UPDATE post SET comments = :comments WHERE id = :post_id";

        $this->insertCommentSql = "INSERT INTO comment (post_id, author_id, content, status, created_at) 
                                   VALUES (:post_id, :author_id, :content, :status, NOW())";
    }


    public function run(): void
    {
        // TODO : This route must be accessible only by admin, else redirect

        echo 'Beginning of the Fixtures<br><br>';

        $this->createUsers(UserEntity::ROLE_ADMIN, 3);
        echo 'Admin Fixtures Done !<br>';

        $this->createUsers(UserEntity::ROLE_USER, 10);
        echo 'Users Fixtures Done !<br>';

        $this->createPosts(3);
        echo 'Posts Fixtures Done !<br>';

        $this->createComments();
        echo 'Comments Fixtures Done !<br>';

        echo '<br>End of the Fixtures';
    }

    
    /**
     * /!\ Danger : Use in dev/test only and be careful, 
     * This method will drop all data stored in database !
     * If you let it commented, it will just be a redirection
     */
    // public function delete()
    // {
    //     echo 'Starting to drop data<br><br>';

    //     $this->emptyTable('comment');
    //     echo 'All comments removed from database !<br>';

    //     $this->emptyTable('post');
    //     echo 'All posts removed from database !<br>';

    //     $this->emptyTable('user');
    //     echo 'All users removed from database !<br>';

    //     echo '<br>Database is now empty';
    // }


    // ========================== PRIVATE FUNCTIONS ========================== \\

    private function emptyTable(string $table): void
    {
        $this->pdo->exec("DELETE FROM $table");
    }

    private function createUsers(string $role, int $number): void
    {
        for($a = 0; $a < $number; $a++){
            $randomNumber = mt_rand(0, 100);

            $params = [
                ':role' => $role,
                ':pseudo' => $randomNumber . $this->faker->userName(),
                ':mail' => $randomNumber . $this->faker->email(),
                ':password' => password_hash($this->faker->word(), PASSWORD_DEFAULT)
            ];

            $query = $this->pdo->prepare($this->insertUserSql);
            $query->execute($params);
        }
    }

    private function createPosts(int $numberOfPostByAdmin): void
    {
        $admins = $this->userRepository->findBy(['role' => UserEntity::ROLE_ADMIN]);

        foreach($admins as $admin){
            for($p = 0; $p < $numberOfPostByAdmin; $p++){

                $userAdmin = $this->normalizer->normalize($admin, UserEntity::class);

                $params = [
                    ':author_id' => $userAdmin->getId(), 
                    ':title' => $this->faker->word(), 
                    ':head' => $this->faker->paragraph(2), 
                    ':content' => $this->faker->paragraph(5), 
                    ':slug' => $this->faker->slug(), 
                    ':status' => self::POST_STATUS[mt_rand(0, 1)]
                ];
    
                $query = $this->pdo->prepare($this->insertPostSql);
                $query->execute($params);
            }
        }
    }

    private function createComments(): void
    {
        $posts = $this->postRepository->findAll();
        $users = $this->userRepository->findBy(data: ['role' => UserEntity::ROLE_USER], limit: 3);
        
        foreach($posts as $post){
            $post = $this->normalizer->normalize($post, PostEntity::class);

            $comments = $post->getComments();
            $comments = $comments ? json_decode($comments) : [];

            foreach($users as $user){
                $user = $this->normalizer->normalize($user, UserEntity::class);

                $userId = $user->getId();
                $postId = $post->getId();
                $content = $this->faker->paragraph(2);

                $status = self::COMMENT_STATUS[mt_rand(0, 2)];

                $params = [
                    ':author_id' => $userId, 
                    ':post_id' => $postId,
                    ':content' => $content,
                    ':status' => $status
                ];

                $query = $this->pdo->prepare($this->insertCommentSql);
                $query->execute($params);

                $comment = $this->commentRepository->findOneBy([
                    'author_id' => $userId, 
                    'post_id' => $postId,
                    'content' => $content,
                    'status' => $status
                ]);

                $comment = $this->normalizer->normalize($comment, CommentEntity::class);

                $comments[] = $comment->getId();
            }

            $comments = json_encode($comments);

            $params = [':post_id' => $postId, ':comments' => $comments];
            
            $query = $this->pdo->prepare($this->updatePostSql);
            $query->execute($params);
        }
    }

}