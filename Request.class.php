<?php

namespace Framework;

class Request {

    public static function getInt($field, $data, $default = null) {
        if (isset($data[$field])) {
            return (int)$data[$field];
        }
        return $default;
    }

}