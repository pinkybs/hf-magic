package 
{
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import flash.display.Loader;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.net.URLRequest;
	import happyfish.events.ModuleEvent;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.model.SwfLoader;
	import happymagic.actModule.command.HappyMagicDMCommand;
	import happymagic.actModule.event.HappyMagicDMEvent;
	import happymagic.actModule.HappyMagicDict;
	import happymagic.actModule.interfaces.HappyMagicDMDomain;
	import happymagic.actModule.model.view.HappyMagicDMView;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	
	/**
	 * ...
	 * @author zc
	 */
	public class HappyMagicDMMain extends ActModuleBase 
	{
		
		public function HappyMagicDMMain():void 
		{
           //EventManager.getInstance().addEventListener(HappyMagicDMEvent.COMPLETE, complete);
		   //addEventListener(Event.ADDED_TO_STAGE, added_to_stage);
		   DataManager.getInstance().setVar("HappyMagicDMState", true);
		   
		}
		
		//private function added_to_stage(e:Event):void 
		//{
			//removeEventListener(Event.ADDED_TO_STAGE, added_to_stage);			
		//}
		
		private function complete(e:HappyMagicDMEvent):void 
		{
			close();
		}
		
        override public function init(actVo:ActVo, _type:uint = 1):void
		{
            super.init(DataManager.getInstance().getActByName("HappyMagicDM"), 1);	
			
			ModuleManager.getInstance().addEventListener(ModuleEvent.MODULE_CLOSE, closecomplete);
			
            start();
		}
		
		private function closecomplete(e:ModuleEvent):void 
		{
			if (e.moduleName == "HappyMagicDM")
			{
				close();
			}
		}
		
		private function start():void
		{		
			var actvo:ActVo = DataManager.getInstance().getActByName("HappyMagicDM");
			
			//获取全局舞台
			HappyMagicDMDomain.getInstance().stage = this.stage;
			
			 if (DataManager.getInstance().getVar("HappyMagicDMState"))
			 {
				 DataManager.getInstance().setVar("HappyMagicDMState", false);
			 }
			 else
			 {
				 loadClassSwf_complete();
			 }
			
			var loader:LoadingItem = SwfLoader.getInstance().add(actvo.moduleData.dmUrl);
			loader.addEventListener(Event.COMPLETE, loadClassSwf_complete);		
			
		}
		
		private function loadClassSwf_complete(e:Event = null):void 
		{
			//var _view:MovieClip = e.target.loader.content as MovieClip;
			
			//DataManager.getInstance().setVar("happymagicMC", _view);
			
			var modlueVo:ModuleVo = new ModuleVo();
			modlueVo.name = HappyMagicDict.MODULE_HAPPYMAGICDM;
			modlueVo.className = HappyMagicDict.MODULE_HAPPYMAGICDM_CLASS;
			modlueVo.algin = "center";
			modlueVo.mvTime = 0.5;
			modlueVo.mvType = "fromCenter";
			modlueVo.single = true;
			modlueVo.y = 20;
			modlueVo.x = 90;
			
			var happyMagicView:HappyMagicDMView = HappyMagicDMDomain.getInstance().addModule(modlueVo) as HappyMagicDMView;
			happyMagicView.setData();
			DisplayManager.uiSprite.setBg(happyMagicView);			
			init_complete();
			
		}
		
		override public function close():void 
		{
			super.close();
		}		
	}
	
}