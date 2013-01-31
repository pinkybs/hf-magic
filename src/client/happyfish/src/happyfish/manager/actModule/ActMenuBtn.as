package happyfish.manager.actModule 
{
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import flash.display.SimpleButton;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.cacher.SwfClassCache;
	import happyfish.events.ActModuleEvent;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.model.SwfLoader;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class ActMenuBtn extends Sprite 
	{
		public var act:ActVo;
		private var btn:SimpleButton;
		public var inited:Boolean;
		public function ActMenuBtn(_act:ActVo) 
		{
			act = _act;
			name = "actBtn_" + act.actName;
			addEventListener(MouseEvent.CLICK, clickFun);
			loadSwf();
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			EventManager.getInstance().dispatchEvent(new ActModuleEvent(ActModuleEvent.ACTMENU_CLICK,act));
		}
		
		private function loadSwf():void 
		{
			var loadingItem:LoadingItem = SwfLoader.getInstance().load(act.menuUrl);
			loadingItem.addEventListener(Event.COMPLETE, loadSwf_complete);
		}
		
		private function loadSwf_complete(e:Event):void 
		{
			var tmpclass:Class = SwfClassCache.getInstance().getClass(act.menuClass);
			if (tmpclass) {
				btn = new tmpclass() as SimpleButton;
				//btn.mouseChildren = false;
				addChild(btn);
				inited = true;
				dispatchEvent(new Event(Event.COMPLETE));
			}
		}
		
		public function get index():uint {
			return act.menuIndex;
		}
	}

}