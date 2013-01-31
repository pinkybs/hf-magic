package happymagic.display.view.rehandling 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happyfish.utils.HtmlTextTools;
	import happymagic.command.RehandlingRequestCommand;
	import happymagic.display.view.rehandling.event.RehandlingEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.RehandlingManager;
	import happymagic.model.vo.RehandlingVo;
	/**
	 * ...
	 * @author zc
	 */
	public class RehandlingLockView extends UISprite
	{
		private var iview:RehandlingLockViewUi;
		private var data:RehandlingVo;		
		public function RehandlingLockView() 
		{
			_view = new RehandlingLockViewUi();
			iview = view as RehandlingLockViewUi;	
			iview.addEventListener(MouseEvent.CLICK, clickrun);
		}

		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "closebtn":
			       iview.mouseChildren = false;
				   iview.mouseEnabled = false;
				   closeMe(true);
				   break;
				
				case "affirm":
				    iview.mouseChildren = false;
					iview.mouseEnabled = false;
					
				    var rehandlingRequestCommand:RehandlingRequestCommand = new RehandlingRequestCommand();
					rehandlingRequestCommand.addEventListener(Event.COMPLETE, rehandlingRequestCommandComplete);
					rehandlingRequestCommand.setData(data.avatarId);	
					
				   break;
			}
		}
		
		private function rehandlingRequestCommandComplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, rehandlingRequestCommandComplete);
			iview.mouseChildren = true;
			iview.mouseEnabled = true;	
			
			var rehandlingVo:RehandlingVo = RehandlingManager.getInstance().getRehandlingVo(data.avatarId);
			
		    var rehandlingAwardView:RehandlingAwardView = DisplayManager.uiSprite.addModule(RehandlingDict.MODULE_REHANDLINGAWARD, RehandlingDict.MODULE_REHANDLINGAWARD_CLASS, false, AlginType.CENTER, 10, -70) as RehandlingAwardView;					 
		    DisplayManager.uiSprite.setBg(rehandlingAwardView);					 
		    rehandlingAwardView.setData(rehandlingVo);	
		    
			RehandlingManager.getInstance().setRehandlingStateVo(rehandlingVo.avatarId);
			
			EventManager.getInstance().dispatchEvent(new RehandlingEvent(RehandlingEvent.COMPLETE));
			
			closeMe(true);
		}
		
		public function setData(_data:RehandlingVo):void
		{
			data = _data;
			
			if (data.type == 1)
			{
				iview.icon.gotoAndStop("coin");
				
				if (DataManager.getInstance().currentUser.coin < data.num)
				{
			        iview.num.htmlText = HtmlTextTools.redWords(String(data.num));					
				}
				else
				{
			        iview.num.text = String(data.num);					
				}
			}
			else
			{
				iview.icon.gotoAndStop("gem");	
				
				if (DataManager.getInstance().currentUser.gem < data.num)
				{
			        iview.num.htmlText = HtmlTextTools.redWords(String(data.num));						
				}
				else
				{
			        iview.num.text = String(data.num);					
				}				
			}
			
			
			

			loadIcon();
		}		
		
		private function loadIcon():void 
		{
			var icon:IconView = new IconView(80, 90, new Rectangle(-42, -90, 80, 90));
			icon.setData(data.className);
			iview.addChild(icon);			
		}
	}

}