package happymagic.display.view.rehandling 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happymagic.command.RehandlingRequestCommand;
	import happymagic.display.view.rehandling.event.RehandlingEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.RehandlingManager;
	import happymagic.model.vo.RehandlingStateVo;
	/**
	 * ...
	 * @author zc
	 */
	public class RehandlingInfoView extends UISprite
	{
		
		private var iview:RehandlingInfoViewUi;
		private var list:RehandlingInfoListView;
		public function RehandlingInfoView() 
		{
			_view = new RehandlingInfoViewUi();
			iview = _view as RehandlingInfoViewUi;	
			//设置选择的默认值为当前人物形象
			DataManager.getInstance().setVar("RehandlingSelect", DataManager.getInstance().curSceneUser.avatar);
			EventManager.getInstance().addEventListener(RehandlingEvent.COMPLETE, LockComplete);
			
			list = new RehandlingInfoListView(new RehandlingInfoListViewUi(), iview, 5, false, false);
			list.init(780, 160, 110, 150, -205, -155);
			list.x = -70;
			list.y = 70;
			list.setGridItem(RehandlingInfoItemView, RehandlingInfoItemViewUi);			
			
			iview.addEventListener(MouseEvent.CLICK, clickrun);
		}
		
		private function LockComplete(e:RehandlingEvent):void 
		{
			list.initPage();
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
		    {
				case "closebtn":
				    iview.mouseChildren = false;
					iview.mouseEnabled = false;
					
					var rehandlingStateVo:RehandlingStateVo = RehandlingManager.getInstance().getRehandlingStateVo(DataManager.getInstance().getVar("RehandlingSelect"));
					
					if (DataManager.getInstance().getVar("RehandlingSelect") == DataManager.getInstance().curSceneUser.avatar)
					{
						iview.mouseChildren = true;
			            iview.mouseEnabled = true;
						closeMe(true);
						return;					
					}
					else if(rehandlingStateVo.lock)
					{
						iview.mouseChildren = true;
			            iview.mouseEnabled = true;
						closeMe(true);						
						return
					}
					
				    var rehandlingRequestCommand:RehandlingRequestCommand = new RehandlingRequestCommand();
					rehandlingRequestCommand.addEventListener(Event.COMPLETE, rehandlingRequestCommandComplete);
					rehandlingRequestCommand.setData(DataManager.getInstance().getVar("RehandlingSelect"));
									
				 break;
			}
		}
		
		private function rehandlingRequestCommandComplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, rehandlingRequestCommandComplete);
			iview.mouseChildren = true;
			iview.mouseEnabled = true;	
			
			closeMe(true);
		}
		
		public function setData():void
		{
			
		  list.setData(DataManager.getInstance().getVar("RehandlingInitstatic"));	
		  
		}
		
	}

}