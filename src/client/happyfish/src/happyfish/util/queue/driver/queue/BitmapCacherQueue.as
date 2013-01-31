package happyfish.util.queue.driver.queue
{
	import flash.events.Event;
	import happyfish.cacher.bitmapMc.cacher.BitmapCacher;
	import happyfish.cacher.bitmapMc.events.BitmapCacherEvent;
	import flash.utils.getQualifiedClassName;
	import happyfish.util.queue.AbstractQueue;

	public class BitmapCacherQueue extends AbstractQueue
	{
		private static var single:BitmapCacherQueue;
		
		private var pre_item:* = null;
		public function BitmapCacherQueue()
		{
			super();
		}
		
	    override public function init():void
		{
			var bitmap_cache:BitmapCacher = BitmapCacher.getInstance();
			bitmap_cache.addEventListener(BitmapCacherEvent.BITMAP_CACHE_COMPLETED, this.next);
		}
		
		public static function getInstance():BitmapCacherQueue
		{
			if (single === null) {
				single = new BitmapCacherQueue;
			}
			return single;
		}
		
		override public function process(class_name:*):void 
		{
			//if (this.pre_item == null || getQualifiedClassName(this.pre_item) != getQualifiedClassName(class_name)) {
				var bitmap_cache:BitmapCacher = BitmapCacher.getInstance();
				bitmap_cache.createCache(class_name as Class);
			//}
			this.pre_item = class_name;
		}
		
		override public function next(e:Event):void
		{
			if (queue.length === 0) {
				this.is_running = false;
				//派发一个全部完成的事件
				dispatchEvent(new Event(Event.COMPLETE));
				return;
			} else {
				this.run();
			}
		}
	}
}