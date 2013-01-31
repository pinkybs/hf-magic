<?php

/**
 * Secret logic's Operation
 * 
 * @package    Hapyyfish/Common
 * @copyright  Copyright (c) 
 * @create     2010/08/10    zhangxin
 */
final class Happyfish_Common_Secret
{
     /**
      * get secret result
      *
      * @param string $source
      * @param string $salt
      * @return boolean
      */
    public static function getSecretResult($source, $salt = '')
    {
        $secret = Zend_Registry::get('secret');
        return hash($secret['validation'], $source . $secret['validationKey'] . $salt);
    }

    /**
     * check the secret string is true or not
     *
     * @param string $source
     * @param string $secretResult
     * @param string $salt
     * @return boolean
     */
    public static function isTrueSecret($source, $secretResult, $salt = '')
    {
        return self::getSecretResult($source, $salt) === $secretResult;
    }

    /**
     * get uuid
     * create a rand md5 string
     *
     * @return string
     */
    public static function getUUID()
    {
        return md5(getmypid() . uniqid(rand(), true) . $_SERVER['SERVER_NAME']);
    }

}