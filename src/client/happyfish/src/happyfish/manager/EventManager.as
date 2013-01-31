package happyfish.manager
{
	import flash.events.EventDispatcher;
	import flash.geom.Point;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.events.SysMsgEvent;
	import happymagic.manager.DisplayManager;
	
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class EventManager extends EventDispatcher
	{
		
		public function EventManager(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
				}
			}
			else
			{	
				throw new Error( "EventManager"+"单例" );
			}
		}	
		
		public function closeUiBg():void {
			//dispatchEvent(new UiEvent(UiEvent.CLOSE_UIBG));
		}
		
		public function showUiBg():void {
			//dispatchEvent(new UiEvent(UiEvent.SHOW_UIBG));
		}
		
		public static function getInstance():EventManager
		{
			if (instance == null)
			{
				instance = new EventManager( new Private() );
			}
			return instance;
		}
		
		public function showLoading():void {
			trace("showLoading");
			DisplayManager.uiSprite.showLoading();
		}
		
		public function hideLoading():void {
			trace("hideLoading");
			DisplayManager.uiSprite.closeLoading();
		}
		
		/**
		 * 显示一个飘屏
		 * @param	piao_type
		 * @param	content
		 * @param	p
		 * @param	now
		 * @param	justShow	只是显示一下,不表现飞向信息面板动画
		 */
		public function showPiaoStr(piao_type:uint, content:String, p:Point = null, now:Boolean = false, justShow:Boolean = false):void {
			var event_piao_msg:PiaoMsgEvent;
			
			var msgs_new:Array = [[piao_type, content]];
			if (!p) 
			{
				p = new Point(DisplayManager.uiSprite.stage.mouseX, DisplayManager.uiSprite.stage.mouseY);
			}
			event_piao_msg = new PiaoMsgEvent(PiaoMsgEvent.SHOW_PIAO_MSG, msgs_new, p.x, p.y, now);
			event_piao_msg.justShow = justShow;
			dispatchEvent(event_piao_msg);
		}
		
		/**
		 * 打开一条系统消息
		 * @param	str
		 * @param	type	消息类型 	1 确认框		0 普通消息框
		 * @param	time	消息在多少时间(毫秒)后自动关闭 -1为不自动关闭
		 */
		public function showSysMsg(str:String,type:uint=0,time:int=-1,_callBack:Function=null):void
		{
			var event:SysMsgEvent = new SysMsgEvent(SysMsgEvent.SHOW_SYSMSG, str, type, time);
			event.callBack = _callBack;
			dispatchEvent(event);
		}
		
		
		private static var instance:EventManager;
		
	}
	
}
class Private {}