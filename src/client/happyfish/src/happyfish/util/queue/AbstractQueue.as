package happyfish.util.queue
{
	import flash.errors.IllegalOperationError;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.utils.Dictionary;
	import flash.utils.getQualifiedClassName;
	
	public class AbstractQueue extends EventDispatcher
	{
		public var queue:Array = new Array();
		public var is_running:Boolean = false;
		
		//单例
		private static var dict:Dictionary = new Dictionary();
		public function AbstractQueue()
		{
			/**
        	var ref:Class = this["constructor"] as Class;
            if (dict[ref])
                throw new IllegalOperationError(getQualifiedClassName(this) + " Abstract Method!Please OverRide this");
            else
            	dict[ref] = this;
			*/
			this.init();
		}
		
		public function init():void
		{
			throw new Error("Abstract Method!Please OverRide this");
		}
		
        /**
         * 获取单例类，若不存在则返回空
         * 
         * @param ref	继承自Singleton的类
         * @return 
         * 
         */
        public static function getInstance(ref:Class):*
        {
			//var ref:Class = this["constructor"] as Class;
        	if (dict[ref] == null)
        		dict[ref] = new ref();
        	
        	return dict[ref];
        }
		
		/**
		 * TODO 如果queue数组里有,则不插入
		 * @param	item_basic
		 */
		public function push(item_basic:*):void
		{
			queue.push(item_basic);
			if (this.is_running === false) {
				this.is_running = true;
				this.run();
			}
		}
		
		public function run():void
		{	
			var item_basic:* = queue.pop();
			this.process(item_basic);
			//item_basic.addMc();
		}
		
		public function process(item_basic:*):void { 
			throw new Error("Abstract Method!Please OverRide this");
		}
		
		public function next(e:Event):void
		{
			if (queue.length === 0) {
				this.is_running = false;
				trace(111);
				//派发一个全部完成的事件
				dispatchEvent(new Event(Event.COMPLETE));
				return;
			} else {
				this.run();
			}
		}
	}
}