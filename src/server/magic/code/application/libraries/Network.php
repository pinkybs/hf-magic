<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * 通讯类
 * 所有的输出都要经过此类处理
 */
class Network {
	private static $buf = array();
	//1:需要翻译 0:无需翻译
	private static $lang = 1;
	
	/**
	 * 缓冲数据
	 * @param $key
	 * @param $vl
	 */
	public static function buffer($key, $vl)
	{
		self::$buf[$key] = $vl;
	}
	
	public static function get($key)
	{
		if (isset(self::$buf[$key])) {
			return self::$buf[$key];
		} else {
			return array();
		}
	}
	
	/**
	 * 已经带着key的数组
	 * @param Array $array
	 */
	public static function bufferArray($array)
	{
		foreach ($array as $key => $vl) {
			self::$buf[$key] = $vl;
			break;
		}
	}
	
	public static function bufferAdd($key, $vl)
	{
		if (isset(self::$buf[$key])) {
			//合并处理
			array_push(self::$buf[$key], $vl);
		} else {
			self::$buf[$key]  = array($vl);
		}
	}
	
	/**
	 * 缓冲错误数据
	 * @param unknown_type $msg
	 * @param unknown_type $params
	 */
	public static function buffer_error($msg, $params = array())
	{
		//self::$buf = array('msg' => $msg, 'error' => 1) + $params;
		if (self::$lang == 1) {
			$msg = Kohana::lang('error.'.$msg);
		}
		
		self::$buf = array('result' => common::result_error($msg)) + $params;

		self::send();
		die();
	}
	
	/**
	 * 发送数据
	 */
	public static function send()
	{
		echo json_encode(self::$buf);
	}
}
?>