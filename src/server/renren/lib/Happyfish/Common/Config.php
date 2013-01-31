<?php

/**
 * config logic's Operation
 * get config file
 * 
 * @package    Hapyyfish/Common
 * @copyright  Copyright (c) 
 * @create     2010/08/10    zhangxin
 */
class Happyfish_Common_Config
{

    /**
     * get college config xml
     *
     * @param string $xml
     * @param string $prefix
     *  college hostname
     * @return xml
     */
    public static function get($xml, $prefix = null)
    {        
        $config = new Zend_Config_Xml($xml, null);
        
        return $config;
    }
}