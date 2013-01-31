<?php

/**
 * string utilty functions
 *
 * @package    MyLib
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/06/24     Hulj
 */
class MyLib_String
{
    public static function truncate($string, $length)
    {
        $len = strlen($string);
        if ($len <= $length) {
            return $string;
        }
        
        $ret = '';
        $j = 0;
        $z = 0;
        $t = 0;
        for($i = 0; $t < $length; $i++) {
            if (ord($string[$i]) > 128) {
                $ret .= $string[$i] . $string[++$i] . $string[++$i];
                $j++;
            }
            else {
                $ret .= $string[$i];
                $z++;
            }

            $t = ceil($z/2) + $j;
        }
        
        if ($z%2 == 1 && $i< $len && ord($string[$i]) < 128) {
            $ret .= $string[$i];
        }

        return $ret;
    }
    
    public static function fromCharCode($codes)
    {
        if (is_scalar($codes)) { 
            $codes = func_get_args();
        }
        
        $str = '';
        foreach ($codes as $code) {
            $str .= chr($code);
        }
        
        return $str;
    }
    
    public static function unescapeEntity($matches)
    {
        return self::fromCharCode($matches[1]);
    }

    public static function unescapeString($string)
    {
        return preg_replace_callback('/&#([0-9]+);/', array('self', 'unescapeEntity'), $string);
    }
    
    public static function escapeString($string)
    {
        $escapeCodePoints = array(
            0 => false, 10 => true, 13 => true, 34 => true, 39 => true,
            60 => true, 62 => true, 92 => true, 8232 => true, 8233 => true
        );
        
        $str = '';
        for($i = 0, $len = strlen($string); $i < $len; $i++) {
            $ch = ord($string[$i]);
            $shouldEscape = $escapeCodePoints[$ch];
            if ($shouldEscape === true) {
                $str .= '&#' . $ch . ';';
            } else if ($shouldEscape !== false) {
                $str .= $string[$i];
            }
        }
        
        return $str;
    }
}