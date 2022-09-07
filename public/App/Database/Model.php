<?php

namespace App\Database;


class Model
{

    private $connection = [];
    private $object = null;
    private static $model = [];
    private static $connect_id = [];

    public function __construct($init = [], $fields = [])
    {

        if (count($fields) > 0) {
            $this->fields = $fields;
        }

        foreach ($this->fields as $v) {
            $this->$v = (isset($init[$v])) ? $init[$v] : NULL;
        }
        if (method_exists($this, "connection")) {
            $this->connection = call_user_func(array($this, 'connection'));
            foreach ($this->connection as $key => $value) {
                $this->$key = NULL;
            }
        }
    }

    public function getObject()
    {
        return $this->object;
    }

    public function hasOne($models)
    {
        foreach ($this->connection as $key => $value) {
            $obj = new $value;
            $q = DB::table($obj->table)
                ->select(implode(',', $obj->fields));

            if (count(self::$connect_id) > 0) {
                $q->whereIn($obj->find, self::$connect_id);
            }
            $all = $q->get();
            foreach ($all as $val) {
                $o = new $value($val);
                $o->object = $val;
                $models[$val[$obj->find]]->$key = $o;
            }
        }
        return (object)$models;
    }

    public static function all($where = [])
    {
        self::$connect_id = [];
        $class = get_called_class();
        $obj = new $class;
        $q = DB::table($obj->table)
            ->select(implode(',', $obj->fields));

        if (count($where) > 0) {
            $q->whereIn($obj->find, $where);
        }
        $all = $q->get();

        $models = [];
        foreach ($all as $v) {
            self::$connect_id[$v['id']] = $v['id'];
            $o = new $class($v);
            $o->object = $v;
            $models[$v['id']] = $o;
        }
        if ($models)
            $models = $obj->hasOne($models);
        return (object)$models;
    }

    public static function find($id)
    {
        $result = self::all([$id]);
        return current($result);

        if (!isset(self::$model[$id])) {
            $class = get_called_class();
            $obj = new $class;
            self::$model[$id] = $obj;
        }
        self::$model[$id]->get($id);
        return self::$model[$id];
    }


    public function __toString()
    {
        $models = [];
        foreach ($this->fields as $key) {
            $models[$key] = $this->$key;
        }
        foreach ($this->connection as $key_connection => $val) {
            $models[$key_connection] = $this?->$key_connection?->object;
        }
        return json_encode($models);
    }

    public function get($id)
    {
        $field = $this->table . "." . implode(', ' . $this->table . ".", $this->fields);

        $object = DB::table($this->table)
            ->select($field)
            ->where([$this->table . "." . $this->find, '=', $id])
            ->orderBy($this->table . '.id', 'ASC');

        $o = $object->first();


        foreach ($o as $key => $val) {
            if (!in_array($key, $this->fields)) {
                unset($o[$key]);
            } else {
                $this->$key = $val;
            }
        }
        $this->object[$id] = $o;
        return $o;
    }

    public function save()
    {
        $o = [];
        foreach ($this->fields as $key) {
            if (!is_array($this->$key)) {
                $o[$key] = $this->$key;
            }
            $this->object[$key] = $this->$key;
        }

        if ($this->id) {
            DB::table($this->table)
                ->where(['id', '=', $this->id])
                ->update($o);
        } else {
            $this->id = DB::table($this->table)
                ->insertGetId($o);
        }
        return $this->id;
    }

    public function delete()
    {
        DB::table($this->table)
            ->where(['id', '=', $this->id])
            ->delete();
        return true;
    }
}
