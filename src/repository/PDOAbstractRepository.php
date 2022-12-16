<?php

namespace App\Repository;

use App\Database\PDOConnection;
use PDO;

abstract class PDOAbstractRepository implements RepositoryInterface
{    
    protected ?PDO $pdo = null;

    protected string $table;
    protected array $requiredColumns;
    protected array $optionnalColumns;


    public function __construct()
    {
        $pdoConnection = new PDOConnection();
        $this->pdo = $pdoConnection->getPdo();
    }


    public function update(array $values, int $id): void
    {
        $authorizedValues = array_merge($this->requiredColumns, $this->optionnalColumns);
        $unauthorizedValues = array_diff_key($values, $authorizedValues);

        if(!empty($unauthorizedValues)){
            echo 'Une erreur est survenue.';
            die();
        }

        extract($this->buildUpdateQuery($values));

        $params[':id'] = $id;

        $query = $this->pdo->prepare($sql);
        $query->execute($params);
    }


    public function create(array $values): void
    {
        $optionnalValues = array_diff_key($values, $this->requiredColumns);
        $unauthorizedValues = array_diff_key($optionnalValues, $this->optionnalColumns);

        if(!empty($unauthorizedValues)){
            echo 'Une erreur est survenue.';
            die();
        }

        extract($this->buildCreateQuery($values, $optionnalValues));

        $query = $this->pdo->prepare($finalSql);
        $query->execute($params);
    }


    public function delete(int $id): void
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id ");
        $query->execute([':id' => $id]);
    }

    
    public function find(int $id): ?array
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 0,1");

        $query->execute([':id' => $id]);
        $result = $query->fetch();

        return $result === false ? null : $result;
    }


    public function findAll(): array
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table}");

        $query->execute();
        $results = $query->fetchAll();

        return $results;
    }


    public function findOneBy(array $data): ?array
    {
        extract($this->getSql($data));

        $query = $this->pdo->prepare("SELECT * FROM {$this->table} $sql");

        $query->execute($params);
        $result = $query->fetch();

        return $result ? $result : null;
    }


    public function findBy(array $data, ?array $orderCriterias = null, ?int $limit = null, ?int $offset = null): array
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


    // ========================== PRIVATE FUNCTIONS ========================== \\

    private function buildUpdateQuery(array $values): array
    {
        $sql = "UPDATE {$this->table} SET";

        $iteration = 1;

        foreach($values as $key => $value){
            $securedValue = htmlspecialchars($value);

            if($iteration === 1){
                $sql .= " $key = :$key ";
                $params[":$key"] = $securedValue;
                
                if(count($values) > 1){
                    $sql .= ', ';
                }
            }else{
                $sql .= " $key = :$key";
                $params[":$key"] = $securedValue;

                if($iteration !== count($values)){
                    $sql .= ', ';
                }
            }            

            $iteration++;
        }

        $sql .= ' WHERE id = :id';

        return compact('sql', 'params');
    }

    
    private function buildCreateQuery(array $values, array $optionnalValues): array
    {
        $sql = "INSERT INTO {$this->table} (";
        $params = [];

        $hasOptionnalValue = count($optionnalValues) > 0 ? true : false;

        extract($this->beginQuery($sql, $values, $params, $hasOptionnalValue));
        extract($this->continueQuery($sql, $params, $hasOptionnalValue, $optionnalValues));

        $sql .= ' VALUES (';

        $finalSql = $this->finishQuery($sql, $params);

        return compact('finalSql', 'params');
    }


    private function beginQuery(string $sql, array $values, array $params, bool $hasOptionnalValue): array
    {
        $iteration = 1;

        foreach($this->requiredColumns as $key => $value){
            $securedValue = htmlspecialchars($values[$key]);

            if($iteration === count($this->requiredColumns)){
                $sql .= " $key ";

                if(!$hasOptionnalValue){
                    $sql .= ')';
                    $params[":$key"] = $securedValue;
                }else{
                    $sql .= ',';
                    $params[":$key"] = $securedValue;
                }
            }else{
                $sql .= " $key,";
                $params[":$key"] = $securedValue;
            }

            $iteration++;
        }

        return compact('sql', 'values', 'params', 'hasOptionnalValue');
    }


    private function continueQuery(string $sql, array $params, bool $hasOptionnalValue, ?array $optionnalValues = null): array
    {
        $iteration = 1;

        if($hasOptionnalValue){
            foreach($optionnalValues as $key => $value){

                $securedValue = htmlspecialchars($value);

                if($iteration !== count($optionnalValues)){
                    $sql .= " $key,";
                    $params[":$key"] = $securedValue;
                }else{
                    $sql .= " $key )";
                    $params[":$key"] = $securedValue;
                }

                $iteration++;
            }
        }

        return compact('sql', 'params', 'hasOptionnalValue', 'optionnalValues');
    }


    private function finishQuery(string $sql, array $params): string
    {
        $iteration = 1;

        foreach($params as $key => $value){
            if($iteration === count($params)){
                $sql .= "$key)";
            }else{
                $sql .= "$key, ";
            }

            $iteration++;
        }
        
        return $sql;
    }


    private function getSql(array $data): array
    {
        $iteration = 1;
        $sql = 'WHERE ';
        $params = [];

        $allColumns = array_merge($this->requiredColumns, $this->optionnalColumns);

        foreach($data as $key => $value){
            if(!in_array($key, $allColumns)){
                // throw exception !
                echo 'Une erreur est survenue.';
                die;
            }

            $securedValue = htmlspecialchars($value); 

            $sql .= "$key = :$key";
            $params[":$key"] = $securedValue;

            if($iteration !== count($data)){
                $sql .= ' AND ';
            }   
            
            $iteration++;
        }

        return compact('sql', 'params');
    }


    private function addCriteriaToSql(array $orderCriterias, string $sql): string
    {
        $allColumns = array_merge($this->requiredColumns, $this->optionnalColumns);
        $iteration = 1;

        foreach($orderCriterias as $key => $value){

            if(!in_array($key, $allColumns)){
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

}