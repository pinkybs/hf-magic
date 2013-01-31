package happymagic.display.view 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happyfish.utils.display.AlginControl;
	import happymagic.events.SysMsgEvent;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.UiManager;
	
	/**
	 * ...
	 * @author jj
	 */
	public class SysMsgView extends UISprite
	{
		static public const TYPE_CONFIRM:uint = 1;
		static public const TYPE_MSG:uint = 0;
		public var result:Boolean;
		private var rect:Rectangle;
		private var type:uint;
		
		private var closeTime:int = -1;
		private var closeId:Number;
		//是否已关闭
		private var delled:Boolean;
		private var callBack:Function;
		
		public function SysMsgView() 
		{
			super();
			_view = new sysMsgUi();
			
			//rect = _view.txt.getRect(_view);
			rect = new Rectangle( -115, -66, 237, 123);
			
			_view.addEventListener(MouseEvent.CLICK, clickFun, true);
		}
		
		public function setData(str:String, _type:uint = 0, _closeTime:int = -1, _callBack:Function=null):void {
			
			callBack = _callBack;
			
			delled = false;
			
			type = _type;
			closeTime = _closeTime;
			if (type==TYPE_CONFIRM) 
			{
				//确认框
				_view["yesBtn"].visible = false;
				
				_view["acceptBtn"].visible = 
				_view["refuseBtn"].visible = true;
			}else {
				//消息框
				//确认框
				_view["yesBtn"].visible = true;
				
				_view["acceptBtn"].visible = 
				_view["refuseBtn"].visible = false;
			}
			
			_view.txt.htmlText = str;
			
			//setTimeout(AlginControl.alginTxtInRect, 10, _view.txt, rect);
			AlginControl.alginTxtInRect(_view.txt, rect);
			
			if (closeTime>0) 
			{
				closeId=setTimeout(closeMe, closeTime);
			}
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target.name) 
			{
				case "yesBtn":
				closeMe();
				break;
				
				case "acceptBtn":
				accept();
				break;
				
				case "refuseBtn":
				refuse();
				break;
				
				case "closeBtn":
				if (type==TYPE_CONFIRM) 
				{
					refuse();
				}else {
					closeMe();
				}
				
				break;
			}
		}
		
		override public function closeMe(del:Boolean=false):void
		{
			if (callBack!=null) 
			{
				if (type==TYPE_CONFIRM) 
				{
					if (result) 
					{
						callBack.call();
					}
				}else if(type == TYPE_MSG){
					callBack.call();
				}
			}
			if (!delled) 
			{
				closeTime = -1;
				delled = true;
				DisplayManager.uiSprite.closeSysMsg();
			}
			
		}
		
		public function accept():void {
			result = true;
			
			var event:SysMsgEvent = new SysMsgEvent(SysMsgEvent.SYSMSG_CLOSED);
			event.result = true;
			EventManager.getInstance().dispatchEvent(event);
			
			closeMe();
		}
		
		private function refuse():void {
			result = false;
			
			var event:SysMsgEvent = new SysMsgEvent(SysMsgEvent.SYSMSG_CLOSED);
			event.result = false;
			EventManager.getInstance().dispatchEvent(event);
			
			closeMe();
		}
		
	}

}