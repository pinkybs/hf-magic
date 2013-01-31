package happyfish.util.queue.driver.queue 
{
	import flash.events.Event;
	import happyfish.cacher.ClassCache;
	import happyfish.util.queue.AbstractQueue;
	
	/**
	 * ...
	 * @author Beck
	 */
	public class ClassCacherQueue extends AbstractQueue
	{
		private var pre_item:* = null;
		private static var single:ClassCacherQueue;
		public function ClassCacherQueue() 
		{
			super();
		}
		
		public static function getInstance():ClassCacherQueue
		{
			if (single === null) {
				single = new ClassCacherQueue;
			}
			return single;
		}
		
	    override public function init():void
		{
			var cache:ClassCache = ClassCache.getInstance();
			cache.addEventListener(Event.COMPLETE, this.next);
		}
		
		override public function process(url:*):void 
		{
			var cache:ClassCache = ClassCache.getInstance();
			cache.loadClassSwf(url);
		}
		
	}

}