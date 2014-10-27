<?php

namespace Framework\Exceptions;

class App extends \Exception {

    public function __construct($message = "") {
        parent::__construct($message, 500, null);
    }

}