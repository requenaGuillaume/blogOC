<?php

namespace App\Repository;

use PDO;
use PDOException;

abstract class PDOAbstractRepository
{
    
    protected const DB_HOST = 'localhost';
    protected const DB_NAME = 'blogoc';
    protected const DB_USER = 'root';
    protected const DB_PASSWORD = '';
    
    protected array $columns;

    protected ?PDO $pdo = null;
    protected string $table;


    public function __construct() 
    {
        $this->pdo = $this->getPDO();
    }


    abstract public function create(): void;
    abstract public function update(): void;

    public function delete(int $id): void
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id ");
        $query->execute([':id' => $id]);
    }

    
    public function find(int $id): array
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 0,1");

        $query->execute([':id' => $id]);
        $result = $query->fetch();

        return $result;
    }


    public function findAll(): array
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table}");

        $query->execute();
        $results = $query->fetchAll();

        return $results;
    }


    public function findOneBy(array $data)
    {
        extract($this->getSql($data));

        $query = $this->pdo->prepare("SELECT * FROM {$this->table} $sql");

        $query->execute($params);
        $result = $query->fetch();

        return $result;
    }


    public function findBy(array $data, ?array $orderByCriteria = null, ?int $limit = null, ?int $offset = null)
    {
        extract($this->getSql($data));

        // TODO : $orderByCriteria, before $limit

        if($limit){
            $sql .= ' LIMIT '.intval($limit);
        }

        if($offset){
            $sql .= ' OFFSET '.intval($offset);
        }

        $query = $this->pdo->prepare("SELECT * FROM {$this->table} $sql");

        $query->execute($params);
        $result = $query->fetchAll();

        return $result;
    }


    protected function getSql(array $data): array
    {
        $iteration = 1;
        $sql = 'WHERE ';
        $params = [];

        foreach($data as $key => $value){
            if(!in_array($key, $this->columns)){
                // throw exception !
                echo 'Une erreur est survenue.';
                die;
            }

            $key = htmlspecialchars($key);
            $value = htmlspecialchars($value); 

            $sql .= "$key = :$key";
            $params[":$key"] = $value;

            if($iteration !== count($data)){
                $sql .= ' AND ';
            }   
            
            $iteration++;
        }

        return compact('sql', 'params');
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