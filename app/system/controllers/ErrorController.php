<?php
namespace rrdev\controllers;

use rrdev\core\Controller;

class ErrorController extends Controller {
    public function notFound() {
        if (!headers_sent()) {
            header("HTTP/1.0 404 Not Found");
            header("Status: 404 Not Found");
            header("Content-Type: text/html; charset=UTF-8");
        }
        return 'error/404';
    }

    public function internalServerError() {
        return 'error/500';
    }

    public function badRequest() {
        return 'error/400';
    }
}