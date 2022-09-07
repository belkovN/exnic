<?php

namespace App\Database;

use App\Interface\Database\DBInterface;
use App\Logger\Logger;
use PDO, PDOException;

class Mysql
extends DBCommonMethods
implements DBInterface
{

    use DBTrait;

    private $dsn;
    private $DBH;

    public function __toString()
    {
        return print_r($this, 1);
    }

    public function db_connect(): bool
    {
        try {
            $this->dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
            $this->DBH = new PDO($this->dsn, $this->uid, $this->password);
            $this->DBH->exec("set names utf8");
            $this->DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        } catch (PDOException $e) {
            Logger::getLogger("mysql")->log("ERROR LINE : " . $e->getLine() . " ERROR: " . $e->getMessage());
            return false;
        }
    }

    public function get(): ?array
    {
        try {
            $sth = $this->sql();
            $sth->setFetchMode(PDO::FETCH_ASSOC);
            $sth->execute($this->params);
            $rows = $sth->fetchAll();
            return $rows;
        } catch (PDOException $e) {
            Logger::getLogger("mysql")->log("ERROR LINE : " . $e->getLine() . " ERROR: " . $e->getMessage());
            return null;
        }
    }

    public function first(): ?array
    {
        try {
            $this->limit = 1;
            $sth = $this->sql();
            $sth->setFetchMode(PDO::FETCH_ASSOC);
            $sth->execute($this->params);
            if (!$row = $sth->fetch()) return null;
            return $row;
        } catch (PDOException $e) {
            Logger::getLogger("mysql")->log("ERROR LINE : " . $e->getLine() . " ERROR: " . $e->getMessage());
            return null;
        }
    }

    private function sql()
    {

        $limit = ($this->limit > 0) ? " LIMIT " . $this->limit : "";
        $sql = "SELECT " . implode(", ", $this->fields) . " FROM `" . $this->table . "` 
        " . $this->get_sql_join() . "
        " . $this->get_where() . " 
        " . $this->get_group_by() . "
        " . $this->having . "
        " . $this->get_order_by() . "
        " . $limit;
        if ($this->test_mode) die($sql . print_r($this->params));

        try {
            $sth = $this->DBH->prepare(
                $sql
            );
            $sth->setFetchMode(PDO::FETCH_ASSOC);
            return $sth;
        } catch (PDOException $e) {
            Logger::getLogger("mysql")->log("ERROR LINE : " . $e->getLine() . " ERROR: " . $e->getMessage());
        }
    }

    public function count(): int
    {
        try {
            $this->fields = ["*"];
            $sth = $this->sql();
            $sth->execute($this->params);
            return $sth->rowCount();
        } catch (PDOException $e) {
            Logger::getLogger("mysql")->log("ERROR LINE : " . $e->getLine() . " ERROR: " . $e->getMessage());
            return false;
        }
    }

    public function max(string $field): array
    {
        try {
            $this->fields = ["max(" . $field . ") as max"];
            $sth = $this->sql();
            $sth->execute($this->params);
            return ['max' => $sth->fetch()['max']];
        } catch (PDOException $e) {
            Logger::getLogger("mysql")->log("ERROR LINE : " . $e->getLine() . " ERROR: " . $e->getMessage());
            return [];
        }
    }

    public function min(string $field): array
    {
        try {
            $this->fields = ["min(" . $field . ") as min"];
            $sth = $this->sql();
            $sth->execute($this->params);
            return ['min' => $sth->fetch()['min']];
        } catch (PDOException $e) {
            Logger::getLogger("mysql")->log("ERROR LINE : " . $e->getLine() . " ERROR: " . $e->getMessage());
            return [];
        }
    }

    public function avg(string $field): array
    {
        try {
            $this->fields = ["AVG(" . $field . ") as avg"];
            $sth = $this->sql();
            $sth->execute($this->params);
            return ['avg' => $sth->fetch()['avg']];
        } catch (PDOException $e) {
            Logger::getLogger("mysql")->log("ERROR LINE : " . $e->getLine() . " ERROR: " . $e->getMessage());
            return [];
        }
    }

    public function sum(string $field): array
    {
        try {
            $this->fields = ["SUM(" . $field . ") as sum"];
            $sth = $this->sql();
            $sth->execute($this->params);
            return ['sum' => $sth->fetch()['sum']];
        } catch (PDOException $e) {
            Logger::getLogger("mysql")->log("ERROR LINE : " . $e->getLine() . " ERROR: " . $e->getMessage());
            return [];
        }
    }

    public function insert(array $fields): bool
    {
        try {
            if (isset($fields['0']) && is_array($fields['0'])) {
                foreach ($fields as $v) {
                    $sql = $this->get_sql_insert($v);
                    $this->DBH->prepare($sql)->execute(array_values($v));
                }
            } else {
                $sql = $this->get_sql_insert($fields);
                $this->DBH->prepare($sql)->execute(array_values($fields));
            }
            return true;
        } catch (PDOException $e) {
            Logger::getLogger("mysql")->log("ERROR LINE : " . $e->getLine() . " ERROR: " . $e->getMessage());
            return false;
        }
    }

    public function insertGetId(array $fields): int
    {
        try {
            $sql = $this->get_sql_insert($fields);
            $this->DBH->prepare($sql)->execute(array_values($fields));
            return $this->DBH->lastInsertId();
        } catch (PDOException $e) {
            Logger::getLogger("mysql")->log("ERROR LINE : " . $e->getLine() . " ERROR: " . $e->getMessage());
            return false;
        }
    }

    public function update(array $fields): bool
    {
        try {
            $sql = $this->get_sql_update($fields);
            $fields = array_merge($fields, $this->params);
            $this->DBH->prepare($sql)->execute(array_values($fields));
            return true;
        } catch (PDOException $e) {
            Logger::getLogger("mysql")->log("ERROR LINE : " . $e->getLine() . " ERROR: " . $e->getMessage());
            return false;
        }
    }

    public function delete(): bool
    {
        try {
            $sql = "DELETE FROM `" . $this->table . "` " . " " . $this->get_where();
            $this->DBH->prepare($sql)->execute($this->params);
            return true;
        } catch (PDOException $e) {
            Logger::getLogger("mysql")->log("ERROR LINE : " . $e->getLine() . " ERROR: " . $e->getMessage());
            return false;
        }
    }

    public function increment(string $field): bool
    {
        try {
            $sql = "UPDATE " . $this->table . " SET `" . $field . "` = " . $field . "+1 " . $this->get_where();
            $this->DBH->prepare($sql)->execute([]);
            return true;
        } catch (PDOException $e) {
            Logger::getLogger("mysql")->log("ERROR LINE : " . $e->getLine() . " ERROR: " . $e->getMessage());
            return false;
        }
    }

    public function decrement(string $field): bool
    {
        try {
            $sql = "UPDATE " . $this->table . " SET `" . $field . "` = " . $field . "-1 " . $this->get_where();
            $this->DBH->prepare($sql)->execute([]);
            return true;
        } catch (PDOException $e) {
            Logger::getLogger("mysql")->log("ERROR LINE : " . $e->getLine() . " ERROR: " . $e->getMessage());
            return false;
        }
    }
}
