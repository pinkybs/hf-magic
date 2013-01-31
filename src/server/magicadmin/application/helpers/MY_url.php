<?php defined ( 'SYSPATH' ) or die ( 'No direct script access.' );
class url extends url_Core{
	//获取IMG路径
	public static function imgpath() {
		return Kohana::config('core.path_img');
	}
	//获取JS路径
	public static function jspath() {
		return Kohana::config('core.path_js');
	}
	//获取资源路径路径
	public static function sourcepath() {
		return Kohana::config('core.path_source');
	}
	//获取图表控件SWF路径
	public static function fchartpath() {
		return Kohana::config('core.path_fcharts');
	}

}