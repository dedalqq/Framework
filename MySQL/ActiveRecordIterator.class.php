<?php

namespace Framework\MySQL;


class ActiveRecordIterator implements \Iterator, \Countable {

    /** @var Result  */
    private $result = null;

    private $model = null;

    /** @var ActiveRecord */
    private $current_model = null;

    function __construct(Result $result, ActiveRecord $model)
    {
        $this->result = $result;
        $this->model = $model;

    }

    public function current()
    {
        return $this->current_model;
    }

    public function next()
    {
        $data = $this->result->fetch();

        if (empty($data)) {
            $this->current_model = null;
            return null;
        }

        $this->current_model = clone $this->model;
        $this->current_model->setData($data);

        return null;
    }

    public function key()
    {
        if (is_null($this->current_model)) {
            return null;
        }

        return $this->current_model->getPkValue();
    }

    public function valid()
    {
        return !is_null($this->current_model);
    }

    public function rewind()
    {
        $this->result->rewind();
        $this->next();
    }

    public function count()
    {
        return $this->result->count();
    }

    public function fetch() {
        $model = $this->current();
        $this->fetch();
        return $model;
    }
}