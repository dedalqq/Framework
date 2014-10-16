<?php

namespace Framework\MySQL;

use Framework\Exceptions\FatalException;

abstract class ActiveRecord {

    const TYPE_INT = 1;

    const TYPE_STRING = 2;

    const TYPE_ARRAY = 3;

    private $data = array();

    private $is_new = true;

    /**
     * @var Connection
     */
    private static $db_connection = null;

    /**
     * @var static
     */
    private static $model = null;

    /**
     * @return string
     */
    abstract protected function getTableName();

    /**
     * @return array
     */
    abstract public function getProperties();

    /**
     * @param Connection $db_connection
     */
    public static function setDbConnection(Connection $db_connection) {
        self::$db_connection = $db_connection;
    }

    private function __construct() {
        if (is_null(self::$db_connection)) {
            throw new FatalException('Connection is not set!');
        }
    }

    public static function model() {

        if (is_null(self::$model)) {
            self::$model = new static();
        }

        return self::$model;

    }

    public function __set($name, $value) {

        if (!isset($property[$name])) {
            return null;
        }

        $this->data[$name] = $this->escapeValue($name, $value);

        return null;
    }

    function __get($name) {

        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }

    protected function getPk() {
        return 'id';
    }

    public function escapeValue($name, $value) {

        $property = $this->getProperties();

        if ($property[$name] == self::TYPE_INT) {
            return (int)$value;
        }
        elseif ($property[$name] == self::TYPE_STRING) {
            return (string)$value;
        }
        elseif ($property[$name] == self::TYPE_ARRAY) {
            // @todo
        }

        return null;
    }

    public function setData(array $data) {

        foreach ($this->getProperties() as $name => $value) {

            if (!isset($data[$name])) {
                continue;
            }

            $this->data[$name] = $this->escapeValue($name, $data[$name]);
        }

    }

    /**
     * @param array $data
     * @return bool|int
     */
    public static function insert(array $data) {
        /** @var self $model */
        $model = new static();
        $model->setData($data);
        $result = $model->save();

        if ($result->isSuccess()) {
            return self::$db_connection->getLastId();
        }

        return false;
    }

    /**
     * @param array $parameters
     * @return static|null
     */
    public static function find(array $parameters = array()) {

        $table_name = self::model()->getTableName();

        $query = QueryBuilder::Select($table_name)->setWhere($parameters);

        $result = self::model()->query($query);

        if ($result->isSuccess()) {
            /** @var self $model */
            $model = new static();
            $model->setData($result->fetch());
            $model->is_new = false;
            return $model;
        }

        return null;

    }

    public static function count(array $parameters = array()) {
        // @todo
    }

    public static function getById($id) {
        // @todo
    }

    public static function getByProperty(array $property) {
        // @todo
        return null;
    }

    public function load($id) {
        // @todo
    }

    /**
     * @return MysqlResult
     */
    public function save() {

        if (empty($this->data)) {
            return false;
        }

        if ($this->is_new) {
            $query = QueryBuilder::Insert($this->getTableName())->setData($this->data);
        }
        else {
            $query = QueryBuilder::Insert($this->getTableName())->setData($this->data);
            $query->setWhere($this->getPk(), $this->data[$this->getPk()]);
        }

        return $this->query($query);
    }

    public function query($query) {
        return self::$db_connection->query($query);
    }

}