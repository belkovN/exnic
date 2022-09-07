<?php

namespace App\Database;

trait DBTrait
{

    private $table;
    private $where = [];
    private $or_where = [];
    private $params = [];
    private $index_param = 0;
    private $fields = ['*'];
    private $order;
    private $group;
    private $test_mode = false;
    private $join = [];
    private $test_where = null;
    private $open = false;
    private $join_field = null;
    private $having;
    private $limit = 0;

    public function init()
    {
        $this->where = [];
        $this->params = [];
        $this->or_where = [];
        $this->params = [];
        $this->index_param = 0;
        $this->fields = ['*'];
        $this->order = null;
        $this->group = null;
        $this->test_mode = false;
        $this->join = [];
        $this->test_where = null;
        $this->open = false;
        $this->join_field = null;
        $this->having = null;
        $this->limit = 0;
    }

    public function set_test_mode(bool $mode): object
    {
        $this->test_mode = $mode;
        return $this;
    }

    public function table(string $table): object
    {
        $this->table = $table;
        return $this;
    }

    public function whereBetween(string $field, array $params): object
    {
        if (isset($this->where[$this->index_param])) $this->where[$this->index_param][] = "and";
        $this->where[$this->index_param][] = $field . " BETWEEN ? AND ?";
        $this->params[] = $params['0'];
        $this->params[] = $params['1'];
        if (!$this->open) {
            $this->index_param++;
        }
        return $this;
    }

    public function whereNotBetween(string $field, array $params): object
    {
        if (isset($this->where[$this->index_param])) $this->where[$this->index_param][] = "and";
        $this->where[$this->index_param][] = $field . " NOT BETWEEN ? AND ?";
        $this->params[] = $params['0'];
        $this->params[] = $params['1'];
        if (!$this->open) {
            $this->index_param++;
        }
        return $this;
    }

    public function where(array $params): object
    {
        $this->params[] = $params['2'];
        if (isset($this->where[$this->index_param])) $this->where[$this->index_param][] = "and";
        $this->where[$this->index_param][] =  $params['0']  . " " . $params['1'] . " ?";
        if (!$this->open) {
            $this->index_param++;
        }
        return $this;
    }

    public function orWhere($params): object
    {
        if (is_callable($params)) {
            $this->open = true;
            $params($this);
            $this->index_param++;
            $this->open = !true;
            return $this;
        }
        $this->params[] = $params['2'];
        if (isset($this->where[$this->index_param])) $this->where[$this->index_param][] = "or";
        $this->where[$this->index_param][] =  $params['0']  . " " . $params['1'] . " ?";
        return $this;
    }

    public function get_where()
    {
        $where = [];
        foreach ($this->where as $v) {
            $where[] = '(' . implode(" ", $v) . ')';
        }
        return (count($where) > 0) ? "where " . implode(' or ', $where) : "";
    }

    public function whereNull(...$params): object
    {
        foreach ($params as $v) {
            if (isset($this->where[$this->index_param])) $this->where[$this->index_param][] = "and";
            $this->where[$this->index_param][] = $v . " IS NULL";
            if (!$this->open) {
                $this->index_param++;
            }
        }
        return $this;
    }

    private function merge($where, $separator = " && ")
    {
        $sql = [];
        foreach ($where as $value) {
            $sql[] = implode(' ', $value);
        }
        return implode($separator, $sql);
    }

    public function whereIn(string $field, array $params): object
    {
        if (isset($this->where[$this->index_param])) $this->where[$this->index_param][] = "and";
        $this->where[$this->index_param][] = $field . " IN (" . implode(', ', array_fill(0, count($params), '?')) . ")";
        $this->params = array_merge($this->params, $params);
        if (!$this->open) {
            $this->index_param++;
        }
        return $this;
    }

    public function whereNotIn(string $field, array $params): object
    {
        if (isset($this->where[$this->index_param])) $this->where[$this->index_param][] = "and";
        $this->where[$this->index_param][] = $field . " NOT IN (" . implode(', ', array_fill(0, count($params), '?')) . ")";
        $this->params = array_merge($this->params, $params);
        if (!$this->open) {
            $this->index_param++;
        }
        return $this;
    }

    public function select(...$fields): object
    {
        if ($this->fields[0] == "*") {
            unset($this->fields[0]);
        }
        $this->fields = array_merge($this->fields, $fields);
        return $this;
    }


    public function groupBy(string $field): object
    {
        $this->group = "GROUP BY " . $field;
        return $this;
    }

    public function orderBy(string $field, string $type = null): object
    {
        $this->order = "ORDER BY " . $field . " " . $type;
        return $this;
    }

    public function orderByRand(): object
    {
        $this->order = "ORDER BY RAND()";
        return $this;
    }

    private function get_order_by()
    {
        return $this->order;
    }

    private function get_group_by()
    {
        return $this->group;
    }

    private function get_sql_insert($params)
    {
        return "INSERT INTO `" . $this->table . "` (" . implode(', ', array_keys($params)) . ") VALUES (" . implode(', ', array_fill(0, count($params), '?')) . ")";
    }

    function mapped_implode($array)
    {
        return implode(
            ", ",
            array_map(
                function ($k) {
                    return $k  . " = ?";
                },
                array_keys($array),
            )
        );
    }

    private function get_sql_update($params)
    {
        return "UPDATE " . $this->table . " SET " . $this->mapped_implode($params) . " " . $this->get_where();
    }

    private function get_sql_join()
    {
        return implode(" ", $this->join);
    }

    public function join(string $field, $params): object
    {
        if (is_callable($params)) {
            $this->join_field = $field;
            $params($this);
            return $this;
        }
        $this->join[] = "  JOIN `" . $field . "` ON " . $params['0'] . " " . $params['1'] . " " . $params['2'];
        return $this;
    }

    public function orOn(array $params): object
    {
        $this->join[] = "  OR  " . $params['0'] . " " . $params['1'] . " " . $params['2'];
        return $this;
    }

    public function on(array $params): object
    {
        $this->join[] = "  JOIN `" . $this->join_field . "` ON " . $params['0'] . " " . $params['1'] . " " . $params['2'];
        return $this;
    }

    public function left_join(string $field, array $params): object
    {
        $this->join[] = " LEFT JOIN `" . $field . "` ON " . $params['0'] . " " . $params['1'] . " " . $params['2'];
        return $this;
    }

    public function having(...$fields): object
    {
        $this->having = "having " . $fields['0'] . " " . $fields['1'] . " " . $fields['2'];
        return $this;
    }
}
