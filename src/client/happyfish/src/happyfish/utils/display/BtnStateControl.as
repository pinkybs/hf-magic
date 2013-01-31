package happyfish.utils.display 
{
	import flash.display.DisplayObject;
	import flash.display.SimpleButton;
	import flash.display.Sprite;
	/**
	 * ...
	 * @author jj
	 */
	public class BtnStateControl
	{
		
		public function BtnStateControl() 
		{
			
		}
		
		public static function setBtnState(target:DisplayObject,state:Boolean,mouseFlag:Boolean=false):void {
			if (state) 
			{
				target.filters = [];
				if (target is SimpleButton) 
				{
					(target as SimpleButton).mouseEnabled = true;
				}else if (target is Sprite) 
				{
					(target as Sprite).mouseEnabled = 
					(target as Sprite).mouseChildren = true;
				}
				
			}else {
				target.filters = [FiltersDomain.grayFilter];
				if (target is SimpleButton) 
				{
					(target as SimpleButton).mouseEnabled = mouseFlag;
				}else if (target is Sprite) 
				{
					(target as Sprite).mouseEnabled = 
					(target as Sprite).mouseChildren = mouseFlag;
				}
			}
		}
		
	}

}