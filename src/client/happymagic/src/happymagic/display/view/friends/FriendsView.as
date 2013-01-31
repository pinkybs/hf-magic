package happymagic.display.view.friends 
{
	import flash.display.StageDisplayState;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.view.PageList;
	import happyfish.display.view.UISprite;
	import happyfish.events.ModuleEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happymagic.events.FriendsEvent;
	import happymagic.events.MagicClassBookEvent;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.PublicDomain;
	import happymagic.model.command.LoadFriendsCommand;
	import happymagic.model.MagicJSManager;
	/**
	 * ...
	 * @author jj
	 */
	public class FriendsView extends UISprite
	{
		private var data:Array;
		private var friendList:FriendsListView;
		private var iview:friendsUi;
		public function FriendsView() 
		{
			
			super();
			_view = new friendsUi();
			iview = _view as friendsUi;
			_view.addEventListener(Event.ADDED_TO_STAGE, bodyAddToStage);
			
			friendList = new FriendsListView(new friendsListUi(), _view);
			
			
			_view.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			//EventManager.getInstance().addEventListener(MagicClassBookEvent.SHOW_EVENT, showMagicClass);
			ModuleManager.getInstance().addEventListener(ModuleEvent.MODULE_CLOSE, moduleClose);
			
			EventManager.getInstance().addEventListener(SceneEvent.START_DIY, diyStart);
			EventManager.getInstance().addEventListener(SceneEvent.DIY_FINISHED, diyFinished);
			EventManager.getInstance().addEventListener(SceneEvent.DIY_CANCELDIY, diyFinished);
			
			EventManager.getInstance().addEventListener(FriendsEvent.FRIENDS_DATA_COMPLETE, data_complete);
			EventManager.getInstance().addEventListener(FriendsEvent.SHOW_FRIENDS_VIEW, showEvent);
			EventManager.getInstance().addEventListener(FriendsEvent.HIDE_FRIENDS_VIEW, hideEvent);
		}
		
		private function moduleClose(e:ModuleEvent):void 
		{
			switch (e.moduleName) 
			{
				case "magicbox":
				//ModuleManager.getInstance().showModule(name);
				break;
			}
		}
		
		
		
		private function clickFun(e:MouseEvent):void 
		{
			
			switch (e.target) 
			{
				case iview.yaoqinBtn:
					MagicJSManager.getInstance().goInvite();
					iview.stage.displayState = StageDisplayState.NORMAL;
				break;
				
			}
		}
		
		private function loadData():void {
			var loader:LoadFriendsCommand = new LoadFriendsCommand();
			loader.loadFriend();
		}
		
		
		
		private function data_complete(e:FriendsEvent):void 
		{
			setData(DataManager.getInstance().friends);
		}
		
		private function setData(value:Array):void
		{
			
			data = value;
			
			friendList.setData(data);
		}
		
		private function diyFinished(e:SceneEvent):void 
		{
			ModuleManager.getInstance().showModule(name);
		}
		
		private function showMagicClass(e:MagicClassBookEvent):void 
		{
			//隐藏自己
			ModuleManager.getInstance().closeModule(name);
		}
		
		private function diyStart(e:SceneEvent):void 
		{
			//隐藏自己
			ModuleManager.getInstance().closeModule(name);
		}
		
		private function bodyAddToStage(e:Event):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, bodyAddToStage);
			
			loadData();
		}
		
		private function hideEvent(e:FriendsEvent):void 
		{
			ModuleManager.getInstance().closeModule(name);
		}
		
		private function showEvent(e:FriendsEvent):void 
		{
			ModuleManager.getInstance().showModule(name);
		}
	}

}