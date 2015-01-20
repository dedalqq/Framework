<?php

namespace Framework;

class Request {

    private static function getFormData() {

        static $form_data = null;

        if (is_null($form_data)) {

            $form_data = array();

            if (
                !empty($_REQUEST['form_data'])
                && is_array($_REQUEST['form_data'])
            ) {
                foreach ($_REQUEST['form_data'] as $item) { // todo вот это можно оптимизировать
                    $form_data[$item['name']] = $item['value'];
                }
            }
        }

        return $form_data + $_REQUEST;
    }

    public static function getInt($field, $default = null, $data = null) {

        if (is_null($data)) {
            $data = self::getFormData();
        }

        if (isset($data[$field])) {
            return (int)$data[$field];
        }

        return $default;
    }

    public static function getString($field, $default = null, $data = null) {

        if (is_null($data)) {
            $data = self::getFormData();
        }

        if (isset($data[$field])) {
            return (string)$data[$field];
        }

        return $default;
    }
}