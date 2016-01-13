<?php
/**
 * Created by PhpStorm.
 * User: hessian
 * Date: 1/12/16
 * Time: 15:08
 */



namespace HessianZ\PhalconSwoole\Session\Adapter;

use HessianZ\PhalconSwoole\Session\Adapter;

class None extends Adapter {

    public function start()
    {
        return false;
    }

    public function get($index, $defaultValue = null, $remove = false)
    {
        return false;
    }

    public function set($index, $value)
    {
    }

    public function has($index)
    {
        return false;
    }

    public function isStarted()
    {
        return false;
    }

    /**
     * Removes a session variable from an application context
     *
     * @param string $index
     */
    public function remove($index)
    {
    }

    /**
     * Destroys the active session
     *
     * @param bool $removeData
     * @return bool
     */
    public function destroy($removeData = false)
    {
    }
}