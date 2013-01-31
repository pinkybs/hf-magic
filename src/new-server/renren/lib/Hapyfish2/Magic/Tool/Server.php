<?php

class Hapyfish2_Magic_Tool_Server
{
	public static function getWebList()
	{
		$list = array();
	    if (SERVER_ID == 9) {
            $list['9'] = array('id'=>'9', 'name'=>'dev', 'pub_ip'=>'', 'local_ip'=>'192.168.1.249', 'status'=>'1');
		}
		else if (SERVER_ID == 99) {
            $list['99'] = array('id'=>'99', 'name'=>'test', 'pub_ip'=>'', 'local_ip'=>'192.168.0.89', 'status'=>'1');
		}
		else {
    		$list['1001'] = array('id'=>'1001', 'name'=>'web01', 'pub_ip'=>'', 'local_ip'=>'192.168.0.84', 'status'=>'1');
    		$list['1002'] = array('id'=>'1002', 'name'=>'web02', 'pub_ip'=>'', 'local_ip'=>'192.168.0.86', 'status'=>'1');
    		$list['1003'] = array('id'=>'1003', 'name'=>'web03', 'pub_ip'=>'', 'local_ip'=>'192.168.0.40', 'status'=>'1');
    		$list['1004'] = array('id'=>'1004', 'name'=>'web04', 'pub_ip'=>'', 'local_ip'=>'192.168.0.41', 'status'=>'1');
		}
		return $list;
	}

	public static function requestWeb($host, $url)
	{
        $ch = curl_init();
        $header = array();
        $header[] = 'Host: ' . $host;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //max curl execute time
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $cURLVersion = curl_version();
        $ua = 'PHP-cURL/' . $cURLVersion['version'] . ' HapyFish-TOPRest/1.0';
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$result = @curl_exec($ch);

        $errno = @curl_errno($ch);
        $error = @curl_error($ch);
        curl_close($ch);

        return $result;
	}
}