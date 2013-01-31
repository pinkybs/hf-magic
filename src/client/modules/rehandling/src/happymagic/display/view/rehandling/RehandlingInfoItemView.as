package happymagic.display.view.rehandling 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happyfish.utils.display.BtnStateControl;
	import happyfish.utils.HtmlTextTools;
	import happymagic.display.view.rehandling.event.RehandlingEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.RehandlingManager;
	import happymagic.model.vo.RehandlingStateVo;
	import happymagic.model.vo.RehandlingVo;
	/**
	 * ...
	 * @author zc
	 */
	public class RehandlingInfoItemView extends GridItem
	{
		private var iview: RehandlingInfoItemViewUi;
		private var data:RehandlingVo;
		private var rehandlingStateVo:RehandlingStateVo;
		public function RehandlingInfoItemView(uiview:MovieClip) 
		{
			super(uiview);
			iview = uiview as RehandlingInfoItemViewUi;
			view.mouseChildren = true;
			iview.addEventListener(MouseEvent.CLICK, clickfun);	
			EventManager.getInstance().addEventListener(RehandlingEvent.SELECT, selectUpdata);
			iview.yesbtn.visible = false;	
			iview.change.visible = false;
			iview.lock.visible = false;
			iview.deblocking.visible = false;
						
		}
		
		private function clickfun(e:MouseEvent):void 
		{
			switch(e.target.name)
		    {
				case "deblocking":
				   iview.mouseChildren = false;
				   iview.mouseEnabled = false;
				   
			       var rehandlingVo:RehandlingVo = RehandlingManager.getInstance().getRehandlingVo(data.avatarId);				   
				   var rehandlingLockView:RehandlingLockView = DisplayManager.uiSprite.addModule(RehandlingDict.MODULE_REHANDLINGLOCK, RehandlingDict.MODULE_REHANDLINGLOCK_CLASS, false, AlginType.CENTER, 10, -70) as RehandlingLockView;					 
		           DisplayManager.uiSprite.setBg(rehandlingLockView);					 
		           rehandlingLockView.setData(rehandlingVo);
				   
				   iview.mouseChildren = true;
				   iview.mouseEnabled = true;		   
				   break;
				   
				case "change":
			       iview.yesbtn.visible = true;	
				   iview.change.visible = false;
				   
			       DataManager.getInstance().setVar("RehandlingSelect", data.avatarId);				   
				   EventManager.getInstance().dispatchEvent(new RehandlingEvent(RehandlingEvent.SELECT));
				   
				   break;
			}
			
			
		}
		
		private function selectUpdata(e:RehandlingEvent):void 
		{  
			
			if (data.avatarId == DataManager.getInstance().getVar("RehandlingSelect"))
			{
				iview.yesbtn.visible = true;
				iview.change.visible = false;				
			}
			else
			{
				if (rehandlingStateVo.lock)
			    {
				return;
			    }
				iview.yesbtn.visible = false;	
				iview.change.visible = true;				
			}
		}
		
		override public function setData(value:Object):void 
		{
            data = value as RehandlingVo;
			
            rehandlingStateVo = RehandlingManager.getInstance().getRehandlingStateVo(data.avatarId);	
			
			if (rehandlingStateVo.lock)
			{
			   iview.deblocking.visible = true;		
			   iview.lock.visible = true;
			   
			   switch(data.type)
			   {
				   case 1:
				        iview.lock["icon"].gotoAndStop("coin");
						
						if (DataManager.getInstance().currentUser.coin < data.num)
						{
			       	 		iview.lock["num"].htmlText = HtmlTextTools.redWords(String(data.num));	
							BtnStateControl.setBtnState(iview.deblocking, false);
						}
						else
						{
							BtnStateControl.setBtnState(iview.deblocking, true);
			           	 	iview.lock["num"].text = String(data.num);				
						}						
				     break;
					 
				   case 2:
				        iview.lock["icon"].gotoAndStop("gem");		
						
						if (DataManager.getInstance().currentUser.gem < data.num)
						{
			       	 		iview.lock["num"].htmlText = HtmlTextTools.redWords(String(data.num));	
							BtnStateControl.setBtnState(iview.deblocking, false);							
						}
						else
						{
			           	 	iview.lock["num"].text = String(data.num);	
							BtnStateControl.setBtnState(iview.deblocking, true);							
						}							
				     break;
			   }
			   
			}
			else
			{
				if (data.avatarId == DataManager.getInstance().getVar("RehandlingSelect"))
			    {
				    iview.yesbtn.visible = true;
				    iview.change.visible = false;
			    }
				else
				{
					iview.change.visible = true;
				}
			        				
			}
			
			loadIcon();
		}	
		
		private function loadIcon():void
		{
			var icon:IconView = new IconView(70, 80, new Rectangle(21, 22, 70, 80));
			icon.setData(data.className);
			iview.addChildAt(icon, iview.getChildIndex(iview.lock));
		}
	}

}