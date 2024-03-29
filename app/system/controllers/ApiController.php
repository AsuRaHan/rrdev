<?php
namespace rrdev\controllers;

use rrdev\core\Controller;
use rrdev\core;

class ApiController extends Controller {



    public function v1($model = null, $method = null, $id = 0) {
        if (!$model) {
            core::headerError(405);
            return ['error' => _('Model name not set')];
        }
        if (!$method) {
            core::headerError(405);
            return ['error' => _('Method name not set')];
        }
        $model = 'rrdev\\api\\' . ucfirst($model) . 'Api';
        if (!class_exists($model)) {
            core::headerError();
            return ['error' => _('Model Class not exists'). $model];
        }

        $clsModel = new $model();
        $method = ucfirst($method).'Action';
        if (!method_exists($clsModel, $method)) {
            core::headerError(405);
            return ['error' => _('Model Class not have Method') .' '. $method];
        }
        $param = $this->json;
        if (!$param) {
            $param['id'] = $id;
            $param['data'] = array_merge($_POST,$_GET);
        }

        $ret = call_user_func_array([$clsModel, $method], [$param]);

        return $ret;
    }

    public function index($model = null, $method = null) {
        return [$this,$method];
    }

}
