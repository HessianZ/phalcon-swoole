<?php
namespace PhalconSwoole;

use Phalcon\Di\Service;

use PhalconSwoole\Session\Adapter\Files as SessionAdapter;

class Di extends \Phalcon\Di\FactoryDefault
{

    /**
     * Phalcon\Di\FactoryDefault constructor
     */
    public function __construct(\swoole_http_request $swooleRequest, \swoole_http_response $swooleResponse)
    {
        parent::__construct();

        $request  = new Request();
        $response = new Response();

        $request->setSwooleRequest($swooleRequest);
        $response->setSwooleResponse($swooleResponse);

        $this->setShared('request', $request);
        $this->setShared('response', $response);

        $this->_services['cookies'] = new Service('cookies', Cookies::class, true);
        $this->_services['session'] = new Service('session', SessionAdapter::class, true);
	}
}
