package happyfish.view 
{
	import com.greensock.easing.Bounce;
	import com.greensock.TweenLite;
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import happyfish.display.view.UISprite;
	import happyfish.manager.mouse.MouseManager;
	import happyfish.utils.display.AlginControl;
	import happyfish.utils.display.TextFieldTools;
	import happyfish.utils.HtmlTextTools;
	import happymagic.manager.DisplayManager;
	
	/**
	 * ...
	 * @author jj
	 */
	public class SysChatsMsgView extends UISprite
	{
		private var iview:guidesChatsUi;
		private var data:Array;
		private var curIndex:uint;
		private var typeTxtControl:TextFieldTools;
		private var container:DisplayObjectContainer;
		private var rect:Rectangle;
		private var txtReady:Boolean;
		private var teacherface:String;
		private var initId:uint;
		public var lastbool:Boolean; 
		
		public function SysChatsMsgView() 
		{
			super();
			
			_view = new guidesChatsUi();
			_view.mouseEnabled = false;
			_view.mouseChildren = false;
			iview = _view as guidesChatsUi;
            lastbool = false;
			rect = iview.txt.getRect(iview);
			
		}
		public function setData(strs:Array,_teacherface:String):void 
		{
			teacherface = _teacherface;
			iview.lele.gotoAndStop(teacherface);
			if (data) 
			{
				data = data.concat(strs);
				
			}else {
				data = strs;
				curIndex = 1;
				initCurChat();				
				startClickEvent();
			}
		}
		
		private function startClickEvent():void
		{
			if (_view.stage&&data !=null) 
			{
				_view.stage.addEventListener(MouseEvent.CLICK, clickFun,true);
			}else {
				_view.addEventListener(Event.ADDED_TO_STAGE, addToStage);
			}
			
		}
		
		private function addToStage(e:Event):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, addToStage);
			_view.stage.addEventListener(MouseEvent.CLICK, clickFun,true);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			e.stopImmediatePropagation();
			//if (typeTxtControl.typeEnd) 
			//{
				//
			//}else {
				//typeTxtControl.stopTimer(true);
			//}
				nextChat();
			

			
		}
		
		private function nextChat():void {
			curIndex++;
			if (curIndex>data.length) 
			{
				chatIsEnd();
				data = null;
			}else {
				initCurChat();
			}
		}
		
		private function chatIsEnd():void
		{
			
			iview.mouseClickIcon.visible = false;
			
			if (data == null)
			{
				return;
			}
			if (data.length >= 2)
			{
				MouseManager.getInstance().clearTmpIcon(101);
			}			
			_view.stage.removeEventListener(MouseEvent.CLICK, clickFun,true);		
			dispatchEvent(new Event(Event.COMPLETE));
		}
		
		private function initCurChat():void {
			//if(!typeTxtControl) typeTxtControl = new TextFieldTools(true);
			//typeTxtControl.typeEffect(iview.txt, data[curIndex-1],1);
			
			iview.txt.htmlText = data[curIndex - 1];

			if (!txtReady) 
			{
				if (iview.txt.textWidth>1) 
				{
					txtReady = true;
				}else {
					if (initId)
					{
						clearTimeout(initId);
					}
					initId = setTimeout(initCurChat, 100);
					return;
				}
			}
			
			
			AlginControl.alginTxtInRect(iview.txt, rect);
							
			iview.parent.addChild(iview);
			
			//iview.visible = true;
			
			//判断当前是否有2页情况
			if (data.length >= 2)
			{
				iview.mouseClickIcon.visible = true;
				MouseManager.getInstance().addMouseIcon("mouseshouxing", new mouseClickTips());
				
				MouseManager.getInstance().setTmpIcon(MouseManager.getInstance().getMouseIcon("mouseshouxing"), 100);
			}
			else
			{	
				if (lastbool)
				{					
					lastbool = false;
					chatIsEnd();					
				}
				else
				{
				  nextChat();	
				}
					

			}
	
		}
		
		public function move(tox:Number,toy:Number):void {
			TweenLite.killTweensOf(this);
			
			TweenLite.to(this, .5, { x:tox, y:toy } );
		}
		
		public function clickend():void
		{
			closeMe();
		}
		

		
	}

}
