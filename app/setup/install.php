<?php

if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST) {
    
	require_once('includes/taskCoreClass.php');
	require_once('includes/databaseLibrary.php');

	$core = new taskCoreClass();
	$database = new Database();

	if($core->checkEmpty($_POST) == true)
	{
            if($database->test_database($_POST) == false)
            {
                    $message = $core->show_message('error',"The database could not be connect, make sure your the host, username, password, database name is correct.");
            } 
            else if ($database->create_tables($_POST) == false)
            {
                    $message = $core->show_message('error',"The database could not be created, make sure your the host, username, password, database name is correct.");
            } 
            else if ($core->checkFile() == false)
            {
                    $message = $core->show_message('error',".env File not Empty");
            }
            else if ($core->write_env($_POST) == false)
            {
                    $message = $core->show_message('error',"The database configuration file could not be written, please chmod application/config/database.php file to 777");
            }
//            else if ($core->write_config($_POST) == false)
//            {
//                    $message = $core->show_message('error',"The config.php configuration file could not be written, please chmod application/config/config.php file to 777");
//            }		
            else {
//                if(empty($_POST['admin_email'])){
//                    $message = 'nextstep';
//                }else{
//                    $message = 'success';
//                }
                $message = 'success';
            }
	} else {
		$message = $core->show_message('error','The host, username, password, database name, and db driver are required.');
	}
	
	echo $message;
}else{
    include SITE_DIR . 'setup'. DS. 'wizard.php';
}