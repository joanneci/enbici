<?php

namespace enbici\MVC;

class Dispatcher extends Object {
    public function autoload($name) {
       if (class_exists($name)) {
           return null;
       }
       if (preg_match('/Model$/', $name)) {
           if ($name == 'Model') {
              $file = MVC_MODEL . DIRECTORY_SEPARATOR . 'Model.php';
           } else {
               $name = preg_replace('/Model$/', '', $name);
               $file = MVC_MODEL . DIRECTORY_SEPARATOR . $name . '.php';
           }
       }
       if (preg_match('/Controller$/', $name)) {
           if ($name == 'Controller') {
              $file = MVC_CONTROLLER . DIRECTORY_SEPARATOR . 'Controller.php';
           } else {
               $name = preg_replace('/Controller$/', '', $name);
               $file = MVC_CONTROLLER . DIRECTORY_SEPARATOR . $name . '.php';
           }
       }
       if (preg_match('/View$/', $name)) {
           if ($name == 'View') {
              $file = MVC_VIEW . DIRECTORY_SEPARATOR . 'View.php';
           } else {
               $name = preg_replace('/View$/', '', $name);
               $file = MVC_VIEW . DIRECTORY_SEPARATOR . $name . '.php';
           }
       }
       
       if (file_exists($file)) {
           require_once($file);
           return true;
       }
       return false;
    }

    public function run($params) {
        spl_autoload_register(array($this, 'autoload'));
        if (isset($params['url'])) {
            $data = preg_split('/\//', $params['url']);
            $controller = ($data[0] ? ucfirst(strtolower($data[0])) : '') . 'Controller';
            $method = $data[1] | 'index';
            if (!class_exists($controller)) {
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
