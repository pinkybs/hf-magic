package happyfish.manager.actModule.display 
{
	import flash.display.Sprite;
	import happyfish.events.ActModuleEvent;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class ActModuleBase extends Sprite 
	{
		public var actData:ActVo;
		//模块的类型	1 
		public var moduleType:uint;
		
		//普通活动模块
		public static const TYPE_NORMAL:uint = 1;
		//初始加载的后台活动模块
		public static const TYPE_BACKMODULE:uint = 2;
		
		public function ActModuleBase() 
		{
			
		}
		
		/**
		 * 
		 * @param	actVo
		 * @param	type	[uint] 1 活动模块	2 backModule
		 * 
		 * 
		 */
		public function init(actVo:ActVo,_type:uint=1):void {
			actData = actVo;
			moduleType = _type;
			
			//init_complete();
		}
		
		public function init_complete():void {
			var event:ActModuleEvent = new ActModuleEvent(ActModuleEvent.ACTMODULE_INIT_COMPLETE,actData);
			event.moduleType = moduleType;
			dispatchEvent(event);
		}
		
		public function close():void {
			var event:ActModuleEvent = new ActModuleEvent(ActModuleEvent.ACT_REQUEST_CLOSE,actData);
			event.moduleType = moduleType;
			EventManager.getInstance().dispatchEvent(event);
		}
		
	}

}