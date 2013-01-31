package happymagic.scene.world.bigScene 
{
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.events.Event;
	import happyfish.cacher.SwfClassCache;
	import happyfish.events.SwfClassCacheEvent;
	import happyfish.scene.world.WorldView;
	
	/**
	 * ...
	 * @author jj
	 */
	public class BigSceneBg extends Bitmap
	{
		public var className:String;
		
		public function BigSceneBg() 
		{
			super(null, "auto", true);
		}
		
		public function setData(className:String):void {
			
		}
		
		public function loadClass(_className:String):void {
			className = _className;
			
			if (SwfClassCache.getInstance().hasClass(className)) 
			{
				create();
			}else {
				SwfClassCache.getInstance().addEventListener(SwfClassCacheEvent.COMPLETE, loadClass_complete);
				SwfClassCache.getInstance().loadClass(className);
			}
			
		}
		
		private function loadClass_complete(e:Event):void 
		{
			SwfClassCache.getInstance().removeEventListener(SwfClassCacheEvent.COMPLETE, loadClass_complete);
			create();
		}
		
		private function create():void
		{
			var tmpclass:Class = SwfClassCache.getInstance().getClass(className);
			smoothing = true;
			bitmapData = new tmpclass(WorldView.WORLD_WIDTH, WorldView.WORLD_HEIGHT) as BitmapData;
			
			dispatchEvent(new Event(Event.COMPLETE));
		}
	}

}