<?php

namespace MVC;

use MVC\Object;
use MVC\Controller\Controller;

class Dispatcher extends Object {
    public function autoload_controller($name) {
       if (class_exists($name)) {
           return true;
       }
       if (defined('MVC_CONTROLLERS')) {
           $file = MVC_CONTROLLERS . DIRECTORY_SEPARATOR . $name . '.php';
       }
       if (file_exists($file)) {
           require_once($file);
           return true;
       }
       return false;
    }

    public function run($params) {
        if (isset($params['url'])) {
            spl_autoload_register(array($this, 'autoload'));
            $data = preg_split('/\//', $params['url']);
            $controller = ($data[0] ? ucfirst(strtolower($data[0])) : '') . 'Controller';
            $method = $data[1] | 'index';
            if (!$this->autoload_controller($controller)) {
                return false;
            }
            $Controller = new $controller();
            if (method_exists($Controller, $method)) {
                $params = array_slice($data, 2);
                call_user_func_array(array($Controller, $method), $params);
            }
            return true;
        }
	return false;
    }
}
