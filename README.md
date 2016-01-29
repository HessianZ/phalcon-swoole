# Phalcon-Swoole
Phalcon很快，Swoole也很快，那他们俩加一块岂不是快上加快？这个项目的目的正是要让他俩成为一对亲密的好基友，解决Phalcon在Swoole环境下运行所遇到的各种问题。

# Question
在最初，我是希望整个运行过程是异步非阻塞的，但是如此一来改造难度和使用的复杂度会高很多，那时候phalcon就不是phalcon了，意义不大。
而在同步阻塞模式下swoole_http_server->setGlobal就已经能自动重写超全局变量GPCS，框架里要做的可能只有session的处理了。

### 同步阻塞函数
* mysql、mysqli、pdo以及其他DB操作函数
* sleep、usleep
* curl
* stream、socket扩展的函数
* swoole_client同步模式
* memcache、redis扩展函数
* file_get_contents/fread等文件读取函数
* swoole_server->taskwait
* swoole_server->sendwait
* swoole_server的PHP代码中有上述函数，Server就是同步服务器
* 代码中没有上述函数就是异步服务器

### 异步非阻塞函数
* swoole_client异步模式
* mysql-async库
* redis-async库
* swoole_timer_tick/swoole_timer_after
* swoole_event系列函数
* swoole_table/swoole_atomic/swoole_buffer
* swoole_server->task/finish函数

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

use PhalconSwoole\MicroApp;
use PhalconSwoole\Di;

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
