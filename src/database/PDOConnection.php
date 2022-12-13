<?php

namespace App\Database;

use PDO;
use PDOException;

class PDOConnection
{

    public const DB_HOST = 'localhost';
    public const DB_NAME = 'blogoc';
    public const DB_USER = 'root';
    public const DB_PASSWORD = '';

    protected ?PDO $pdo = null;


    public function __construct() 
    {
        $this->pdo = $this->getPDO();
    }
    
    public function getPdo(): PDO
    {
        if($this->pdo === null){
            var_dump("Connexion");
            try{
                $this->pdo = new PDO('mysql:host='.self::DB_HOST.';
                                      dbname='.self::DB_NAME.';
                                      charset=utf8', 
                                      self::DB_USER, 
                                      self::DB_PASSWORD, 
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