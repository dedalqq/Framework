<?php

namespace Framework;

abstract class AbstractRouter {

    public function getMethod() {
        return 'actionIndex';
    }

    public function getRequestUrl() {
        return $_SERVER['REDIRECT_URL'];
    }

    /**
     * @return AbstractController
     */
    public function getController() {
       return null;
    }

}