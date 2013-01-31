package happymagic.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author jj
	 */
	public class PiaoMsgEvent extends Event 
	{
		public static const SHOW_PIAO_MSG:String = "showPiaoMsg";
		
		
		public var msgs:Array;
		public var x:int;
		public var y:int;
		public var now:Boolean;
		public var justShow:Boolean;
		/**
		 * 飘屏事件
		 * @param	type
		 * @param	_msgs	[Array]	样式:[[PiaoMsgType.TYPE_BLUE,-20],[PiaoMsgType.TYPE_BAD_STRING,"场景人数已满"]]
		 * @param	_x
		 * @param	_y
		 * @param	bubbles
		 * @param	cancelable
		 */
		public function PiaoMsgEvent(type:String, _msgs:Array, _x:int, _y:int, _now:Boolean = false, bubbles:Boolean = false, cancelable:Boolean = false) 
		{ 
			super(type, bubbles, cancelable);
			msgs = _msgs;
			x = _x;
			y = _y;
			now = _now;
		} 
		
		public override function clone():Event 
		{ 
			return new PiaoMsgEvent(type, msgs, x, y,now,bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("PiaoMsgEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}