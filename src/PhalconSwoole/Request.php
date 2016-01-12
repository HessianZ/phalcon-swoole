<?php
/**
 * Created by PhpStorm.
 * User: hessian
 * Date: 1/8/16
 * Time: 15:38
 */

namespace HessianZ\PhalconSwoole;

use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Http\RequestInterface;

class Request implements  RequestInterface, InjectionAwareInterface
{
    private $_dependencyInjector;

    /**
     * @var \swoole_http_request
     */
    private $request;

    public function setSwooleRequest(\swoole_http_request $request)
    {
        $this->request = $request;
    }

    /**
     * @return \swoole_http_request
     */
    public function getSwooleRequest()
    {
        return $this->request;
    }

    /**
     * Sets the dependency injector
     *
     * @param mixed $dependencyInjector
     */
    public function setDI(\Phalcon\DiInterface $dependencyInjector)
    {
        $this->_dependencyInjector = $dependencyInjector;
    }

    /**
     * Returns the internal dependency injector
     *
     * @return \Phalcon\DiInterface
     */
    public function getDI()
    {
        return $this->_dependencyInjector;
    }

    /**
     * Gets a variable from the $_REQUEST superglobal applying filters if needed
     *
     * @param string $name
     * @param string|array $filters
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get($name = null, $filters = null, $defaultValue = null)
    {
        return $this->request->get[$name];
    }

    /**
     * Gets a variable from the $_POST superglobal applying filters if needed
     *
     * @param string $name
     * @param string|array $filters
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getPost($name = null, $filters = null, $defaultValue = null)
    {
        return $this->request->post[$name];
    }

    /**
     * Gets variable from $_GET superglobal applying filters if needed
     *
     * @param string $name
     * @param string|array $filters
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getQuery($name = null, $filters = null, $defaultValue = null)
    {
        // TODO: Implement getQuery() method.
    }

    /**
     * Gets variable from $_SERVER superglobal
     *
     * @param string $name
     * @return mixed
     */
    public function getServer($name)
    {
        return $this->request->server[$name];
    }

    /**
     * Checks whether $_REQUEST superglobal has certain index
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->request->get[$name]);
    }

    /**
     * Checks whether $_POST superglobal has certain index
     *
     * @param string $name
     * @return bool
     */
    public function hasPost($name)
    {
        // TODO: Implement hasPost() method.
    }

    /**
     * Checks whether the PUT data has certain index
     *
     * @param string $name
     * @return bool
     */
    public function hasPut($name)
    {
        // TODO: Implement hasPut() method.
    }

    /**
     * Checks whether $_GET superglobal has certain index
     *
     * @param string $name
     * @return bool
     */
    public function hasQuery($name)
    {
        // TODO: Implement hasQuery() method.
    }

    /**
     * Checks whether $_SERVER superglobal has certain index
     *
     * @param string $name
     * @return bool
     */
    public function hasServer($name)
    {
        // TODO: Implement hasServer() method.
    }

    /**
     * Gets HTTP header from request data
     *
     * @param string $header
     * @return string
     */
    public function getHeader($header)
    {
        return $this->request->header($header);
    }

    /**
     * Gets HTTP schema (http/https)
     *
     * @return string
     */
    public function getScheme()
    {
        // TODO: Implement getScheme() method.
    }

    /**
     * Checks whether request has been made using ajax. Checks if $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'
     *
     * @return bool
     */
    public function isAjax()
    {
        // TODO: Implement isAjax() method.
    }

    /**
     * Checks whether request has been made using SOAP
     *
     * @return bool
     */
    public function isSoapRequested()
    {
        // TODO: Implement isSoapRequested() method.
    }

    /**
     * Checks whether request has been made using any secure layer
     *
     * @return bool
     */
    public function isSecureRequest()
    {
        // TODO: Implement isSecureRequest() method.
    }

    /**
     * Gets HTTP raws request body
     *
     * @return string
     */
    public function getRawBody()
    {
        // TODO: Implement getRawBody() method.
    }

    /**
     * Gets active server address IP
     *
     * @return string
     */
    public function getServerAddress()
    {
        // TODO: Implement getServerAddress() method.
    }

    /**
     * Gets active server name
     *
     * @return string
     */
    public function getServerName()
    {
        // TODO: Implement getServerName() method.
    }

    /**
     * Gets information about schema, host and port used by the request
     *
     * @return string
     */
    public function getHttpHost()
    {
        // TODO: Implement getHttpHost() method.
    }

    /**
     * Gets most possibly client IPv4 Address. This methods search in $_SERVER['REMOTE_ADDR'] and optionally in $_SERVER['HTTP_X_FORWARDED_FOR']
     *
     * @param bool $trustForwardedHeader
     * @return string
     */
    public function getClientAddress($trustForwardedHeader = false)
    {
        // TODO: Implement getClientAddress() method.
    }

