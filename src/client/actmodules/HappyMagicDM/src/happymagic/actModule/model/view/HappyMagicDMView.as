package happymagic.actModule.model.view 
{
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import flash.display.Loader;
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.net.navigateToURL;
	import flash.net.URLRequest;
	import flash.utils.getDefinitionByName;
	import happyfish.cacher.SwfClassCache;
	import happyfish.display.view.UISprite;
	import happyfish.events.SwfClassCacheEvent;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happymagic.actModule.event.HappyMagicDMEvent;
	import happymagic.actModule.manager.HappyMagicManager;
	import happymagic.actModule.model.vo.HappyMagicDMVo;
	import happymagic.display.view.magicBook.CompoundTotalView;
	import happymagic.display.view.ModuleDict;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.MixMagicVo;
	/**
	 * ...
	 * @author zc
	 */
	public class HappyMagicDMView extends UISprite
	{
		private var iview:MovieClip;
		private var allNum:uint;
		private var currentpageNum:uint;//当前页数
		
		public function HappyMagicDMView() 
		{
			
			var tmpclass:Class = SwfClassCache.getInstance().getClass("HappyMagicViewUi");
			
			_view = new tmpclass();
			iview = _view;
			
			iview.addEventListener(Event.ADDED_TO_STAGE, addToStage);
			iview.addEventListener(MouseEvent.CLICK, clickrun, true);
			allNum = iview["MovieMc"].totalFrames;
			
			if (allNum == 1)
			{
				iview["reducebtn"].visible = false;
				iview["addbtn"].visible = false;
			}
		}		
		
		private function addToStage(e:Event):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, addToStage);
			
		}
		
		private function movieMcClick(e:MouseEvent):void 
		{

		}
		
		private function clickrun(e:MouseEvent):void 
		{
			var url:URLRequest;	
            var compoundTotalView:CompoundTotalView;
			var mixmagicvo:MixMagicVo;
			
			switch(e.target.name)
			{	   
				case "addbtn":
				         add();
				   break;
				   
				case "reducebtn":
				         reduce();
				   break;
				   
				case "closebtn":
				         viewclose();
				   break;	
                default:	
				    var nameArr:Array = e.target.name.split("_");	
					
					switch(nameArr[0])
					{
					    case "showActModule":
					           var actvo:ActVo = DataManager.getInstance().getActByName(nameArr[1]);
					           var giftActVoloadingitem:LoadingItem = ActModuleManager.getInstance().addActModule(actvo);
							   viewclose();
						break;
						
						case "mix":
                              compoundTotalView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_COMPOUNDTOTAL, ModuleDict.MODULE_COMPOUNDTOTAL_CLASS, false, AlginType.CENTER, 0, 0) as CompoundTotalView;
			                  compoundTotalView.setData(nameArr[1], nameArr[2],nameArr[3]);					  
			                  DisplayManager.uiSprite.setBg(compoundTotalView);		
							  viewclose();
						break;
						
					    case "giftbtn":
						      EventManager.getInstance().dispatchEvent(new Event("giftActEventStart"));
							  viewclose();
						break;
						
						
					}
					
				break;
			}			
			
			
		}
		
		private function reduce():void 
		{
			currentpageNum--;
			if (currentpageNum == 0)
			{
				currentpageNum = allNum;
				var temp:uint = allNum - 1;
				iview.MovieMc.gotoAndStop(currentpageNum);				
			}
			else
			{
				iview.MovieMc.prevFrame();				
			}

			iview["currentpageNum"].text = String(currentpageNum);
			  
		}
		
		private function add():void 
		{
			currentpageNum++;
			if (currentpageNum > allNum)
			{
				currentpageNum = 1;
				iview.MovieMc.gotoAndStop(1);
			}
			else
			{
				iview.MovieMc.nextFrame();
			}

			  iview["currentpageNum"].text = String(currentpageNum);			
			
		}
		
		
        public function setData():void
		{
			  currentpageNum = 1;
			  
			  iview["currentpageNum"].text = String(currentpageNum);
			  
			  iview.MovieMc.stop();
			   
		}
		
		private function clear():void
		{
			
		}
		
		private function viewclose():void
		{
			 EventManager.getInstance().dispatchEvent(new HappyMagicDMEvent(HappyMagicDMEvent.COMPLETE));
			 closeMe(true);
		}
				
	}

}
