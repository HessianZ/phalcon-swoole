<?php

namespace PhalconSwoole;

class MicroApp extends \Phalcon\Mvc\Micro
{
    public function __construct($dependencyInjector = null)
    {
        parent::__construct($dependencyInjector);

        ini_set('session.use_cookies', 0);
    }

}
