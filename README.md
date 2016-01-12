# Phalcon-Swoole


## Phalcon Framework
Phalcon is an open source web framework delivered as a C extension for the PHP language providing high performance and lower resource consumption.

* Homepage: https://phalconphp.com
* Github: https://github.com/phalcon/cphalcon


## Swoole
Swoole is an event-based & concurrent framework for internet applications, written in C, for PHP.

* Homepage: http://www.swoole.com
* Github: https://github.com/swoole/swoole-src


## Todolist
- [x] 路由解析
- [ ] 会话隔离
    - [x] Get
    - [x] Post
    - [x] Cookie
    - [ ] Session
- [ ] 数据库连接等资源的复用和释放处理策略？


## Usage
You need to add these lines below to `composer.json` and run `composer update`.
```
{
    "require": {
        "HessianZ/phalcon-swoole": "dev-master"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/HessianZ/phalcon-swoole"
        }
    ]
}
```

## Most important

```php
$di = new Di($request, $response);
$app->setDI($di);

```

## Example
```php
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

```
