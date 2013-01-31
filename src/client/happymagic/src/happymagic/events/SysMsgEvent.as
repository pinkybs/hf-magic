package happymagic.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author jj
	 */
	public class SysMsgEvent extends Event 
	{
		public static const MSGTYPE_MSG:uint = 0;
		public static const MSGTYPE_CONFIRM:uint = 1;
		
		public static const SHOW_SYSMSG:String = "showSysMsg";
		public static const SYSMSG_CLOSED :String = "sysMsgClosed";
		
		public var msgType:uint;
		public var content:String;
		public var result:Boolean;
		public var showTime:int = -1;
		
		public var callBack:Function;
		public function SysMsgEvent(type:String,_content:String="",_msgType:uint=0,_showTime:int=-1, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			msgType = _msgType;
			content = _content;
			showTime = _showTime;
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new SysMsgEvent(type,content,msgType,showTime, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("SysMsgEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}