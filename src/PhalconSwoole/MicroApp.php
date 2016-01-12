<?php

namespace HessianZ\PhalconSwoole;

class MicroApp extends \Phalcon\Mvc\Micro
{
    public function __construct($dependencyInjector)
    {
        parent::__construct($dependencyInjector);

        ini_set('session.use_cookies', 0);
    }

}
