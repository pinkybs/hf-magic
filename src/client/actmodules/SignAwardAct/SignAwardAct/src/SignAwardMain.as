package 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happymagic.actModule.signAward.Commond.SignAwardInitStaticCommand;
	import happymagic.actModule.signAward.SignAwardDict;
	import happymagic.actModule.signAward.View.event.SignAwardEvent;
	import happymagic.actModule.signAward.View.SignAwardView;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.TestCommand;
	import happymagic.model.vo.SignAwardVo;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class SignAwardMain extends ActModuleBase 
	{
		
		public function SignAwardMain():void 
		{
			EventManager.getInstance().addEventListener(SignAwardEvent.COMPLETE, complete);
		}
		
		private function complete(e:SignAwardEvent):void 
		{
			close();
		}
		
		override public function init(actVo:ActVo, _type:uint = 1):void 
		{
			super.init(DataManager.getInstance().getActByName("signAct"), 1);
			
			var signAwardInitStaticCommand:SignAwardInitStaticCommand = new SignAwardInitStaticCommand();
			signAwardInitStaticCommand.init();
			signAwardInitStaticCommand.addEventListener(Event.COMPLETE, signAwardInitStaticCommandComplete);
			
		}	
		
		private function signAwardInitStaticCommandComplete(e:Event):void 
		{
			//DataManager.getInstance().currentUser.signDay = 2;
			//DataManager.getInstance().currentUser.signAwardNumber = "1234567890";	
			
			var modlueVo:ModuleVo = new ModuleVo();
			modlueVo.name = SignAwardDict.SIGNAWARDDICT_SIGNAWARDVIEW;
			modlueVo.className = SignAwardDict.SIGNAWARDDICT_SIGNAWARDVIEW_ClASS;
			modlueVo.algin = "center";
			modlueVo.mvTime = 0.5;
			modlueVo.mvType = "fromCenter";
			modlueVo.single = true;
			modlueVo.y = -40;			
			
			var signawardview:SignAwardView = DisplayManager.uiSprite.addModuleByVo(modlueVo) as SignAwardView;
			signawardview.setInit();
			DisplayManager.uiSprite.setBg(signawardview);
			
			init_complete();
		}

		
	}
	
}