<?php

/**
 * Created by PhpStorm.
 * User: hessian
 * Date: 1/12/16
 * Time: 14:19
 */


namespace PhalconSwoole\Session;


use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Session\AdapterInterface;
use Phalcon\Session\Exception;

abstract class Adapter implements AdapterInterface, InjectionAwareInterface
{
    const SESSION_ACTIVE = 2;
    const SESSION_NONE = 1;
    const SESSION_DISABLED = 0;


    protected $_id;
    protected $_uniqueId;
    protected $_options;
    protected $_name;
    protected $_started = false;
    protected $_status = self::SESSION_NONE;
    protected $_dependencyInjector;

    protected static $_defaults;

    /**
     * Adapter constructor.
     * @param array $options
     */
    public function __construct($options)
    {
        if (static::$_defaults === null) {
            static::readDefaults();
        }

        $this->_options = $options;

        $this->_name = $this->getOption('name');
    }

    public static function readDefaults()
    {
        $iniSettings = ini_get_all('session', false);

        $settings = [];
        foreach ($iniSettings as $key => $value) {
            $shortKey = str_replace('session.', '', $key);
            $settings[$shortKey] = $value;
        }
        unset($iniSettings);

        if (empty($settings['save_path'])) {
            $settings['save_path'] = sys_get_temp_dir();
        }

        self::$_defaults = $settings;
    }

    /**
     * @param \Phalcon\DiInterface $dependencyInjector
     */
    public function setDI(\Phalcon\DiInterface $dependencyInjector)
    {
        $this->_dependencyInjector = $dependencyInjector;
    }

    /**
     * @return \Phalcon\DiInterface $dependencyInjector|null
     */
    public function getDI()
    {
        return $this->_dependencyInjector;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function start()
    {
        if ($this->_started) {
            return true;
        }

        if (empty($this->_dependencyInjector)) {
            throw new Exception("A dependency injection object is required to access the 'cookies' service");
        }

        // Todo: session.use_trans_id

        $cookies = $this->_dependencyInjector->getShared('cookies');

        if (empty($this->_id)) {
            if ($cookies->has($this->_name)) {
                $cookie = $cookies->get($this->_name);
                $this->_id = $cookie->getValue();
            } else {
                $this->regenerateId();
            }
        }

        $this->_started = true;

        return true;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param bool|true $deleteOldSession
     * @return $this
     * @throws Exception
     */
    public function regenerateId($deleteOldSession = true)
    {
        if (empty($this->_dependencyInjector)) {
            throw new Exception("A dependency injection object is required to access the 'request,cookies' service");
        }

        if ($deleteOldSession) {
            // TODO: delete old session
        }

        $request = $this->_dependencyInjector->getShared('request');
        $cookies = $this->_dependencyInjector->getShared('cookies');

        $ip = $request->getClientAddress(true);
        $ua = $request->getUserAgent();
        $time = microtime(true);

        $prefix = $ip . $ua . $time;

        $id = md5(uniqid($prefix, true));
        $this->setId($id);

        $cookieLifetime = $this->getOption('cookie_lifetime');
        $cookiePath = $this->getOption('cookie_path');
        $cookieDomain = $this->getOption('cookie_domain');
        $cookieSecure = !!$this->getOption('cookie_secure');
        $cookieHttponly = !!$this->getOption('cookie_httponly');

        $cookies->set($this->_name, $id, $cookieLifetime, $cookiePath, $cookieSecure, $cookieDomain, $cookieHttponly);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * @return int
     */
    public function status()
    {
        // TODO: Check session status
        return $this->_status;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->_options = $options;

        if (isset($options['uniqueId'])) {
            $this->_uniqueId = $options['uniqueId'];
        }
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->_options;
    }

    public function getOption($name, $default = null)
    {
        if (isset($this->_options[$name])) {
            return $this->_options[$name];
        }

        if (isset(static::$_defaults[$name])) {
            return static::$_defaults[$name];
        }

        return $default;
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        return $this->_started;
    }

    /**
     * @param $index
     * @return mixed
     */
    public function __get($index)
    {
        return $this->get($index);
    }

    /**
     * @param $index
     * @param $value
     * @return mixed
     */
    public function __set($index, $value)
    {
        return $this->set($index, $value);
    }

    /**
     * @param $index
     * @return bool
     */
    public function __isset($index)
    {
        return $this->has($index);
    }

    /**
     * @param $index
     * @return mixed
     */
    public function __unset($index)
    {
        return $this->remove($index);
    }
}