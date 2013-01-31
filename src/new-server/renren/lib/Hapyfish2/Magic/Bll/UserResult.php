<?php

class Hapyfish2_Magic_Bll_UserResult
{
	private static $result = array(
		'status' => 1,
		'content' => '',
		'levelUP' => false,
		'coin' => 0,
		'gem' => 0,
		'exp' => 0,
		'mp' => 0,
		'roomLevelUp' => 0
	);

	private static $uid = 0;

	private static $field = array();

	public static function addField($uid, $name, $value)
	{
		if (self::$uid == $uid) {
			if (!isset(self::$field[$name])) {
				self::$field[$name] = $value;
			} else {
				self::$field[$name] = array_merge(self::$field[$name], $value);
			}
		}
	}

	public static function setUser($uid)
	{
		self::$uid = $uid;
	}

	public static function mergeCoin($uid, $coin)
	{
		if (self::$uid == $uid) {
			self::$result['coin'] += $coin;
		}
	}

	public static function mergeGold($uid, $gold)
	{
		if (self::$uid == $uid) {
			self::$result['gem'] += $gold;
		}
	}

	public static function mergeExp($uid, $exp)
	{
		if (self::$uid == $uid) {
			self::$result['exp'] += $exp;
		}
	}

	public static function mergeMp($uid, $mp)
	{
		if (self::$uid == $uid) {
			self::$result['mp'] += $mp;
		}
	}

	public static function setLevelUp($uid, $levelUp)
	{
		if (self::$uid == $uid) {
			self::$result['levelUP'] = $levelUp;
		}
	}

	public static function setRoomLevelUp($uid, $roomLevelUp)
	{
		if (self::$uid == $uid) {
			self::$result['roomLevelUp'] = $roomLevelUp;
		}
	}

	public static function result($data = false)
	{
		if ($data) {
			return self::$result;
		} else {
			return array('result' => self::$result);
		}
	}

	public static function flush()
	{
		$ret = self::all();

		self::$result = array(
			'status' => 1,
			'content' => '',
			'levelUP' => false,
			'coin' => 0,
			'gem' => 0,
			'exp' => 0,
			'mp' => 0,
			'roomLevelUp' => 0
		);
		self::$field = array();

		return $ret;
	}

	public static function Error($content = '', $status = -1)
	{
	    include CONFIG_DIR . '/language.php';
	    $message = isset($LANGUAGE_MESSAGE[$content]) ? $LANGUAGE_MESSAGE[$content] : 'sys err';

		$result = array('status' => $status);
		//$result['content'] = $message; //."\n". $content;
		$result['content'] = $content; //."\n". $content;
		return array('result' => $result);
	}

	public static function field(&$ret)
	{
		if(!empty(self::$field)) {
			foreach (self::$field as $k => $v) {
				$ret[$k] = $v;
			}
		}
	}

	public static function all()
	{
		$ret = array('result' => self::$result);

		if(!empty(self::$field)) {
			foreach (self::$field as $k => $v) {
				$ret[$k] = $v;
			}
		}

		return $ret;
	}
}