<?php

class Hapyfish2_Util_Arr
{
	public static function decode($delimiter, $str)
	{
		if (empty($str)) {
			return array();
		}
		
		return explode($delimiter, $str);
	}
	
	

}