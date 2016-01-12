<?php
/**
 * Created by PhpStorm.
 * User: hessian
 * Date: 1/8/16
 * Time: 11:39
 */

use Phalcon\Http\ResponseInterface;

use HessianZ\PhalconSwoole\MicroApp;
use HessianZ\PhalconSwoole\Di;

define('BASE_DIR', dirname(__DIR__));
define('APP_PATH', BASE_DIR . '/app');

$config = include APP_PATH . '/config/config.php';

/**
 * Load composer loader
 */
require_once APP_PATH . '/vendor/autoload.php';

/**
 * Create swoole server
 */
$serv = new swoole_http_server("0.0.0.0", 9502);

$serv->on('Request', function($request, $response) use($config) {
    try {
        $app = new MicroApp();

        $di = new Di($request, $response);
        $app->setDI($di);

        $app->get('/', function() use($app) {
            return $app->response->setContent("Hello swoole.");
        });

        $app->notFound(function() {
            return "404";
        });

        $ret = $app->handle($request->server['request_uri']);

        if ($ret instanceof ResponseInterface) {
            $ret->send();
            $response->end($ret->getContent());
        } else if (is_string($ret)) {
            $response->end($ret);
        } else {
            $response->end();
        }
    } catch (\Exception $e) {
        echo $e->getMessage() . PHP_EOL;
        echo $e->getTraceAsString() . PHP_EOL;
    }
});

$serv->start();
