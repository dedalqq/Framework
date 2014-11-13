<?php

namespace Framework;


class Date
{

    static private $instance = null;

    public static function i()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function date($unix_time) {
        return date('d.m.Y', $unix_time);
    }

    public function dateTime($unix_time) {
        return date('d.m.Y H:i', $unix_time);
    }
} 