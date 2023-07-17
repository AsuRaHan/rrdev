<?php

namespace rrdev;

use rrdev\core\ControllerResolver;
use rrdev\core\Router;
use rrdev\core\session\SessionManager;
use RedBeanPHP\Facade as R;
/**
 * Главный класс всего приложения
 * 
 */
final class Core {
    private static $router;
    private static $route;



    /**
     * Основной метод запускает все приложение. Так называемая точка входа
     * 
     */
    public static function run() {
        Config::init();
        date_default_timezone_set(Config::get('app.timezone'));

        self::initWhoops();
        self::i18n();
//        self::dbInit();
//        SessionManager::init();
        self::$router = new Router();
        self::$route = self::$router->match();
//        dd(self::$route);

        $ExecRetVal = self::callRoute();

        if (is_string($ExecRetVal)) {
            $viw = \rrdev\core\View::getInstance();
            $viw->mainbody = $ExecRetVal;
            echo $viw->execute();
        } elseif (is_array($ExecRetVal)) {
            if (!headers_sent()) {
//                header("Access-Control-Allow-Origin: *");
                header("Content-Type: application/json; charset=UTF-8");
            }
            echo json_encode($ExecRetVal, JSON_UNESCAPED_UNICODE);
        } else {
            if (!headers_sent()) {
                header("HTTP/1.1 400 Bad Request");
                header("Status: 400 Bad Request");
            }
            dump(self::$ExecRetVal);
        }
//        echo 'work time = ' . (microtime(true)- TIME_START);
    }
    /**
     * Метод запускает крон задачу приложения.
     *
     */
    public static function RunCron() {
//        echo 'Cron service start';
        Config::init();
        echo SessionManager::GarbageCollector();
//        echo 'Cron service stop';
    }

    /**
     * @return mixed
     */
    public static function callRoute()
    {
        $controllerName = self::$route['controller']?:'IndexController';
        $action = self::$route['action']?:'index';
        $params = self::$route['params'];
        $controller = "\\rrdev\\controllers\\$controllerName";

        if (!ControllerResolver::isValidController($controller)) {
            $controller = '\\rrdev\\controllers\\ErrorController';
            $action = 'notFound';
            $params = [];
        }
        if (!ControllerResolver::isValidAction($controller, $action)) {
            $controller = '\\rrdev\\controllers\\ErrorController';
            $action = 'notFound';
            $params = [];
        }

        $objectController = new $controller();
        return call_user_func_array([$objectController, $action], $params);

    }
    /**
     * Включает отладчик Whoops
     * https://github.com/filp/whoops
     *
     * https://phpprofi.ru/blogs/post/77
     *
     */
    private static function initWhoops() {
        $whoops = new \Whoops\Run;
        $whoops_pretty_page_handler = new \Whoops\Handler\PrettyPageHandler();
        $whoops_pretty_page_handler->setEditor('vscode');


        $whoops->pushHandler($whoops_pretty_page_handler);

//        $whoops->pushHandler(function ($e) {
//
//        });

        $monolog_multiline_formatter = new \Monolog\Formatter\HtmlFormatter(); //new \Monolog\Formatter\LineFormatter(null, null, true);
        $monolog_error_log_handler = new \Monolog\Handler\ErrorLogHandler();
        $monolog_error_log_handler->setFormatter($monolog_multiline_formatter);
        $monolog_logger_error_log = new \Monolog\Logger('whoops_logger', [$monolog_error_log_handler]);
        $monolog_logger_error_log->pushHandler(new \Monolog\Handler\StreamHandler(SITE_DIR . 'error.log'));

        $whoops_plain_text_handler = new \Whoops\Handler\PlainTextHandler();
        $whoops_plain_text_handler->loggerOnly(true);
        $whoops_plain_text_handler->setLogger($monolog_logger_error_log);
        $whoops->pushHandler($whoops_plain_text_handler);

        $monolog_browser_console_handler = new \Monolog\Handler\BrowserConsoleHandler();
        $monolog_browser_console_handler->setFormatter($monolog_multiline_formatter);
        $monolog_browser_console_logger = new \Monolog\Logger('whoops_browser_console_logger', [$monolog_browser_console_handler]);
        $whoops_plain_text_handler2 = new \Whoops\Handler\PlainTextHandler();
        $whoops_plain_text_handler2->loggerOnly(true);
        $whoops_plain_text_handler2->setLogger($monolog_browser_console_logger);
        $whoops->pushHandler($whoops_plain_text_handler2);

        $whoops->register();
    }

