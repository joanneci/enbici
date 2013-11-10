<?php

namespace MVC;

use MVC\Dispatcher;

class Bootstrap {
    class __construct() {
        $dispatcher = new Dispatcher();
        $dispatcher->dispatch();
    }
}
