<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
/*
 * Created on 2009-2-26
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
/**
 * Media URL
 *
 * Returns the "media_url" item from your config file
 *
 * @access	public
 * @return	string
 * @since 2007/12/12
 * @author xupeng
 */	
class media_Core {
	public static function base()
	{
		$base_url = Kohana::config('core.meida_domain');
		$media_path = Kohana::config('media.media_path');
		
		return $base_url.$media_path;
	}
	
	public static function media_url()
	{
		$base_url = Kohana::config('core.meida_domain');
		$media_path = Kohana::config('media.media_path');
		
		return $base_url.$media_path;
	}
	
	public static function server_static_url()
	{
		$base_url = Kohana::config('core.meida_domain');
		$media_path = Kohana::config('media.media_path');
		
		//获取版本
		$mserver_path = Kohana::config('core.media_server');
		
		return $base_url.$mserver_path.Kohana::config('media.static_path');
	}
	
	public static function static_url()
	{
		$base_url = Kohana::config('core.meida_domain');
		$media_path = Kohana::config('media.static_path');
		
		return $base_url.$media_path;
	}
	
	public static function img_url()
	{
		$base_url = Kohana::config('core.meida_domain');
		$media_path = Kohana::config('media.img_path');
		
		return $base_url.$media_path;
	}
	
	public static function js_url()
	{
		$base_url = Kohana::config('core.meida_domain');
		$media_path = Kohana::config('media.js_path');
		
		return $base_url.$media_path;
	}
	
	public static function css_url()
	{
		$base_url = Kohana::config('core.meida_domain');
		$media_path = Kohana::config('media.css_path');
		
		return $base_url.$media_path;
	}
	
	public static function flash_url()
	{
		$base_url = Kohana::config('core.meida_domain');
		$media_path = Kohana::config('media.flash_path');
		$version_path = Kohana::config('version.flash');
		
		return $base_url.$media_path.$version_path;
	}
	
	public static function file_url()
	{
		$base_url = Kohana::config('core.meida_domain');
		$media_path = Kohana::config('media.file_path');
		
		return $base_url.$media_path;
	}
}
?>
