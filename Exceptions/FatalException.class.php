<?php

namespace Framework\Exceptions;

class FatalException extends \Exception {

    public function __construct($message = "") {
        parent::__construct($message, 500, null);
    }

}