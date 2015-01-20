<?php

namespace Framework\MySQL;

use Framework\Exceptions\Fatal as FatalException;

abstract class ActiveRecord
{

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
    public static function setDbConnection(Connection $db_connection)
    {
        self::$db_connection = $db_connection;
    }

    public function __construct()
    {
        if (is_null(self::$db_connection)) {
            throw new FatalException('Connection is not set!');
        }
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @return static
     */
    public static function model()
    {
        return new static();
    }

    public function setValue($name, $value)
    {

        $property = $this->getProperties();

        if (!isset($property[$name])) {
            return null;
        }

        $this->data[$name] = $this->escapeValue($name, $value);

        return null;
    }

    public function __set($name, $value)
    {
        return $this->setValue($name, $value);
    }

    function __get($name)
    {

        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }

    protected function getPk()
    {
        return 'id';
    }

    public function getPkValue()
    {
        if (isset($this->data[$this->getPk()])) {
            return $this->data[$this->getPk()];
        }
        return null;
    }

    public function escapeValue($name, $value)
    {

        $property = $this->getProperties();

        if ($property[$name] == self::TYPE_INT) {
            return (int)$value;
        } elseif ($property[$name] == self::TYPE_STRING) {
            return (string)$value;
        } elseif ($property[$name] == self::TYPE_ARRAY) {
            // @todo vse nado vsem
        }

        return null;
    }

    public function setData(array $data)
    {

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
    public static function insert(array $data)
    {
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
    public static function find(array $parameters = array())
    {

        $table_name = self::model()->getTableName();

        $query = QueryBuilder::Select($table_name)->setWhere($parameters);

        $result = self::model()->query($query);

        if ($result->isEmpty()) {
            return null;
        }

        $data = $result->fetch();

        if (empty($data)) {
            return null;
        }

        /** @var self $model */
        $model = new static();

        $model->setData($data);

        $model->is_new = false;
        return $model;

    }

    /**
     * @param array $parameters
     * @return ActiveRecordIterator
     */
    public static function findAll(array $parameters = array())
    {
        $table_name = self::model()->getTableName();

        $query = QueryBuilder::Select($table_name)->setWhere($parameters);

        $result = self::model()->query($query);

        if ($result->isEmpty()) {
            return null;
        }

        return new ActiveRecordIterator($result, self::model());
    }


    public static function count(array $parameters = array())
    {
        $table_name = self::model()->getTableName();
        $query = QueryBuilder::Select($table_name)->setWhere($parameters);
        $query->setSelectFields(array('count' => 'COUNT(*)'));
        $result = self::model()->query($query);
        $data = $result->fetch();
        return (int)$data['count'];
    }

    public static function getById($id)
    {
        $parameters = array('id' => $id);
        return self::find($parameters);
    }

    /**
     * @return Result
     */
    public function save()
    {

        if (empty($this->data)) {
            return false;
        }

        if ($this->is_new) {
            $query = QueryBuilder::Insert($this->getTableName())->setData($this->data);
        } else {
            $query = QueryBuilder::Update($this->getTableName())->setData($this->data);
            $pk_name = $this->getPk();
            $pk_value = $this->data[$this->getPk()];
            $query->setWhere(array($pk_name => $pk_value));
        }

        return $this->query($query);
    }

    /**
     * @param QueryBuilder $query
     * @return Result|null
     * @throws FatalException
     * @throws \Framework\Exceptions\MySQL
     */
    public function query(QueryBuilder $query)
    {
        return self::$db_connection->query($query);
    }

}