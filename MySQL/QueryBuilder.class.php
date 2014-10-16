<?php

namespace Framework\MySQL;

class QueryBuilder {

    const SELECT = 1;

    const INSERT = 2;

    const UPDATE = 3;

    const DROP = 5;

    const SHOW_TABLES = 6;

    private $table_prefix = '';

    private $select_fields = array();

    private $table_name = null;

    private $query_type = null;

    private $data = array();

    private $where = array();

    private function __construct($table_name) {
        $this->table_name = $table_name;
    }

    public static function Select($table) {
        $builder = new self($table);
        $builder->query_type = self::SELECT;
        return $builder;
    }

    public static function Insert($table) {
        $builder = new self($table);
        $builder->query_type = self::INSERT;
        return $builder;
    }

    public static function Drop($table) {
        $builder = new self($table);
        $builder->query_type = self::INSERT;
        return $builder;
    }

    public static function ShowTables() {
        $builder = new self('');
        $builder->query_type = self::SHOW_TABLES;
        return $builder;
    }

    public function setTablePrefix($prefix) {
        $this->table_prefix = (string)$prefix;
        return $this;
    }

    public function addData($name, $value) {
        $this->data[$name] = $value;
        return $this;
    }

    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    public function addSelectField($field) {
        $this->select_fields[] = $field;
        return $this;
    }

    public function setSelectFields(array $fields) {
        $this->select_fields = $fields;
        return $this;
    }

    private function getSelectSection($select_fields) {

        if (empty($select_fields)) {
            return '*';
        }

        return '`'.join('`, `', $select_fields).'`';
    }

    private function escape($value) {
        return $value;
    }

    public function setWhere(array $where) {
        $this->where = $where;
        return $this;
    }

    private function getWhereString($where) {
        if (empty($where)) {
            return '';
        }

        $where_properties = array();
        foreach ($where as $name => $value) {
            $where_properties[] = $name.'='.$this->escape($value);
        }

        return ' WHERE '.join(' AND ', $where_properties);
    }

    private function getFinalTableName() {
        return $this->table_prefix.$this->table_name;
    }

    /**
     * @return string
     */
    public function get() {

        $table_name = $this->getFinalTableName();

        switch ($this->query_type) {
            case self::SELECT:

                $select = $this->getSelectSection($this->select_fields);

                $query = 'SELECT '.$select.' FROM '.$table_name;
                $query.= $this->getWhereString($this->where);
                return $query;

            case self::INSERT:

                return '';
            case self::UPDATE:

                return '';
            case self::SHOW_TABLES:
                return 'SHOW TABLES;';
            case self::DROP:
                return 'DROP '.'TABLE '.$table_name.';';
            default:

                return '';
        }
    }
}