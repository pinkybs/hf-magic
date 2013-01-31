package happymagic.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author jj
	 */
	public class MainInfoEvent extends Event 
	{
		//用户信息进入假记数模式
		public static const START_TMP_STATE:String = "mainInfoStartTmpState";
		//需要重载用户信息事件
		public static const RELOAD:String = "mainInfoReload";
		public function MainInfoEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new MainInfoEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("MainInfoEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}