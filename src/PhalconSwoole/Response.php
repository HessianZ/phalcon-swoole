<?php
/**
 * Created by PhpStorm.
 * User: hessian
 * Date: 1/8/16
 * Time: 15:38
 */

namespace PhalconSwoole;

class Response extends \Phalcon\Http\Response
{
    /**
     * @var \swoole_http_response
     */
    private $response;

    /**
     * @param \swoole_http_response $response
     */
    public function setSwooleResponse(\swoole_http_response $response)
    {
        $this->response = $response;
    }

    /**
     * @return \swoole_http_response
     */
    public function getSwooleResponse()
    {
        return $this->response;
    }

    public function sendCookies()
    {
        if ($this->_cookies) {
            $this->_cookies->send();
        }
    }

    public function send()
    {

        if ($this->_sent) {
            throw new Exception("Response was already sent");
        }

        if ($this->_headers) {
            $headers = $this->_headers->toArray();

            foreach ($headers as $k => $v) {
                $this->response->header($k, $v);
            }
        }

        if ($this->_cookies) {
            $this->_cookies->send();
        }

        return $this;
    }

    public function redirect($location = null, $externalRedirect = false, $statusCode = 302)
    {
        $this->response->status($statusCode);
        $this->response->header('Location: ' . $location);
        $this->response->end();
        return $this;
    }


}
