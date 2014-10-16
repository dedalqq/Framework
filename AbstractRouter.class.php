<?php

namespace Framework;

abstract class AbstractRouter {

    /**
     * @return AbstractController
     */
    public function getController() {
       return null;
    }

}