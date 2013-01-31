package happyfish.cacher.bitmapMc.events
{
	import flash.events.Event;
	public class BitmapCacherEvent extends Event
	{
		//bitmap cache创建完成
		public static const BITMAP_CACHE_COMPLETED:String = "bitmap_cache_completed";
		public static const SPRITE_CACHE_COMPLETED:String = "sprite_cache_completed";
		
		public var data:Object;
		
		public var class_name:String;
		
		public function BitmapCacherEvent(type:String, $class_name:String = '', $data:Object = null, bubbles:Boolean=true, cancelable:Boolean=true)
		{
			this.class_name = $class_name;
			this.data = $data;
			super(type, bubbles, cancelable);
		}
	}
}