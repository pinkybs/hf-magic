package happymagic.actModule.signAward.View 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.external.ExternalInterface;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.utils.display.BtnStateControl;
	import happymagic.actModule.signAward.Commond.SignAwardIsFanCommand;
	import happymagic.actModule.signAward.SignAwardDict;
	import happymagic.actModule.signAward.View.event.SignAwardEvent;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.TestCommand;
	import happymagic.model.vo.SignAwardVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class SignAwardView extends UISprite
	{
		private var iview:SignAwardUi;
		private var signAwardStatic:Array;//静态数据以及对应的列表
		private var signAwardStaticList:SignAwardListView;
		private var signAwardTrends:Array;//动态数据以及对应的列表
		private var signAwardTrendsList:SignAwardListView;
		
		public function SignAwardView() 
		{
			_view = new SignAwardUi();
			iview = _view as SignAwardUi;
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			iview.lightstar.stop();
			signAwardStaticList = new SignAwardListView(new signawardlist(), iview);
			signAwardStaticList.init(600, 180, 112, 175, 0, 0);
			signAwardStaticList.x = -290;
			signAwardStaticList.y = -70;
			iview.addChild(iview.lightstar);		
		}
		
		//初始化
		public function setInit():void
		{
			iview.day.text = String(DataManager.getInstance().currentUser.signDay);
			if (!DataManager.getInstance().currentUser.signDay)
			{
				BtnStateControl.setBtnState(iview.lightstar, false);			
			}

		    signAwardStatic = DataManager.getInstance().signAwardClass;
			
			var strnumber:String
			
			if (DataManager.getInstance().currentUser.signDay<6)
			{
			    strnumber = "_" + DataManager.getInstance().currentUser.signDay;				
			}
			else
			{
			    strnumber = "_" + 5;
			}

			
			if (DataManager.getInstance().currentUser.isfans)
			{
				iview.becomefans.visible = false;
			}
			else
			{
				iview.becomefans.visible = true;				
			}
			
			iview.lightstar.gotoAndStop(strnumber);
			iview.lightstar.gotoAndPlay(strnumber);
			
			iview.awardnumber.text = DataManager.getInstance().currentUser.signAwardNumber;	
			
			signAwardStaticList.setData(signAwardStatic);
			
		}
		
		private function clickrun(e:MouseEvent):void
		{
			switch(e.target.name)
			{
				case "closeBtn":
				closeMe(true);
				EventManager.getInstance().dispatchEvent(new SignAwardEvent(SignAwardEvent.COMPLETE));
				break
				
				case "becomefans":
			          iview.removeEventListener(MouseEvent.CLICK, clickrun);
					  if (DataManager.getInstance().currentUser.isfans)
					  {
                           EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("signfans"));						  
					  }
					  else
					  {
						  ExternalInterface.call("goFans");
					  	  //var signAwardIsFanCommand:SignAwardIsFanCommand = new SignAwardIsFanCommand();
					  	  //signAwardIsFanCommand.init();
					  	  //signAwardIsFanCommand.addEventListener(Event.COMPLETE, signAwardIsFanCommandComplete);	
			              iview.addEventListener(MouseEvent.CLICK, clickrun);						  
					  }

				break;
			}
		}
		
		private function signAwardIsFanCommandComplete(e:Event):void 
		{
			//e.target.removeEventListener(Event.COMPLETE, signAwardIsFanCommandComplete);
			//iview.addEventListener(MouseEvent.CLICK, clickrun);
			//
			//DataManager.getInstance().currentUser.isfans = true;
			//
			//var modlueVo:ModuleVo = new ModuleVo();
			//modlueVo.name = SignAwardDict.SIGNAWARDFANS_VIEW;
			//modlueVo.className = SignAwardDict.SIGNAWARDFANS_VIEW_CLASS;
			//modlueVo.algin = "center";
			//modlueVo.mvTime = 0.5;
			//modlueVo.mvType = "fromCenter";
			//modlueVo.single = true;
			//modlueVo.y = -40;			
			//
			//var signAwardFanView:SignAwardFanView = DisplayManager.uiSprite.addModuleByVo(modlueVo) as SignAwardFanView;			
		}

	}

}