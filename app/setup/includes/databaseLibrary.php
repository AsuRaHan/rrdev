<?php

use RedBeanPHP\R;

class Database
{

    function test_database($data)
    {

        $host = $data['db_host'];
        $port = $data['db_port'];
        $login = $data['db_user'];
        $pass = $data['db_password'];
        $dbname = $data['db_name'];
        $dbdrv = $data['db_driver'] ?: 'sqlite';


        switch (strtolower($dbdrv)) {
            case "mariadb":
                R::setup("mysql:host=$host:$port;dbname=$dbname", $login, $pass);
                break;
            case "postgresql":
                R::setup("pgsql:host=$host:$port;dbname=$dbname", $login, $pass);
                break;
            case "sqlite":
                R::setup('sqlite:' . SITE_DIR . 'sqlite.db');
                break;
            case "cubrid":
                R::setup("cubrid:host=$host;port=$port;dbname=$dbname", $login, $pass);
                break;
        }
        if (!R::testConnection()) {
//            die("$dbdrv ошибка бaзы данных $host:$port. неудалось установить соединение c БД $dbname");
            return false;
        }
        return true;

    }

    function create_tables($data)
    {
//		$mysqli = new mysqli($data['hostname'],$data['username'],$data['password'],$data['database']);
//		if(mysqli_connect_errno())
//			return false;
//		$query = file_get_contents('assets/database.sql');
//		$mysqli->multi_query($query);
//		$mysqli->close();
        return true;
    }

}
