package happyfish.events 
{
	import flash.events.Event;
	import happyfish.manager.actModule.vo.ActVo;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class ActModuleEvent extends Event 
	{
		public var act:ActVo;
		public var moduleType:uint;
		
		public static const ACTMENU_CLICK:String = "actMenuClick";
		public static const BACKMODULE_INIT_COMPLETE:String = "backModuleInitComplete";
		//活动数据发生改变
		public static const ACT_DATA_CHANGE:String = "actDataChange"; 
		//请求关闭某个活动模块
		public static const ACT_REQUEST_CLOSE:String = "actRequestClose";
		//活动模块初始化完成
		public static const ACTMODULE_INIT_COMPLETE:String = "actModuleInitComplete";
		
		public function ActModuleEvent(type:String,_act:ActVo, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			act = _act;
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new ActModuleEvent(type,act, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("ActModuleEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}