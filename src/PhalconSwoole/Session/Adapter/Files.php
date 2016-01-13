<?php
/**
 * Created by PhpStorm.
 * User: hessian
 * Date: 1/12/16
 * Time: 15:08
 */



namespace HessianZ\PhalconSwoole\Session\Adapter;

use HessianZ\PhalconSwoole\Session\Adapter;

class Files extends Adapter {

    protected $_filenamePrefix = 'psess_';
    protected $_data = [];

    function __destruct()
    {
        if ($this->_started) {
            $this->sync();
        }

        unset($this->_data);
    }

    /**
     * @param string $index
     * @param string $defaultValue
     * @return string
     */
    public function get($index, $defaultValue = null)
    {
        return empty($this->_data[$index]) ? $defaultValue : $this->_data[$index];
    }

    /**
     * Sets a session variable in an application context
     *
     * @param string $index
     * @param mixed $value
     */
    public function set($index, $value)
    {
        $this->_data[$index] = $value;
        $this->sync();
    }

    /**
     * Check whether a session variable is set in an application context
     *
     * @param string $index
     * @return bool
     */
    public function has($index)
    {
        return isset($this->_data[$index]);
    }

    /**
     * Removes a session variable from an application context
     *
     * @param string $index
     */
    public function remove($index)
    {
        unset($this->_data[$index]);
        $this->sync();
    }

    /**
     * Destroys the active session
     *
     * @param bool $removeData
     * @return bool
     */
    public function destroy($removeData = false)
    {
        $this->_data = [];

        if ($removeData) {
            $filename = $this->getFilename();

            unlink($filename);
        }

        return true;
    }

    public function start()
    {
        if (!parent::start()) {
            return false;
        }

        // gc
        $gcProbability = $this->getOption('gc_probability', 1);
        if ($gcProbability) {
            $gcDivisor = $this->getOption('gc_divisor', 100);
            $rand = mt_rand(1, $gcDivisor);

            if ($rand <= $gcProbability) {
                $maxLifetime = $this->getOption('gc_maxlifetime', 1440);
                $this->gc($maxLifetime);
            }
        }

        // load
        $filename = $this->getFilename();
        if (file_exists($filename)) {
            $serilizedData = file_get_contents($filename);

            $this->_data = unserialize($serilizedData);
        }

        return true;
    }

    protected function getFilename()
    {
        $savePath = $this->getOption('save_path');
        return $savePath . DIRECTORY_SEPARATOR . $this->_filenamePrefix . $this->_id;
    }

    /**
     * Sync to disk
     */
    protected function sync()
    {
        $filename = $this->getFilename();
        $serializedData = serialize($this->_data);
        file_put_contents($filename, $serializedData);
    }

    protected function gc($maxlifetime)
    {
        $savePath = $this->getOption('save_path');
        $pattern = $savePath . DIRECTORY_SEPARATOR . $this->_filenamePrefix . '*';
        foreach (glob($pattern) as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }
    }

}