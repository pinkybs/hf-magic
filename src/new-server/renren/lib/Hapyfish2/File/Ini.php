<?php

class Hapyfish2_File_Ini
{
	const LF = "\r\n";
	
	public static function read($filename, $section = true)
	{
		return parse_ini_file($filename, $section);
	}
	
	public static function write($filename, $info, $section = true)
	{
		
	}

}