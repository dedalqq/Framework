<?php

namespace Framework\Mongo;


abstract class AbstractModel
{

    const TYPE_INT = 1;

    const TYPE_STRING = 2;

    const TYPE_ARRAY = 3;

    private $data = array();

    /** @var null|\MongoId */
    private $ref_id = null;

    /**
     * @var Connection
     */
    private static $connection = null;

    abstract public function getProperties();

    abstract public function getCollectionName();

    public function __set($name, $value)
    {
        return $this->setValue($name, $value);
    }

    function __get($name)
    {
        if (isset($this->data[$name])) {

            $property = $this->getProperties();
            if ($property[$name] instanceof self) {

                /** @var $object self */
                $object = clone $property[$name];
                $data = $object->getCollection()->findOne(array('_id' => $this->data[$name]['$id']));
                if (is_null($data)) {
                    return null;
                }
                $object->data = $data;
                return $object;
            }

            return $this->data[$name];
        }

        return null;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function setRefId(\MongoId $ref_id)
    {
        $this->ref_id = $ref_id;
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

    public function escapeValue($name, $value)
    {
        $property = $this->getProperties();

        if (
            $property[$name] instanceof self
            && get_class($property[$name]) == get_class($value)
        ) {
            /** @var $value self */
            if (empty($value->ref_id)) {
                $value->save();
            }
            return \MongoDBRef::create($value->getCollectionName(), $value->ref_id);
        } elseif ($property[$name] == self::TYPE_INT) {
            return (int)$value;
        } elseif ($property[$name] == self::TYPE_STRING) {
            return (string)$value;
        } elseif ($property[$name] == self::TYPE_ARRAY) {
            return (array)$value;
        }

        return null;
    }

    /**
     * @param Connection $connection
     */
    public static function setConnection(Connection $connection)
    {
        self::$connection = $connection;
    }

    /**
     * @return static
     */
    public static function model()
    {
        return new static();
    }

    /**
     * @return \MongoCollection
     */
    protected function getCollection()
    {
        return self::$connection->getDataBase()->selectCollection($this->getCollectionName());
    }

    /**
     * @param array $property
     * @return static
     */
    public static function find(array $property = array())
    {
        $collection = self::model()->getCollection();
        $data = $collection->findOne($property);

        if (is_null($data)) {
            return null;
        }

        $object = self::model();
        $object->data = $data;
        $object->ref_id = $data['_id'];
        return $object;
    }

    public static function findAll(array $property = array())
    {
        $collection = self::model()->getCollection();
        $result = $collection->find($property);

        if (is_null($result)) {
            return null;
        }

        return new Iterator($result, self::model());
    }

    public function save()
    {
        $collection = $this->getCollection();

        try {
            $collection->save($this->data);
            $this->ref_id = $this->data['_id'];
            return true;
        } catch (\MongoException $exception) {
            echo '<pre>';
            var_dump($exception);
            echo '</pre>';
        }

        return false;
    }

    public function update(array $data = array())
    {
        $collection = $this->getCollection();
        return $collection->update(array('_id' => $this->ref_id), $data);
    }

    public function delete()
    {
        $this->getCollection()->remove(array('_id' => $this->ref_id));
    }
}