<?php

namespace App\Repository;

use PDO;
use PDOException;

class PDOAbstractRepository
{
    
    protected const DB_HOST = 'localhost';
    protected const DB_NAME = 'blogoc';
    protected const DB_USER = 'root';
    protected const DB_PASSWORD = '';
    
    protected ?PDO $pdo = null;
    protected string $table;


    public function __construct() 
    {
        $this->pdo = $this->getPDO();        
    }

    
    public function find(int $id): array
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 0,1");

        $query->execute([':id' => $id]);
        $result = $query->fetch();

        return $result;
    }


    protected function getPdo(): PDO
    {
        if($this->pdo === null){
            try{
                $this->pdo = new PDO('mysql:host='.self::DB_HOST.';dbname='.self::DB_NAME.';charset=utf8', self::DB_USER, self::DB_PASSWORD, 
                                    [
                                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                                    ]);
            }catch(PDOException $e){
                echo $e->getMessage();
                exit;
            }
        }

        return $this->pdo;
    }  
}