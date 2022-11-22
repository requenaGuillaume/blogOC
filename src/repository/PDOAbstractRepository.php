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
    protected array $requiredColumns;
    protected array $optionnalColumns;

    protected ?PDO $pdo = null;
    protected string $table;


    public function __construct() 
    {
        $this->pdo = $this->getPDO();
    }


    // abstract public function create(): void;
    abstract public function update(): void;

    // too much Loop ? use ternary instead of ifelse ?
    public function create(array $values): void
    {
        $sql = "INSERT INTO {$this->table} (";
        $optionnalValues = array_diff_key($values, $this->requiredColumns);
        $hasOptionnalValue = count($optionnalValues) > 0 ? true : false;
        $params = [];
        $iteration = 1;

        foreach($this->requiredColumns as $key => $value){
            $value1 = $values[$key];

            if($iteration === count($this->requiredColumns)){
                $sql .= " $key ";

                if(!$hasOptionnalValue){
                    $sql .= ')';
                    $params[":$key"] = $value1;
                }else{
                    $sql .= ',';
                    $params[":$key"] = $value1;
                }
            }else{
                $sql .= " $key,";
                $params[":$key"] = $value1;
            }

            $iteration++;
        }

        $iteration = 1;

        if($hasOptionnalValue){
            foreach($optionnalValues as $key => $value){
                if(!in_array($key, $this->optionnalColumns)){
                    // Throw exception
                    echo 'Une erreur est survenue.';
                    die();
                }

                if($iteration !== count($optionnalValues)){
                    $sql .= " $key,";
                    $params[":$key"] = $value;
                }else{
                    $sql .= " $key )";
                    $params[":$key"] = $value;
                }

                $iteration++;
            }
        }

        $sql .= ' VALUES (';
        $iteration = 1;

        foreach($params as $key => $value){
            if($iteration === count($params)){
                $sql .= "$key)";
            }else{
                $sql .= "$key, ";
            }

            $iteration++;
        }

        $query = $this->pdo->prepare($sql);
        $query->execute($params);
    }


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


    public function findBy(array $data, ?array $orderCriterias = null, ?int $limit = null, ?int $offset = null)
    {
        extract($this->getSql($data));

        if($orderCriterias){
            $sql = $this->addCriteriaToSql($orderCriterias, $sql); 
        }

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


    // ========================== PROTECTED FUNCTIONS ========================== \\

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


    protected function addCriteriaToSql(array $orderCriterias, string $sql): string
    {
        $iteration = 1;

        foreach($orderCriterias as $key => $value){

            if(!in_array($key, $this->columns)){
                // throw exception !
                echo 'Une erreur est survenue.';
                die;
            }

            $key = htmlspecialchars($key);
            $value = htmlspecialchars($value);

            if($iteration === 1){
                $sql .= " ORDER BY $key $value";
            }else{
                $sql .= ", $key $value";
            }

            $iteration++;
        }
        
        return $sql;
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