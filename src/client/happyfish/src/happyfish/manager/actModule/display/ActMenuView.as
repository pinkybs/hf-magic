package happyfish.manager.actModule.display 
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.net.navigateToURL;
	import flash.net.URLRequest;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.actModule.ActMenuBtn;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.model.JSManager;
	import xrope.HLineLayout;
	import xrope.VLineLayout;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class ActMenuView extends UISprite 
	{
		private var btns:Array;
		private var layouter:VLineLayout;
		
		public function ActMenuView() 
		{
			super();
			btns = new Array();
			_view = new MovieClip();
			
			layouter = new VLineLayout(_view, 0, 0, 100, 60);	
			layouter.useBounds = true;
			
			_view.addEventListener(MouseEvent.CLICK, clickFun);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			var actMenu:ActMenuBtn = e.target.parent as ActMenuBtn;
			var url:URLRequest;	
			
			if (actMenu.act.menuJs) 
			{
				JSManager.getInstance().call(actMenu.act.menuJs);
			}
			
			if (actMenu.act.menuLink) 
			{
				url = new URLRequest(actMenu.act.menuLink);
                navigateToURL(url, "_blank");	
			}
			
			if (actMenu.act.moduleUrl) 
			{
				ActModuleManager.getInstance().addActModule(actMenu.act);
			}
			
		}
		
		public function add(btn:ActMenuBtn):void {
			btns.push(btn);
			layouter.add(btn);
			if (!btn.inited) 
			{
				btn.addEventListener(Event.COMPLETE, btnInit_complete);
			}else {
				layouter.layout();
			}
		}
		
		private function btnInit_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, btnInit_complete);
			layouter.layout();
		}
		
		private function sortBtn():void {
			btns.sortOn("index", Array.NUMERIC);
			layouter.removeAll();
			for (var i:int = 0; i < btns.length; i++) 
			{
				var item:ActMenuBtn = btns[i];
				layouter.add(item);
			}
			layouter.layout();
		}
		
	}

}