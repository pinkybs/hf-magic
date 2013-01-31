package happymagic.display.control 
{
	import flash.display.DisplayObjectContainer;
	import flash.events.EventDispatcher;
	import flash.geom.Point;
	import happyfish.manager.module.ModuleManager;
	import happymagic.display.view.PiaoMsgItemView;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.manager.DisplayManager;
	
	/**
	 * ...
	 * @author jj
	 */
	public class PiaoMsgControl 
	{
		
		public function PiaoMsgControl(access:Private) 
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
				throw new Error( "PiaoMsgControl"+"单例" );
			}
		}
		
		public function setContainer(value:DisplayObjectContainer,eventer:EventDispatcher):void {
			_container = value;
			
			eventer.addEventListener(PiaoMsgEvent.SHOW_PIAO_MSG, showMsg);
		}
		
		private function showMsg(e:PiaoMsgEvent):void 
		{
			var toPoint:Point; 
			var num:uint=0;
			for (var i:int = 0; i < e.msgs.length; i++) 
			{
				if (e.msgs[i][1]) 
				{
					var tmp:PiaoMsgItemView = new PiaoMsgItemView();
					tmp.now = e.now;
					tmp.justShow = e.justShow;
					_container.addChild(tmp);
					tmp.setData(e.msgs[i][0], e.msgs[i][1], e.x, e.y, num * 1500);
					num++;
				}
			}
			
		}
		
		public static function getInstance():PiaoMsgControl
		{
			if (instance == null)
			{
				instance = new PiaoMsgControl( new Private() );
			}
			return instance;
		}
		
		
		private static var instance:PiaoMsgControl;
		private var _container:DisplayObjectContainer;
		
	}
	
}
class Private {}