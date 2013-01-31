<?php

class Happyfish_Magic_Bll_Application_Status
{
    const NORMAL = 1;
    const JSON = 2;

    public static function check($uid = 0)
    {
        $stop = (defined('APP_STATUS') && APP_STATUS == 0);

        if ($stop && defined('APP_STATUS_DEV') && APP_STATUS_DEV == 1) {
        	$dev = isset($_POST['hf_dev']) ? $_POST['hf_dev'] : '0';
        	if ($dev == '1') {
                return;
        	}

            $ipList = array('220.248.92.126', '114.91.68.230');
            $ip = false;
        	try {
	            if(!empty($_SERVER["HTTP_CLIENT_IP"])){
	                $ip = $_SERVER["HTTP_CLIENT_IP"];
	            }
	            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	                $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
	                if ($ip) {
	                    array_unshift($ips, $ip);
	                    $ip = false;
	                }
	                for ($i = 0, $n = count($ips); $i < $n; $i++) {
	                    if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
	                        $ip = $ips[$i];
	                        break;
	                    }
	                }
	            }
	            if (!$ip) {
	                $ip = $_SERVER['REMOTE_ADDR'];
	            }

	            if ($ip) {
	                if (in_array($ip, $ipList)) {
	                    $stop = false;
	                }
	            }
	        }catch (Exception $e) {

	        }
        }

        if ($stop) {
            $type = self::getType();
            if ($type == self::JSON) {
                echo self::getJsonMsg();
            } else {
                echo self::getNoramlMsg();
            }
            exit;
        }
    }

    public static function getNoramlMsg()
    {
        $file = CONFIG_DIR . '/status/normal.msg';
        return @file_get_contents($file);
    }

    public static function getJsonMsg()
    {
        $file = CONFIG_DIR . '/status/json.msg';
        return @file_get_contents($file);
    }

    public static function getType()
    {
        $type = self::NORMAL;
        $uri = $_SERVER['REQUEST_URI'];
        if ($uri) {
            if (preg_match('/^\/api\/(.*)+$/i', $uri)) {
                $type = self::JSON;
            }
        }

        return $type;
    }
}
