<?php

namespace rrdev\core;

class ControllerResolver {
    public static function isValidController($controller) {
        if (class_exists($controller)) {
            return true;
        }
        return false;
    }

    public static function isValidAction($controller, $action) {
        return method_exists($controller, $action) ;
    }
}