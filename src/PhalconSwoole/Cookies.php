<?php
/**
 * Created by PhpStorm.
 * User: hessian
 * Date: 1/12/16
 * Time: 13:09
 */

namespace PhalconSwoole;

class Cookies extends \Phalcon\Http\Response\Cookies
{
    protected $cookieClass = Cookie::class;

    protected $_useEncryption = false;

    /**
     * @return mixed
     */
    public function getCookieClass()
    {
        return $this->cookieClass;
    }

    /**
     * @param mixed $cookieClass
     */
    public function setCookieClass($cookieClass)
    {
        $this->cookieClass = $cookieClass;
    }

    public function set($name, $value = null, $expire = 0, $path = "/", $secure = null, $domain = null, $httpOnly = null)
    {
		$encryption = $this->_useEncryption;

		/**
         * Check if the cookie needs to be updated or
         */
        $cookie = $this->_cookies[$name];
		if (!$cookie) {
            /**
             * 这里用一个自定义的类替换掉phalcon自己的cookie类.
             */
            $params = [$name, $value, $expire, $path, $secure, $domain, $httpOnly];
            $cookie = $this->_dependencyInjector->get($this->cookieClass, $params);

			/**
             * Pass the DI to created cookies
             */
			$cookie->setDi($this->_dependencyInjector);

			/**
             * Enable encryption in the cookie
             */
			if ($encryption) {
                $cookie->useEncryption($encryption);
			}

			$this->_cookies[$name] = $cookie;

		} else {
            /**
             * Override any settings in the cookie
             */
            $cookie->setValue($value);
			$cookie->setExpiration($expire);
			$cookie->setPath($path);
			$cookie->setSecure($secure);
			$cookie->setDomain($domain);
			$cookie->setHttpOnly($httpOnly);
		}

		/**
         * Register the cookies bag in the response
         */
		if ($this->_registered === false) {

            $dependencyInjector = $this->_dependencyInjector;
			if (!is_object($dependencyInjector)) {
                throw new Exception("A dependency injection object is required to access the 'response' service");
            }

			$response = $dependencyInjector->getShared("response");

			/**
             * Pass the cookies bag to the response so it can send the headers at the of the request
             */
			$response->setCookies($this);
		}

		return $this;
    }

    public function get($name)
    {
        if (isset($this->_cookies[$name])) {
            return $this->_cookies[$name];
        }

        /**
         * Create the cookie if the it does not exist
         */
        $dependencyInjector = $this->_dependencyInjector;

        $cookie = $dependencyInjector->get($this->cookieClass, [$name]);

        /**
         * Pass the DI to created cookies
         */
        $cookie->setDi($dependencyInjector);

        $encryption = $this->_useEncryption;

        /**
         * Enable encryption in the cookie
         */
        if ($encryption) {
            $cookie->useEncryption($encryption);
        }

        $this->_cookies[$name] = $cookie;

        return $cookie;
    }


    /**
     * Check if a cookie is defined in the bag or exists in the _COOKIE superglobal
     */
    public function has($name)
	{
        /**
         * Check the internal bag
         */
        if (isset($this->_cookies[$name])) {
            return true;
        }

        /*
         * Check the swoole
         */
        $request = $this->_dependencyInjector->get("request");
        if ($request->hasCookie($name)) {
            return true;
        }

		return false;
	}

    public function send()
    {
        if (empty($this->_cookies)) {
            return;
        }

        $response = $this->_dependencyInjector->get("response");

        $swooleRepsnose = $response->getSwooleResponse();

        /**
         * @var $cookie Cookie;
         */
        foreach ($this->_cookies as $cookie) {
            $name   = $cookie->getName();
            $value  = $cookie->getEncryptValue();
            $expire = $cookie->getExpiration();
            $path   = $cookie->getPath();
            $domain = $cookie->getDomain();
            $secure = $cookie->getSecure();
            $httpOnly = $cookie->getHttpOnly();

            $swooleRepsnose->cookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
        }
    }


}