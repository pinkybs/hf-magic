<?php

class Hapyfish2_Validate_UserCertify
{

    public static function generateKey($uid, $puid, $session_key, $t, $rnd, $secret)
    {
        $sig = md5($uid . $puid . $session_key . $t . $rnd . $secret);
        $aryElement = array($uid, $puid, base64_encode($session_key), $t, $rnd, $sig);
        $skey = implode('.', $aryElement);
        return $skey;
    }

	public static function checkKey($skey, $secret)
	{
		if (!$skey) {
    		return false;
    	}

    	$tmp = explode('.', $skey);
    	if (empty($tmp)) {
    		return false;
    	}
    	$count = count($tmp);
    	if ($count != 5 && $count != 6) {
    		return false;
    	}

        $uid = $tmp[0];
        $puid = $tmp[1];
        $session_key = base64_decode($tmp[2]);
        $t = $tmp[3];

        $rnd = -1;
        if ($count == 5) {
        	$sig = $tmp[4];
	        $vsig = md5($uid . $puid . $session_key . $t . $secret);
	        if ($sig != $vsig) {
	        	return false;
	        }
        } else if ($count == 6) {
        	$rnd = $tmp[4];
        	$sig = $tmp[5];
        	$vsig = md5($uid . $puid . $session_key . $t . $rnd . $secret);
        	if ($sig != $vsig) {
	        	return false;
	        }
        }

        //max long time one day
        if (time() > $t + 86400) {
        	return false;
        }

        return array('uid' => $uid, 'puid' => $puid, 'session_key' => $session_key,  't' => $t, 'rnd' => $rnd);
	}



}