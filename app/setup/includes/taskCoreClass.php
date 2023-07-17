<?php

class taskCoreClass {

    function checkEmpty($data) {
        if (!empty($data['db_host']) && !empty($data['db_port']) && !empty($data['db_user']) && !empty($data['db_password']) && !empty($data['db_name']) && !empty($data['db_driver'])) {
            return true;
        } else {
            return false;
        }
//        $empArr = ['hostname','username','password','database','driver'];
//        foreach ($empArr as $value) {
//            if (!empty($data[$value])) {
//                return true;
//            }
//        }
//        return false;
    }

    function show_message($type, $message) {
        return $message;
    }

    function getAllData($data) {
        return $data;
    }

    function write_env($data) {
        $template_path = SITE_DIR . 'setup'. DS. 'includes/.env.example';
        $output_path = SITE_DIR . '.env';

        $database_file = file_get_contents($template_path);

        $new = str_replace("%HOSTNAME%", $data['db_host'], $database_file);
        $new = str_replace("%USERNAME%", $data['db_user'], $new);
        $new = str_replace("%PASSWORD%", $data['db_password'], $new);
        $new = str_replace("%DATABASE%", $data['db_name'], $new);
        $new = str_replace("%DRIVER%", $data['db_driver'], $new);

        $handle = fopen($output_path, 'w+');
        @chmod($output_path, 0777);

        if (is_writable(dirname($output_path))) {

            if (fwrite($handle, $new)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function write_config($data) {
        $template_path = 'includes/config.php';

        $output_path = '../application/config/config.php';

        $config_file = file_get_contents($template_path);

        $new = str_replace("%BASE_URL%", $data['url'], $config_file);

        $handle = fopen($output_path, 'w+');
        @chmod($output_path, 0777);

        if (is_writable(dirname($output_path))) {

            if (fwrite($handle, $new)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function checkFile() {
        $output_path = SITE_DIR . '.env';

        if (!file_exists($output_path)) {
            return true;
        } else {
            return false;
        }
    }

}
