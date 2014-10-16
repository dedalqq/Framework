<?php

namespace Framework\MySQL;

class MySQLResult {

    /**
     * @var bool
     */
    private $is_success = false;

    /**
     * @var \mysqli_result
     */
    private $result = null;

    public function __construct($is_success, \mysqli_result $result = null) {
        $this->is_success = $is_success;
        $this->result = $result;
    }

    public function fetchAll() {

        if (is_null($this->result)) {
            return null;
        }

        return $this->result->fetch_all();
    }

    public function fetch() {

        if (is_null($this->result)) {
            return null;
        }

        return $this->result->fetch_array();
    }

    /**
     * @return bool
     */
    public function isSuccess() {
        return (bool)$this->is_success;
    }

}