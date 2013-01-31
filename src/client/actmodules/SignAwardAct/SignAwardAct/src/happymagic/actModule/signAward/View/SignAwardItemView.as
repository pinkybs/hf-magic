package happymagic.actModule.signAward.View 
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import flash.net.navigateToURL;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Tooltips;
	import happyfish.manager.EventManager;
	import happyfish.utils.display.BtnStateControl;
	import happymagic.actModule.signAward.Commond.SignAwardCommond;
	import happymagic.actModule.signAward.SignAwardDict;
	import happymagic.actModule.signAward.View.event.SignAwardEvent;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.SysMsgView;
	import happymagic.display.view.task.TaskNeedItemView;
	import happymagic.display.view.ui.AwardResultView;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.TestCommand;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.SignAwardVo;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class SignAwardItemView extends GridItem
	{
		private var iview: signawarditemview;
		private var data:SignAwardVo;
		private var whiteItemView:TaskNeedItemView;
		private var greenItemView:TaskNeedItemView;
		private var rectangle:Rectangle;
		private var currentDay:int;
		
		public function SignAwardItemView(uiview:MovieClip) 
		{
			super(uiview);
			iview = uiview as signawarditemview;
			iview.greennumber.stop();
			iview.bluenumber.stop();
		}
		
		private function clickfun(e:MouseEvent):void 
		{
			switch(e.target.name)
		    {		
				case "awardbtn":
				     iview.removeEventListener(MouseEvent.CLICK, clickfun);
				     signawardControl();
				break;
			}
		}
		
		override public function setData(value:Object):void 
		{
			data = value as SignAwardVo;
			
			currentDay = DataManager.getInstance().currentUser.signDay;
			
			if (currentDay >= 6)
			{
				currentDay = 5;
			}
			
		    if (currentDay == data.day)
			{
				iview.greennumber.visible = false;
				iview.bluenumber.gotoAndStop(data.day);
				iview.addEventListener(MouseEvent.CLICK, clickfun);
			}
			else
			{
				iview.bluenumber.visible = false;
				iview.greennumber.gotoAndStop(data.day);
				BtnStateControl.setBtnState(iview.awardbtn, false);
			}
			rectangle = new Rectangle(-24, -24, 50, 50);
            whiteItemView = new TaskNeedItemView(new signAwardYellowItemUi(), 40, rectangle);
			whiteItemView.setData(data.awards[0]);
			iview.addChild(whiteItemView.view);
			whiteItemView.view.x = 30;
			whiteItemView.view.y = 62;
			
			rectangle = new Rectangle(-24, -24, 50, 50);
            greenItemView = new TaskNeedItemView(new signAwardGreenItemUi(), 40, rectangle);
			greenItemView.setData(data.fansaward[0]);
			iview.addChild(greenItemView.view);
			greenItemView.view.x = 30;
			greenItemView.view.y = 170;
			
			if (DataManager.getInstance().currentUser.isfans)
			{
				BtnStateControl.setBtnState(greenItemView.view, true);
			}
			else
			{
				BtnStateControl.setBtnState(greenItemView.view, false);				
			}
			
			iview.mouseChildren = true;

		}
		
		//领奖的过程
		private function signawardControl():void 
		{
			 var signawardcommond:SignAwardCommond = new SignAwardCommond();
			 signawardcommond.init();
			 signawardcommond.addEventListener(Event.COMPLETE , signawardcomplete);
		}
		
		
		private function signawardcomplete(e:Event):void
		{
             iview.addEventListener(MouseEvent.CLICK, clickfun);   
			   if (DisplayManager.uiSprite.getModule(SignAwardDict.SIGNAWARDDICT_SIGNAWARDVIEW))
			   {
				   DisplayManager.uiSprite.closeModule(SignAwardDict.SIGNAWARDDICT_SIGNAWARDVIEW, true);
			   }
			   
				var awards:Array = new Array();
				var i:int = 0;				
				if (e.target.data.result)
				{
					if (e.target.data.result.coin)
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_COIN, num:e.target.data.result.coin } ));
					}
					
					if (e.target.data.result.gem)
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_GEM, num:e.target.data.result.gem } ));
					}
				}
				
				if (e.target.data.addItem)
				{
					for (i = 0; i < e.target.data.addItem.length; i++) 
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.ITEM, id:e.target.data.addItem[i].i_id, num:e.target.data.addItem[i].num } ));
					}
				}
				
				if (e.target.data.addDecorBag)
				{
					for (i = 0; i < e.target.data.addDecorBag.length; i++) 
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.DECOR, id:e.target.data.addDecorBag[i].d_id, num:e.target.data.addDecorBag[i].num } ));
					}
				}

				var awardwin:AwardResultView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_AWARD_RESULT, ModuleDict.MODULE_AWARD_RESULT_CLASS,true) as AwardResultView;
				awardwin.setData( { name:"连续登入奖励", awards:awards } );
				   
			   EventManager.getInstance().dispatchEvent(new SignAwardEvent(SignAwardEvent.COMPLETE));
			

		}
		
		

		
	}

}