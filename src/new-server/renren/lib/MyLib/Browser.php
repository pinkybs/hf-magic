<?php

/**
 * get client browser info
 *
 * @package    MyLib
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2008/07/18     Hulj
 */
class MyLib_Browser
{
    /**
     * get browser name
     *
     * @return string
     */
    public static function getBrowser()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $browser = '';
        $browserver = '';
        $browserArray = array('Lynx', 'MOSAIC', 'AOL', 'Opera', 'JAVA', 'MacWeb', 'WebExplorer', 'OmniWeb');
        
        for ($i = 0; $i <= 7; $i++) {
            if (strpos($agent, $browserArray[$i])) {
                $browser = $browserArray[$i];
                $browserver = '';
                break;
            }
        }
        
        if (ereg('Mozilla', $agent) && ereg('MSIE', $agent)) {
            $temp = explode('(', $agent);
            $part = $temp[1];
            $temp = explode(';', $part);
            $part = $temp[1];
            $temp = explode(' ', $part);
            $browserver = $temp[2];
            $browserver = preg_replace('/([d.] )/', '', $browserver);
            $browserver = $browserver;
            $browser = 'Internet Explorer';
        }
        else if (ereg('Mozilla', $agent) && ereg('Firefox', $agent)) {
            $temp = explode('/', $agent);
            $browserver = $temp[count($temp) - 1];
            $browser = 'Firefox';
        }
        else if (ereg('Mozilla', $agent) && ereg('Netscape', $agent)) {
            $temp = explode('/', $agent);
            $browserver = $temp[count($temp) - 1];
            $browser = 'Netscape';
        }
        else if (ereg('Mozilla', $agent) && ereg('Safari', $agent)) {
            $temp = explode('/', $agent);
            $part = $temp[count($temp) - 2];
            $temp = explode(' ', $part);
            $browserver = $temp[0];
            $browser = 'Safari';
        }
        else if (ereg('Opera', $agent)) {
            $temp = explode('(', $agent);
            $part = $temp[0];
            $temp = explode('/', $part);
            $browserver = $temp[1];
            $browser = 'Opera';
        }
        else if (ereg('NetCaptor', $agent)) {
            $browserver = '';
            $browser = 'NetCaptor';
        }
        else if (ereg('Konqueror', $agent)) {
            $browserver = '';
            $browser = 'Konqueror';
        }
        
        if ($browser != '') {
            $browseinfo = $browser . ' ' . $browserver;
        } else {
            $browseinfo = '';
        }
        
        return $browseinfo;
    }

    /**
     * get client ip address
     *
     * @return string
     */
    public static function getIP()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        }
        else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        }
        else if (getenv('REMOTE_ADDR')) {
            $ip = getenv('REMOTE_ADDR');
        }
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return $ip;
    }

    /**
     * get client os name
     *
     * @return string
     */
    public static function getOS()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $os = false;
        if (eregi('win', $agent) && strpos($agent, '95')) {
            $os = 'Windows 95';
        }
        else if (eregi('win 9x', $agent) && strpos($agent, '4.90')) {
            $os = 'Windows ME';
        }
        else if (eregi('win', $agent) && ereg('98', $agent)) {
            $os = 'Windows 98';
        }
        else if (eregi('win', $agent) && eregi('nt 6.0', $agent)) {
            $os = 'Windows Vista';
        }
        else if (eregi('win', $agent) && eregi('nt 5.2', $agent)) {
            $os = 'Windows 2003';
        }
        else if (eregi('win', $agent) && eregi('nt 5.1', $agent)) {
            $os = 'Windows XP';
        }
        else if (eregi('win', $agent) && eregi('nt 5', $agent)) {
            $os = 'Windows 2000';
        }
        else if (eregi('win', $agent) && eregi('nt', $agent)) {
            $os = 'Windows NT';
        }
        else if (eregi('win', $agent) && ereg('32', $agent)) {
            $os = 'Windows 32';
        }
        else if (eregi('linux', $agent)) {
            $os = 'Linux';
        }
        else if (eregi('unix', $agent)) {
            $os = 'Unix';
        }
        else if (eregi('Macintosh', $agent)) {
            $os = 'Macintosh';
        }
        else if (eregi('sun', $agent) && eregi('os', $agent)) {
            $os = 'SunOS';
        }
        else if (eregi('ibm', $agent) && eregi('os', $agent)) {
            $os = 'IBM OS/2';
        }
        else if (eregi('Mac', $agent) && eregi('PC', $agent)) {
            $os = 'Macintosh';
        }
        else if (eregi('PowerPC', $agent)) {
            $os = 'PowerPC';
        }
        else if (eregi('AIX', $agent)) {
            $os = 'AIX';
        }
        else if (eregi('HPUX', $agent)) {
            $os = 'HPUX';
        }
        else if (eregi('NetBSD', $agent)) {
            $os = 'NetBSD';
        }
        else if (eregi('OpenBSD', $agent)) {
            $os = 'OpenBSD';
        }
        else if (eregi('BSD', $agent)) {
            $os = 'BSD';
        }
        else if (ereg('OSF1', $agent)) {
            $os = 'OSF1';
        }
        else if (ereg('IRIX', $agent)) {
            $os = 'IRIX';
        }
        else if (eregi('FreeBSD', $agent)) {
            $os = 'FreeBSD';
        }
        else if (eregi('teleport', $agent)) {
            $os = 'teleport';
        }
        else if (eregi('flashget', $agent)) {
            $os = 'flashget';
        }
        else if (eregi('webzip', $agent)) {
            $os = 'webzip';
        }
        else if (eregi('offline', $agent)) {
            $os = 'offline';
        }
        else {
            $os = 'Unknown';
        }
        
        return $os;
    }

}