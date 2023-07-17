<?php
/**
 * Copyright 2023 Ivan P. Kolotilkin
 * 
 * logic@xaker.ru
 * 
 * +79372796383
 * 
 * https://vk.com/id131505651
 * 
 * https://github.com/AsuRaHan/rrdev
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
define('TIME_START', microtime(true)); // для подсчета времени работы скрипта
define('USE_MEM_START', memory_get_usage()); // то-же самое только для используемой памяти сервера
function resolvePlatform() {
    // Check PHP version.
    $minPhpVersion = '8.0'; // Update PHP.
    if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
        die(sprintf(
            'Your PHP version must be %s or higher to run Application. Current version: %s',
            $minPhpVersion,
            PHP_VERSION
        ));
    }
    $requiredExtensions = [
        'curl',
        'gettext',
        'json',
        'mbstring',
        'xml',
    ];

    $missingExtensions = [];

    foreach ($requiredExtensions as $extension) {
        if (!extension_loaded($extension)) {
            $missingExtensions[] = $extension;
        }
    }

    if ($missingExtensions !== []) {
//            throw FrameworkException::forMissingExtension(implode(', ', $missingExtensions));
        die(sprintf(
            'The framework needs the following extension(s) installed and loaded: %s.',
            implode(', ', $missingExtensions)
        ));
    }
}

resolvePlatform();
//ini_set('memory_limit', -1);

define('DS', DIRECTORY_SEPARATOR); // разделитель для путей к файлам
define('ROOT', dirname(__FILE__)); // защита файлов приложения от прямого доступа к ним
define('SITE_DIR', realpath(dirname(__FILE__)) . DS); // путь к корневой папке сайта getcwd()
define('APP', SITE_DIR . 'system' . DS); // путь к приложению
define('TEMPLATE_DIR', SITE_DIR . 'templates' . DS);
define('CONFIG_DIR', SITE_DIR . 'config' . DS); // папка с конфигами

// Проверим есть ли SSL
if ((isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) || $_SERVER['SERVER_PORT'] == 443) {
    $protocol = 'https://';
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
    $protocol = 'https://';
} else {
    $protocol = 'http://';
}
define('HTTP_SERVER', $protocol . $_SERVER['HTTP_HOST'] . '/');
//die(HTTP_SERVER);

define('COMPOSER', SITE_DIR . 'vendor' . DS . 'autoload.php');
if (file_exists(COMPOSER)) {
    require_once COMPOSER; // подключаем композер
} else {
    include 'composer_missing.php';
    die();
}

if (!getenv("ENV")) {
    try {
        $dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
        $dotenv->load();
        //$dotenv->required(['DB_HOST', 'DB_PORT', 'DB_USERNAME', 'DB_PASSWORD', 'DB_NAME', 'DB_DRIVER','DB_FROZEN']);
    } catch (Exception $e) {
        include SITE_DIR . 'setup' . DS . 'install.php';
        die();
    }
}
define('TIMEZONE', 'Europe/Ulyanovsk');

