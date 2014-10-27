<?php

namespace Framework\Exceptions;

class Fatal extends \Exception {

    public function __construct($message = "") {
        parent::__construct($message, 500, null);
    }

}