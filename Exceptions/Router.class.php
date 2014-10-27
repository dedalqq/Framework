<?php

namespace Framework\Exceptions;

class Router extends \Exception {

    public function __construct($message = "") {
        parent::__construct($message, 404, null);
    }

}