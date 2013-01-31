package happyfish.utils.display 
{
	import flash.display.DisplayObject;
	import flash.geom.Rectangle;
	/**
	 * ...
	 * @author jj
	 */
	public class ScaleControl
	{
		
		public function ScaleControl() 
		{
			
		}
		
		public static function size(target:DisplayObject, maxWidth:Number, maxHeight:Number,rect:Rectangle=null):void {
			var iconrect:Rectangle = target.getBounds(target);
			
			var biger:Boolean;
			if (iconrect.width<maxWidth && iconrect.height<maxHeight) 
			{
				biger = true;
			}
			
			var iconScale:Number;
			var wScale:Number = maxWidth / iconrect.width;
			var hScale:Number = maxHeight / iconrect.height;
			if (biger) 
			{
				iconScale = Math.min(wScale, hScale);
			}else {
				iconScale = Math.min(wScale, hScale);
			}
			iconScale = Number(iconScale.toFixed(2));
			target.scaleX=
			target.scaleY = iconScale;
			
			if (rect) 
			{
				var tmprect:Rectangle = rect;
			
				target.x = tmprect.x + (tmprect.width / 2) - iconrect.width * iconScale / 2 -(iconrect.left - target.x) * iconScale;
				
				target.y = tmprect.y + (tmprect.height / 2) - iconrect.height * iconScale / 2 - (iconrect.top - target.y) * iconScale;
			}
			
		}
		
	}

}