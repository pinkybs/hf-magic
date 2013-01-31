package 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.actModule.giftGetAct.commond.GiftGetActInitCommond;
	import happyfish.actModule.giftGetAct.commond.GiftGetActInitStaticCommond;
	import happyfish.actModule.giftGetAct.event.GiftGetActEvent;
	import happyfish.actModule.giftGetAct.GiftGetActDict;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happyfish.actModule.giftGetAct.model.vo.GiftDiaryVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftRequestVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftUserVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftVo;
	import happyfish.actModule.giftGetAct.view.giftGetAct.GiftGetActView;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.TestCommand;
	
	/**
	 * ...
	 * @author ZC
	 */
	
	 //其他项目请将ActModuleBase改成sprite
	public class GiftGetActMain extends ActModuleBase 
	{
		private var state:uint;
		private var giftgetbtn:GiftGetActBtn;
		
		public function GiftGetActMain():void 
		{
			//其他项目请注释
			EventManager.getInstance().addEventListener(GiftGetActEvent.CLOSE, fullClose);
		}
		
		//每个版本的init都不一样 请自己重新改写
		override public function init(actVo:ActVo, _type:uint = 1):void 
		{			
			
			super.init(DataManager.getInstance().getActByName("giftact"), 1);
			
            GiftDomain.getInstance().setGiftUserVo(DataManager.getInstance().getActByName("giftact"));		
			GiftDomain.getInstance().stage = stage;	
			
			var loopback:Boolean = false;
			GiftDomain.getInstance().setVar("loopbackboolean", loopback);
			EventManager.getInstance().addEventListener(SceneEvent.SCENE_DATA_COMPLETE, scene_complete);

			var giftGetActInitStaticCommond:GiftGetActInitStaticCommond = new GiftGetActInitStaticCommond();
			giftGetActInitStaticCommond.setData();
			giftGetActInitStaticCommond.addEventListener(Event.COMPLETE, initstaticcommondcomplete1);
			
			//游戏内部调用此模块
			EventManager.getInstance().addEventListener("giftActEventStart",giftgetbtnclick);
		}
		
		private function initstaticcommondcomplete1(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, initstaticcommondcomplete1);
             //TODO 将礼物的按钮放入游戏主界面上	------------------------------------------------------------		
			giftgetbtn = new GiftGetActBtn();
			DisplayManager.menuView.view.addChild(giftgetbtn);			
			
			giftgetbtn.addEventListener(MouseEvent.CLICK, giftgetbtnclick);		
			giftgetbtn.addEventListener(MouseEvent.MOUSE_OVER,giftgetbtnover);	
			giftgetbtn.addEventListener(MouseEvent.MOUSE_OUT, giftgetbtnout);	
			
			giftgetbtn.x = 255;
			giftgetbtn.y = 25;
			
			giftgetbtn.tips1.visible = false;
			
			if (GiftDomain.getInstance().getVar("IsNewGift"))
			{
				var giftuservo:GiftUserVo = GiftDomain.getInstance().getVar("giftUserVo");
				giftgetbtn.numbertips["num"].text = String(giftuservo.giftNum);
               EventManager.getInstance().addEventListener(GiftGetActEvent.CLOSE_NUMBERSHOW,closenumbershow);					
			}
			else
			{
				giftgetbtn.numbertips.visible = false;
			}
			
			init_complete();
		}
		
		private function scene_complete(e:SceneEvent):void 
		{
			if (DataManager.getInstance().isSelfScene)
			{
                giftgetbtn.visible = true;				
			}
			else
			{
				giftgetbtn.visible = false;
			}
		
		}
		
		
		private function giftgetbtnclick(e:Event):void 
		{
			giftgetbtn.mouseChildren = false;
			giftgetbtn.mouseEnabled = false;
			
			if (e is MouseEvent)
			{
				state = 0;
			}
			else
			{
				state = 3;
			}
			
			start();			
		}
		
		//其他项目请注释
		private function fullClose(e:GiftGetActEvent):void 
		{
			close();
		}
		
		//其他项目请注释
		override public function close():void 
		{
			super.close();
		}
		
		public function start():void
		{		
			var giftgetinitcommond:GiftGetActInitCommond = new GiftGetActInitCommond();
			giftgetinitcommond.setData();
			giftgetinitcommond.addEventListener(Event.COMPLETE, initcommondcomplete2);	
			
		}
		
		private function initcommondcomplete2(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, initcommondcomplete2);
			giftgetbtn.mouseChildren = true;
			giftgetbtn.mouseEnabled = true;
			var modlueVo:ModuleVo = new ModuleVo();
			modlueVo.name = GiftGetActDict.ACTDICT_GIFTGETACT;
			modlueVo.className = GiftGetActDict.ACTDICT_GIFTGETACT_CLASS;
			modlueVo.algin = "center";
			modlueVo.mvTime = 0.5;
			modlueVo.mvType = "fromCenter";
			modlueVo.single = false;

		    var giftgetactview:GiftGetActView = GiftDomain.getInstance().addModule(modlueVo) as GiftGetActView;
		    giftgetactview.setData(state);
			GiftDomain.getInstance().setBg(giftgetactview);					
		
		}
		
		private function closenumbershow(e:GiftGetActEvent):void 
		{
			giftgetbtn.numbertips.visible = false;			
		}
		
		private function giftgetbtnout(e:MouseEvent):void 
		{
			giftgetbtn.scaleX = 1.0;
			giftgetbtn.scaleY = 1.0;
			giftgetbtn.tips1.visible = false;	
			giftgetbtn.tips2.visible = true;
		}
		
		private function giftgetbtnover(e:MouseEvent):void 
		{
			giftgetbtn.tips1.visible = true;	
		    giftgetbtn.tips2.visible = false;
			giftgetbtn.scaleX = 1.1;
			giftgetbtn.scaleY = 1.1;			
		}

	}
	
}