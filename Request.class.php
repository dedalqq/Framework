<?php

namespace Framework;

class Request {

    public static function getInt($field, $data = null, $default = null) {

        if (is_null($data)) {
            $data = $_REQUEST;
        }

        if (isset($data[$field])) {
            return (int)$data[$field];
        }
        return $default;
    }

    public static function getString($field, $data = null, $default = null) {

        if (is_null($data)) {
            $data = $_REQUEST;
        }

        if (isset($data[$field])) {
            return (string)$data[$field];
        }
        return $default;
    }
}