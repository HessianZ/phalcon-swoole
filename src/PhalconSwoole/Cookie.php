<?php
/**
 * Created by PhpStorm.
 * User: hessian
 * Date: 1/12/16
 * Time: 13:11
 */

namespace PhalconSwoole;

use Phalcon\Http\Response\Exception;

class Cookie extends \Phalcon\Http\Cookie
{
    public function send()
    {
        return $this;
    }

    public function getValue($filters = null, $defaultValue = null)
    {
        $dependencyInjector = $this->_dependencyInjector;

        if (empty($dependencyInjector)) {
            throw new Exception("A dependency injection object is required to access the 'request,filter,crypt' service");
        }

        if (!$this->_restored) {
//            $this->restore();
        }

        if (!$this->_readed) {
            $request = $dependencyInjector->get('request');

            if ($request->getCookie($this->_name)) {
                $value = $request->getCookie($this->_name);

                if ($this->_useEncryption) {
                    $crypt = $dependencyInjector->get('crypt');
                    $decryptedValue = $crypt->decryptBase64($value);
                } else {
                    $decryptedValue = $value;
                }

                $this->_value = $decryptedValue;

                if ($filters) {
                    $filter = $this->_filter;
                    if (empty($filter)) {
                        $filter = $dependencyInjector->get('filter');
                        $this->_filter = $filter;
                    }

                    return $filter->sanitize($decryptedValue, $filters);
                }

                return $decryptedValue;
            }

            return $defaultValue;
        }

        return $this->_value;
    }


    public function getEncryptValue()
    {
        $value = $this->_value;

        if ($this->_useEncryption) {

            if (!empty($value)) {
                $crypt = $this->_dependencyInjector->getShared("crypt");

                /**
                 * Encrypt the value also coding it with base64
                 */
                $encryptValue = $crypt->encryptBase64((string)$value);

            } else {
                $encryptValue = $value;
            }

        } else {
            $encryptValue = $value;
        }

        return $encryptValue;
    }

}