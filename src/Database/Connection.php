<?php

namespace Src\Database;

use PDO;
use PDOException;

class Connection
{
    private $connection = null;
    private array $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

    ];
    private $cursor = null;
    private string $user = 'root';
    private string $password = 'toor';
    private string $host = 'localhost';
    private string $dbname = 'ice_store';

    public function __construct()
    {
        try {
            if (!isset($this->connection)) {
                $this->connection = new PDO("mysql:host=$this->host;charset=utf8; dbname=$this->dbname", $this->user, $this->password, $this->options);
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }


    }

    public function all(): bool|array
    {
        return $this->connection->query('select * from teachers')->fetchAll();
    }

    public function prepare($sql, $value): static
    {

        $this->connection->prepare($sql);
        return $this;
    }

    public function save(): static
    {
        $this->connection->commit();
        return $this;
    }

    public function first(): static
    {
        return $this;
    }

}


/**
 */