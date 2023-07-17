<?php

namespace rrdev\core;

defined('ROOT') OR die('No direct script access.');

/**
 * Description of Model
 *
 * @author Ivan P Kolotilkin
 * 
 * https://youtu.be/iU8zlbkpwyo
 */
use RedBeanPHP\Facade as R;
use rrdev\Core;

class Model extends R {

    public $tableName = '';

    public function __construct($name = null) {
        if ($this->tableName === '') {
            $a = explode('\\', get_class($this));
            $this->tableName = mb_strtolower(str_replace('Model', '', end($a)));
        }elseif ($name) {
            $this->tableName = mb_strtolower($name);
        }
        if (!R::testConnection()) {
            Core::databaseInit();
        }
    }

    public function set($data = null) {
        if (!$data)
            return FALSE;
        if (isset($data['id'])) {
            $table = $this->load($this->tableName, intval($data['id']));
            unset($data['id']);
            $table->import($data);
            $table->updatedatetime = date('Y-m-d H:i:s');
            return $this->store($table);
        } else {
            $table = $this->dispense($this->tableName);
            $table->createdatetime = date('Y-m-d H:i:s');
            $table->import($data);
            return $this->store($table);
        }
        return FALSE;
    }

    public function getById($id) {
        if (!$id)
            return null;
        return $this->load($this->tableName, intval($id));
    }

    public function getList($data = null) {
        if (!$data)
            return FALSE;
        $start = $data['start'] ? intval($data['start']) : 0;
        $limit = $data['limit'] ? intval($data['limit']) : 10;
        $list['count'] = $this->count($this->tableName);
//        $list['columns'] = $this->inspect($this->tableName);
        if (isset($data['orderby']) and $data['orderby'] != '') {
            $order['orderby'] = $data['orderby'];
            if ($data['dir'] != '') {
                $order['dir'] = $data['dir'];
            } else {
                $order['dir'] = 'ASC';
            }
        } else {
            $order = null;
        }

        if (is_array($order)) {
            $tempbean = $this->findAll($this->tableName, 'ORDER BY ' . $order['orderby'] . ' ' . $order['dir'] . ' LIMIT ' . $start . ', ' . $limit);
        } else {
            $tempbean = $this->findAll($this->tableName, 'LIMIT ' . $start . ', ' . $limit);
        }
        if ($tempbean) {
//            $tempbean = $this->exportAll($tempbean, true);
            $list['data'] = $tempbean;
            return $list;
        }
        return FALSE;
    }

    public function dellete($data = null) {
        if (!$data)
            return FALSE;
        return $this->trash($this->load($this->tableName, $data['id']));
    }

}
