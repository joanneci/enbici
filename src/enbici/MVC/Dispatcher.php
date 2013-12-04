<?php

namespace enbici\MVC;

class Dispatcher extends Object {
    protected $model;
    protected $view;
    protected $controller;

    public function __construct($options = array()) {
        $options = array_merge(array(
            'base'       => $this->guessBasePath(),
            'model'      => '/model',
            'view'       => '/view',
            'controller' => '/controller'
        ), $options);
        extract($options);
        // absolute paths can be project or filesystem relative, so let's first
        // check if it exists somewhere in the project, otherwise will assume
        // it's an absolute path relative to the filesystem
        $this->model = (is_readable($base.$model) ? $base.$model : $model);
        $this->view = (is_readable($base.$view) ? $base.$view : $view);
        $this->controller = (is_readable($base.$controller) ? $base.$controller : $controller);
    }

    protected function guessBasePath() {
        $path = dirname(__FILE__);
        $regex = '/(.*)(?:\\' . DIRECTORY_SEPARATOR . 'vendor' . '\\' . DIRECTORY_SEPARATOR . ').*/';
        if (preg_match($regex, $path, $matches)) {
            return $matches[1];
        } else {
            return dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
        }
    }


    public function autoload($name) {
        if (class_exists($name)) {
            return null;
        }
        if (preg_match('/Model$/', $name)) {
            if ($name == 'Model') {
                $file = $this->model . DIRECTORY_SEPARATOR . 'Model.php';
            } else {
                $name = preg_replace('/Model$/', '', $name);
                $file = $this->model . DIRECTORY_SEPARATOR . $name . '.php';
            }
        }
        if (preg_match('/Controller$/', $name)) {
            if ($name == 'Controller') {
                $file = $this->controller . DIRECTORY_SEPARATOR . 'Controller.php';
            } else {
                $name = preg_replace('/Controller$/', '', $name);
                $file = $this->controller . DIRECTORY_SEPARATOR . $name . '.php';
           }
        }
        if (preg_match('/View$/', $name)) {
            if ($name == 'View') {
                $file = $this->view . DIRECTORY_SEPARATOR . 'View.php';
            } else {
                $name = preg_replace('/View$/', '', $name);
                $file = $this->view . DIRECTORY_SEPARATOR . $name . '.php';
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
            if (!class_exists($controller)) {
                return false;
            }
            $Controller = new $controller();
            $method = $data[1] ? $data[1] : 'index';
            if (method_exists($Controller, $method)) {
                $params = array_slice($data, 2);
                call_user_func_array(array($Controller, $method), $params);
            }
            return true;
        }
        return false;
    }
}
