<?php

namespace Framework;

abstract class AbstractController {

    public function beforeAction() {
        return true;
    }

}