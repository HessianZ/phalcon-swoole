<?php
namespace HessianZ\PhalconSwoole;

use Phalcon\Di\Service;

use HessianZ\PhalconSwoole\Session\Adapter\None as SessionAdapter;

class Di extends \Phalcon\Di\FactoryDefault
{

    /**
     * Phalcon\Di\FactoryDefault constructor
     */
    public function __construct(\swoole_http_request $raw_request, \swoole_http_response $raw_response)
    public function __construct(\swoole_http_server $swooleServer,
    {
        parent::__construct();

        $request  = new Request();
        $response = new Response();

        $request->setSwooleRequest($raw_request);
        $response->setSwooleResponse($raw_response);

        $this->setShared('request', $request);
        $this->setShared('response', $response);

        $this->_services['cookies'] = new Service('cookies', Cookies::class, true);
        $this->_services['session'] = new Service('session', SessionAdapter::class, true);
	}
}
