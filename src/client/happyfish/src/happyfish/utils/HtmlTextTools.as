package happyfish.utils
{
	import flash.geom.Rectangle;
	import flash.text.TextField;
	import flash.text.TextFormat;
	import flash.utils.setTimeout;
	/**
	 * ...
	 * @author slamjj
	 */
	public class HtmlTextTools
	{
		public static var defaultFont:String;
		public static var defaultSize:uint;
		public function HtmlTextTools() 
		{
			
		}
		
		public static function fontWord(str:String, color:String = "#000000", size:uint = 0, font:String = ""):String {
			if (size == 0) size = defaultSize;
			if (font == "") font = defaultFont;
			
			return "<font " + "color='" + color + "' " + "face='" + font + "' " + "size='" + size + "' >" + str + "</font>";
		}
		
		public static function redWords(str:String):String {
			return "<font color='#FF0000'>" + str + "</font>";
		}
		
		public static function greenWords(str:String):String {
			return "<font color='#339933'>" + str + "</font>";
		}
		
		public static function blueWords(str:String):String {
			return "<font color='#0000FF'>" + str + "</font>";
		}
		
		public static function linkTo(str:String,url:String,type:String="blank"):String {
			return "<a href='" + url + "' target='" + type + "'>" + str + "</a>";
		}
		
		
		/**
		 * 把原来元件里的属性保留下来 比如字间隔，字体 颜色等等
		 * @param	field
		 * @param	str
		 * @param	color
		 * @param	autoCenterY		文本框内文字在Y上居中在文本框
		 */
		public static function setTxtSaveFormat(field:TextField, str:String, color:Number = 0, autoCenterY:Boolean = false):void {
			if (autoCenterY) 
			{
				var rect:Rectangle = field.getRect(field);
			}
			
			var tmpFormat:TextFormat = field.getTextFormat();
			field.htmlText = str;
			if (color) 
			{
				tmpFormat.color = color;
			}
			field.setTextFormat(tmpFormat);
			
			if (autoCenterY) 
			{
				//setTimeout(alginTextFieldY,200, field,rect);
				alginTextFieldY(field,rect);
			}
		}
		
		public static function alginTextFieldY(field:TextField,rect:Rectangle):void {
			field.y = field.y + rect.height / 2 - field.textHeight / 2;
		}
	}

}