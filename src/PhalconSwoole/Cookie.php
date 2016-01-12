<?php
/**
 * Created by PhpStorm.
 * User: hessian
 * Date: 1/12/16
 * Time: 13:11
 */

namespace HessianZ\PhalconSwoole;

class Cookie extends \Phalcon\Http\Cookie
{
    public function send()
    {
        return $this;
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