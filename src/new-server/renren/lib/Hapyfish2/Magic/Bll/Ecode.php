<?php

class Hapyfish2_Magic_Bll_Ecode
{
	public static $CODE_LIST = array(
		'1' => 'u8Abajk#Ne*u75$2',
		'2' => '0b3WZIfAcShBh>Va',
		'3' => 'ltLc5&vcwvGxD~&Z',
		'4' => '@Tyda>$q4cB*KaJq'
	);

	public static function check($rnd, $uid, $ts, $authid, $params = array())
	{
		if(!isset(self::$CODE_LIST[$rnd])) {
			return true;
		}

		$code = self::$CODE_LIST[$rnd];

		ksort($params);
		$p = '';
		foreach ($params as $v) {
			$p .= $v;
		}

		$valid = md5($code . $uid . $ts . $p);

		return $valid == $authid;
	}

}