    public static function databaseInit() {
        $host = Config::get('database.host');
        $port = Config::get('database.port');
        $username = Config::get('database.username');
        $pass = Config::get('database.password');
        $dbname = Config::get('database.dbname');
        $driver = Config::get('database.driver');

        switch (strtolower($driver)) {
            case "mariadb":
                R::setup("mysql:host=$host:$port;dbname=$dbname", $username, $pass);
                break;
            case "postgresql":
                R::setup("pgsql:host=$host:$port;dbname=$dbname", $username, $pass);
                break;
            case "sqlite":
                R::setup('sqlite:' . SITE_DIR . 'sqlite.db');
                break;
            case "cubrid":
                R::setup("cubrid:host=$host;port=$port;dbname=$dbname", $username, $pass);
                break;
        }
        if (!R::testConnection()) {
//            R::fancyDebug(false);
            die("$driver ошибка соединения с $host:$port. не удалось установить соединение c БД $dbname");
            //throw new \Exception(__METHOD__ . " $driver ошибка соединения с $host:$port. не удалось установить соединение c БД $dbname");
        }
        //for version 5.3 and higher
        //optional but recommended
        R::useFeatureSet('novice/latest');
        R::useJSONFeatures(true);
        R::freeze(filter_var(Config::get('database.frozen'), FILTER_VALIDATE_BOOLEAN));
        R::ext("xDispense", function ($table_name) {
            return R::getRedBean()->dispense($table_name);
        });
    }

    /**
     * Устанавливает язык приложения
     * @return String
     */
    public static function i18n($domain = 'rrdev', $locale = 'default') {
        if ($locale === 'default') {
            $locale = filter_input(INPUT_SERVER, "HTTP_ACCEPT_LANGUAGE");
        }
        $dir = APP . 'locale';
        putenv('LC_ALL=' . $locale);
        putenv('LANG=' . $locale);
        putenv('LANGUAGE=' . $locale);
        setlocale(LC_ALL, $locale);
        bindtextdomain($domain, $dir);
        bind_textdomain_codeset($domain, 'UTF-8');
        textdomain($domain);
//        var_dump($locale, $dir, gettext("Hello world"));
    }

    /**
     * Выполняет контроллер!
     * @return String
     */
    public static function Exec($Controller = '', $Action = '', $Param = []) {
//        dd($Controller,$Action,$Param);
        $ctrl = 'rrdev\\controllers\\' . $Controller;

        if (class_exists($ctrl)) {
            $objectCtrl = new $ctrl();
            if (method_exists($objectCtrl, $Action)) {

                if (count($Param)) {
                    return call_user_func_array([$objectCtrl, $Action], $Param);
                } else {
                    return call_user_func([$objectCtrl, $Action]);
                }
            }
            if (!headers_sent()) {
                header("HTTP/1.1 405 Method Not Allowed");
                header("Status: 405 Method Not Allowed");
            }

            return "<h1>405 Method Not Allowed</h1>" . __METHOD__ . "<h5> Контроллер <b style=\"color: red;\">" . $Controller . "</b> Не имеет метода <b style=\"color: red;\">$Action()</b></h5>";
        }
        if (!headers_sent()) {
            header("HTTP/1.1 523 Origin Is Unreachable");
            header("Status: 523 Origin Is Unreachable");
        }

        return "<h1>523 Origin Is Unreachable</h1>" . __METHOD__ . "<h5> Нет исполнительного контроллера <b style=\"color: red;\">$Controller</b></h5>";
    }

