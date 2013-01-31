package happyfish.utils.display 
{
	import flash.display.DisplayObjectContainer;
	import flash.geom.Rectangle;
	import flash.text.TextField;
	import flash.utils.setTimeout;
	/**
	 * ...
	 * @author jj
	 */
	public class AlginControl
	{
		
		public function AlginControl() 
		{
			
		}
		
		public static function alginInRect(target:DisplayObjectContainer,rect:Rectangle):void {
			
			var iconrect:Rectangle = target.getBounds(target);
			
			var tmprect:Rectangle = rect;
			
			var iconScale:Number = target.scaleX;
			
			target.x = tmprect.x + (tmprect.width / 2) - iconrect.width * iconScale / 2 -(iconrect.left - target.x) * iconScale;
			
			target.y = tmprect.y + (tmprect.height / 2) - iconrect.height * iconScale / 2 - (iconrect.top - target.y) * iconScale;
		}
		
		public static function alginTxtInRect(target:TextField, rect:Rectangle):void {
			if (target.textWidth<2) 
			{
				setTimeout(alginTxtInRect, 10, target, rect);
				return;
			}
			target.x = rect.left + rect.width / 2 - target.textWidth / 2;
			target.y = rect.top + rect.height / 2 - target.textHeight / 2;
		}
		
	}

}