    /**
     * Gets HTTP method which request has been made
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->request->server['request_method'];
    }

    /**
     * Gets HTTP user agent used to made the request
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->request->header['user-agent'];
    }

    /**
     * Check if HTTP method match any of the passed methods
     *
     * @param string|array $methods
     * @param bool $strict
     * @return boolean
     */
    public function isMethod($methods, $strict = false)
    {
        $http_method = $this->getMethod();

        if (!is_array($methods)) {
            $methods = [$methods];
        }

        foreach ($methods as $method) {
            if ($http_method == $method) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether HTTP method is POST. if $_SERVER['REQUEST_METHOD']=='POST'
     *
     * @return bool
     */
    public function isPost()
    {
        return $this->isMethod('POST');
    }

    /**
     * Checks whether HTTP method is GET. if $_SERVER['REQUEST_METHOD']=='GET'
     *
     * @return bool
     */
    public function isGet()
    {
        return $this->isMethod('GET');
    }

    /**
     * Checks whether HTTP method is PUT. if $_SERVER['REQUEST_METHOD']=='PUT'
     *
     * @return bool
     */
    public function isPut()
    {
        // TODO: Implement isPut() method.
    }

    /**
     * Checks whether HTTP method is HEAD. if $_SERVER['REQUEST_METHOD']=='HEAD'
     *
     * @return bool
     */
    public function isHead()
    {
        // TODO: Implement isHead() method.
    }

    /**
     * Checks whether HTTP method is DELETE. if $_SERVER['REQUEST_METHOD']=='DELETE'
     *
     * @return bool
     */
    public function isDelete()
    {
        // TODO: Implement isDelete() method.
    }

    /**
     * Checks whether HTTP method is OPTIONS. if $_SERVER['REQUEST_METHOD']=='OPTIONS'
     *
     * @return bool
     */
    public function isOptions()
    {
        // TODO: Implement isOptions() method.
    }

    /**
     * Checks whether request include attached files
     *
     * @param boolean $onlySuccessful
     * @return boolean
     */
    public function hasFiles($onlySuccessful = false)
    {
        // TODO: Implement hasFiles() method.
    }

    /**
     * Gets attached files as Phalcon\Http\Request\FileInterface compatible instances
     *
     * @param bool $onlySuccessful
     * @return \Phalcon\Http\Request\FileInterface
     */
    public function getUploadedFiles($onlySuccessful = false)
    {
        // TODO: Implement getUploadedFiles() method.
    }

    /**
     * Gets web page that refers active request. ie: http://www.google.com
     *
     * @return string
     */
    public function getHTTPReferer()
    {
        return $this->request->header['referer'];
    }

    /**
     * Gets array with mime/types and their quality accepted by the browser/client from $_SERVER['HTTP_ACCEPT']
     *
     * @return array
     */
    public function getAcceptableContent()
    {
        // TODO: Implement getAcceptableContent() method.
    }

    /**
     * Gets best mime/type accepted by the browser/client from $_SERVER['HTTP_ACCEPT']
     *
     * @return string
     */
    public function getBestAccept()
    {
        // TODO: Implement getBestAccept() method.
    }

    /**
     * Gets charsets array and their quality accepted by the browser/client from $_SERVER['HTTP_ACCEPT_CHARSET']
     *
     * @return array
     */
    public function getClientCharsets()
    {
        // TODO: Implement getClientCharsets() method.
    }

    /**
     * Gets best charset accepted by the browser/client from $_SERVER['HTTP_ACCEPT_CHARSET']
     *
     * @return string
     */
    public function getBestCharset()
    {
        // TODO: Implement getBestCharset() method.
    }

    /**
     * Gets languages array and their quality accepted by the browser/client from _SERVER['HTTP_ACCEPT_LANGUAGE']
     *
     * @return array
     */
    public function getLanguages()
    {
        // TODO: Implement getLanguages() method.
    }

    /**
     * Gets best language accepted by the browser/client from $_SERVER['HTTP_ACCEPT_LANGUAGE']
     *
     * @return string
     */
    public function getBestLanguage()
    {
        // TODO: Implement getBestLanguage() method.
    }

    /**
     * Gets auth info accepted by the browser/client from $_SERVER['PHP_AUTH_USER']
     *
     * @return array
     */
    public function getBasicAuth()
    {
        // TODO: Implement getBasicAuth() method.
    }

    /**
     * Gets auth info accepted by the browser/client from $_SERVER['PHP_AUTH_DIGEST']
     *
     * @return array
     */
    public function getDigestAuth()
    {
        // TODO: Implement getDigestAuth() method.
    }

    public function getURI()
    {
        return $this->request->server['request_uri'];
    }
}
