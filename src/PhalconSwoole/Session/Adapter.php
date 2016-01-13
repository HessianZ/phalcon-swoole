<?php

/**
 * Created by PhpStorm.
 * User: hessian
 * Date: 1/12/16
 * Time: 14:19
 */


namespace HessianZ\PhalconSwoole\Session;


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

    /**
     * Adapter constructor.
     * @param array $options
     */
    public function __construct($options)
    {
        if (isset($options['name'])) {
            $this->_name = $options['name'];
        } else {
            // Default session name
            $this->_name = ini_get('session.name');
        }

        $this->_options = $options;
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
            throw new Exception("A dependency injection object is required to access the 'request' service");
        }

        if ($deleteOldSession) {
            // TODO: delete old session
        }

        $request = $this->_dependencyInjector->getShared('request');

        $ip = $request->getClientAddress(true);
        $ua = $request->getUserAgent();
        $time = microtime(true);

        $prefix = $ip . $ua . $time;

        $id = md5(uniqid($prefix, true));
        $this->setId($id);

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