    /**
     * Получаем контроллер и метод.
     * Данная функция находит в УРЛ тот контроллер и метод на который пришел запрос
     * заполняет переменные роутинга
     * и если необходимо настраивает пришедший в УРЛ запрос
     * @return Boolean
     */
    private static function GetControllerAndAction() {
        $access = false;
        self::GetURI();
        $cfg = self::$globalConfig['App_Config_Dir'] . self::$globalConfig['App_Router_Config_File'];
        if (file_exists($cfg)) {
            $routes = include($cfg);
        } else {
//            throw new \Exception(__METHOD__ . " Конфигурационный фаил роутинга $cfg не найден. продолжить невозможно");
//            return false;
            $routes = [];
        }

        // проверяю запрос на соответствие регулярному выражению
        foreach ($routes as $uriPattern => $path) {
            if (!preg_match("~$uriPattern~", self::$URI, $matches)) {
                continue;
            } else {
                if (is_object($path)) {
                    // https://github.com/devcoder-xyz/php-user-authentication/blob/master/src/Core/UserManager.php
                    dd($uriPattern, $path(), $matches);
                }
            }
            // получаем внутренний путь из внешнего согласно правилам маршрутизации
            $access = preg_replace("~$uriPattern~", $path, self::$URI);
        }
        if (!$access) {
            if (empty(self::$URI)) {
                $access = self::$globalConfig['Router_Default_Controller'] . "/" . self::$globalConfig['Router_Default_Action']; //
            } else {
                $access = self::$URI;
            }
        }
        $segments = explode('/', $access);
        $controlerName = ucfirst(array_shift($segments)) . 'Controller';
        $controllerClass = 'rrdev\\controllers\\' . $controlerName;
        if (class_exists($controllerClass)) {
//            $action = ucfirst(array_shift($segments));
            $action = array_shift($segments);
//            dd($action);
            if (empty($action)) {
                $action = ucfirst(self::$globalConfig['Router_Default_Action']);
            }
            $actionName = $action . 'Action';
            self::$ActionName = $actionName;
            self::$ControllerName = $controlerName;
            self::$ParametersArray = $segments;
            return true;
        }
        self::$ControllerName = null;
        return false;
    }

    /**
     * Поиск файла по имени во всех папках и подпапках
     * @param string $fileName - искомый файл
     * @param string $folderName - пусть до папки
     */
    public static function SearchFile($fileName, $folderName) {
        // перебираем пока есть файлы
        if (!is_dir($folderName)) {
            return false;
        }
        $dirArray = scandir($folderName);
        foreach ($dirArray as $file) {
            if ($file != "." && $file != "..") {
                // если файл проверяем имя
                if (is_file($folderName . DIRECTORY_SEPARATOR . $file)) {
                    // если имя файла искомое,
                    // то вернем путь до него
                    if ($file == $fileName) {
                        return $folderName . DIRECTORY_SEPARATOR . $file;
                    }
//                    echo $folderName.'\\'.$file.'<br>';
                }
                // если папка, то рекурсивно
                // вызываем SearchFile
                if (is_dir($folderName . DIRECTORY_SEPARATOR . $file)) {
                    $retVal = self::SearchFile($fileName, $folderName . DIRECTORY_SEPARATOR . $file);
                    if ($retVal) { // если фуекция что-то вернула то выходим
                        return $retVal;
                    }
                }
            }
        }
    }

    public static function Redirect($url, $permanent = false) {
        if (headers_sent() === false) {
            header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
        }
        return '<script type="text/javascript">window.location = "' . $url . '"</script>';
    }

// Encrypt Function
    public static function StrEncrypt($encrypt, $key) {
        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($encrypt, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        return base64_encode($iv . $hmac . $ciphertext_raw);
    }

// Decrypt Function
    public static function StrDecrypt($decrypt, $key) {
        $c = base64_decode($decrypt);
        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        if (hash_equals($hmac, $calcmac)) {
            return $plaintext;
        }
    }

    public static function BrouserHash() {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $ua = $_SERVER['HTTP_USER_AGENT'] . $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        } else {
            $ua = implode(" - ", $_SERVER);
        }

        return md5($_SERVER['REMOTE_ADDR'] . $ua);
    }

    public static function headerError($errnumber = 400) {
        http_response_code($errnumber);
        if (!headers_sent()) {
            switch ($errnumber) {
                case 400:
                    header("HTTP/1.1 400 Bad Request");
                    header("Status: 400 Bad Request");
                    break;
                case 403:
                    header("HTTP/1.1 403 Forbidden");
                    header("Status: 403 Forbidden");
                    break;
                case 404:
                    header("HTTP/1.1 404 Not Found");
                    header("Status: 404 Not Found");
                    break;
                case 405:
                    header("HTTP/1.1 405 Method Not Allowed");
                    header("Status: 405 Method Not Allowed");
                    break;
                default:
                    header("HTTP/1.1 503 Service Unavailable");
                    header("Status: 503 Service Unavailable");
            }
        }
    }

    public static function RusTextTranslit($str) {
        $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
        $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
        return str_replace($rus, $lat, $str);
    }

}
