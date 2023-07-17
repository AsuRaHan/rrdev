<?php

namespace rrdev\controllers;

use rrdev\core\Controller;
use rrdev\core\session\SessionManager;
use \rrdev\core;

defined('ROOT') OR die('No direct script access.');

class AdminController extends Controller {

//    public function __construct() {
//        parent::__construct();
//        $LoggedUser = SessionManager::get('User');
////        dd($LoggedUser);
//        if (is_null($LoggedUser)or($LoggedUser['role'] < 900)) {
//            SessionManager::set('redirectfrom', filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL));
//            core::Redirect('/user/login');
//            die('access denied');
//        }
//    }

    public function IndexAction($param = null) {
        $this->View->DashboardContent = $this->View->Render('main.html');
        return $this->View->Render('dashboard.html');
    }

}
