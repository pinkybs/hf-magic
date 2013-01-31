<?php

/**
 * language pack instance
 *
 * @package    Happyfish/Magic/Bll/Language
 * @copyright  Copyright (c) 
 * @create     2010/11/02    zhangxin
 */
class Happyfish_Magic_Bll_Language_Local
{

    /**
     * get language context info
     *
     * @param integer $id
     * @return string
     */
    public static function getText($id)
    {
    	$strRtn = '';
    	$refObj = 'Happyfish_Magic_Bll_Language_' . LANGUAGE;
        if (is_callable(array($refObj, 'getContext'))) {
        	$strRtn = call_user_func(array($refObj, 'getContext'), $id);
        }
    	return $strRtn;
    }
}