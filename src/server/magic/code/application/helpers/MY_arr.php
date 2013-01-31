<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class arr extends arr_Core
{
	// 序列转换数组
	public static function _serial_to_array ( $strSerial, $strSplitMain = ';', $strSplitSub = ':', $mergeNumeric = true )
	{
		$arrResult = array ();
		if ( $strSerial )
		{
			$arrRand = explode ( $strSplitMain, $strSerial );
			while ( list ( $key, $item ) = @each ( $arrRand ) )
			{
				if ( !$item ) continue;
				$arrItem = explode ( $strSplitSub, $item );
				$arrItem[0] = str_replace ( array ( "\n", "\r" ), '', $arrItem[0] );
	
				// 是否合并数值型值
				if ( $mergeNumeric && isset ( $arrResult[$arrItem[0]] ) && is_numeric ( $arrResult[$arrItem[0]] ) && is_numeric ( $arrItem[1] ) )
				{
					$arrResult[$arrItem[0]] += $arrItem[1];
				}
				else
				{
					$arrResult[$arrItem[0]] = $arrItem[1];
				}
			}
		}
		return $arrResult;
	}
	
	// 数组转换序列
	public static function _array_to_serial ( $array, $strSplitMain = ';', $strSplitSub = ':' )
	{
		while ( list ( $key, $item ) = @each ( $array ) )
		{
			$array[$key] = $key . $strSplitSub . $item;
		}
		$strSerial = self::_join ( $strSplitMain, $array );
		return $strSerial;
	}
	
	// 多重序列转换数组
	public static function _multi_serial_to_array ( $strSerial, $lineSplit = "\n", $idSplit = '/', $strSplitMain = ';', $strSplitSub = ':', $mergeNumeric = true )
	{
		$arrResult = array ();
		if ( $strSerial )
		{
			$strSerial = str_replace ( "\r", '', $strSerial );
			if ( $strSerial && $entry = explode ( $lineSplit, $strSerial ) )
			{
				while ( list ( $key, $item ) = @each ( $entry ) )
				{
					$temp = explode ( $idSplit, $item );
					if ( $temp[0] ) $arrResult[$temp[0]-1] = self::_serial_to_array ( $temp[1], $strSplitMain, $strSplitSub, $mergeNumeric );
				}
			}
		}
		return $arrResult;
	}
	
	// 数组转换多重序列
	public static function _array_to_multi_serial ( $array, $lineSplit = "\n", $idSplit = '/', $strSplitMain = ';', $strSplitSub = ':' )
	{
		while ( list ( $key, $item ) = @each ( $array ) )
		{
			$array[$key] = $key . $idSplit . self::_array_to_serial ( $item, $strSplitMain, $strSplitSub );
		}
		$strSerial = self::_join ( $lineSplit, $array );
		return $strSerial;
	}
	
	// 连接数组并忽略空键值
	public static function _join ( $char, $array )
	{
		@reset ( $array );
		while ( list ( $key, $item ) = @each ( $array ) )
		{
			if ( strval ( $item ) == '' )
			{
				unset ( $array[$key] );
			}
		}
		$str = @join ( $char, $array );
		return $str;
	}
	
	public static function _string_to_array($str, $split = ',')
	{
		if (empty($str)) {
			return array();
		}
		$array = explode($split, $str);
		return $array;
	}
	
	public static function _array_to_string($array, $split = ',')
	{
		$str = implode($split, $array);
		return $str;
	}
	
	//随即返回数组
	public static function _array_rand($data)
	{
		return $data[array_rand($data)];
	}
}
