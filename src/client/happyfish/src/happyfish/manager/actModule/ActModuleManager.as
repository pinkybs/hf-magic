package happyfish.manager.actModule 
{
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import happyfish.events.ActModuleEvent;
	import happyfish.manager.actModule.display.ActMenuView;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActMenuType;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happyfish.model.SwfLoader;
	import happymagic.display.view.MenuView;
	import happymagic.display.view.ModuleDict;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	
	/**
	 * ...
	 * @author jj
	 */
	public class ActModuleManager 
	{
		
		public function ActModuleManager(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
				}
			}
			else
			{	
				throw new Error( "ActModuleManager"+"单例" );
			}
		}
		
		public function init(__container:DisplayObjectContainer):void {
			container = __container;
			
			//准备模块列表
			modules = new Object();
			backModules = new Array();
			
			//入口点击事件
			EventManager.getInstance().addEventListener(ActModuleEvent.ACTMENU_CLICK, actMenuClick);
			//活动模块要求关闭事件
			EventManager.getInstance().addEventListener(ActModuleEvent.ACT_REQUEST_CLOSE, closeActModule);
			
			EventManager.getInstance().addEventListener(SceneEvent.SCENE_COMPLETE, sceneComplete);
		}
		
		private function sceneComplete(e:SceneEvent):void 
		{
			acts = DataManager.getInstance().acts;
			//加载所有backModule
			loadBackModule();
		}
		
		private function actMenuClick(e:ActModuleEvent):void 
		{
			
		}
		
		private function loadBackModule():void 
		{
			
			backModuleLoadNum = 0;
			for (var i:int = 0; i < acts.length; i++) 
			{
				if ((acts[i] as ActVo).backModuleUrl) 
				{
					backModuleLoadNum++;
				}
			}
			
			if (backModuleLoadNum == 0)
			{
				backModule_load_complete();
			}
			
			for (var j:int = 0; j < acts.length; j++) 
			{
				if ((acts[j] as ActVo).backModuleUrl) 
				{
					
					var item:LoadingItem = SwfLoader.getInstance().load((acts[j] as ActVo).backModuleUrl);
					item.addEventListener(Event.COMPLETE, backModule_load_complete);
					//把模块先放入backModules列表
					backModules.push( { actVo:acts[j], item:item } );
				}
				
			}
		}
		
		private function backModule_load_complete(e:Event = null):void 
		{
			backModuleLoadNum--;
			if (backModuleLoadNum <= 0)
			{
				//全部加载完成了
				
				//设置所有menu
				initActMenu();
			}
		}
		
		/**
		 * 初始化所有活动入口按钮
		 */
		private function initActMenu():void 
		{
			var tmpbtn:ActMenuBtn;
			for (var i:int = 0; i < acts.length; i++) 
			{
				var item:ActVo = acts[i];
				if (item.menuUrl) 
				{
					tmpbtn = new ActMenuBtn(item);
					switch (item.menuType) 
					{
						case ActMenuType.ACT_MENU:
							(ModuleManager.getInstance().getModule(ModuleDict.MODULE_ACTMENU) as ActMenuView).add(tmpbtn);
						break;
						
						case ActMenuType.MAIN_MENU:
							(ModuleManager.getInstance().getModule(ModuleDict.MODULE_MAINMENU) as MenuView).addBtnLeftSelf(tmpbtn);
						break;
					}
				}
			}
			
			//init所有backModule
			initBackModule();
		}
		
		/**
		 * 初始化模块
		 */
		private function initBackModule():void 
		{
			if (backModules.length==0) 
			{
				//初始化backModule完成
				return;
			}
			var curModule:Object = backModules.shift();
			var item:ActModuleBase = curModule.item.content;
			var actvo:ActVo = curModule.actVo;
			//置入modules
			modules[ actvo.actName] = item;
			
			item.addEventListener(ActModuleEvent.ACTMODULE_INIT_COMPLETE, initBackModule_complete);
			//模块加入容器
			container.addChild(item);
			//开始模块
			item.init(actvo, ActModuleBase.TYPE_BACKMODULE);
			
			//for (var i:int = 0; i < backModules.length; i++) 
			//{
				//var item:ActModuleBase = backModules[i].item.content;
				//置入modules
				//modules[ backModules[i].actVo.actName] = item;
				//
				//item.addEventListener(ActModuleEvent.ACTMODULE_INIT_COMPLETE, initBackModule_complete);
				//模块加入容器
				//container.addChild(item);
				//开始模块
				//item.init(backModules[i].actVo, ActModuleBase.TYPE_BACKMODULE);
			//}
			//
			//backModules = [];
		}
		
		private function initBackModule_complete(e:ActModuleEvent):void 
		{
			e.target.removeEventListener(ActModuleEvent.ACTMODULE_INIT_COMPLETE, initBackModule_complete);
			initBackModule();
		}
		
		/**
		 * 加载显示模块swf
		 * @param	act
		 */
		public function addActModule(act:ActVo):LoadingItem {
			
			if (!modules[act.actName]) 
			{
				var loadingItem:LoadingItem = SwfLoader.getInstance().load(act.moduleUrl);
				modules[act.actName] = loadingItem;
				loadingItem.addEventListener(Event.COMPLETE, addActModule_complete);
				if (loadingItem.isLoaded) 
				{
					loadingItem.dispatchEvent(new Event(Event.COMPLETE));
				}
				
				return loadingItem;
			}
			return null;
		}
		
		private function addActModule_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, addActModule_complete);
			
			var loadingItem:LoadingItem = e.target as LoadingItem;
			var actModule:ActModuleBase = loadingItem.content;
			container.addChild(actModule);
			
			
			var actName:String = getActNameByModule(loadingItem);
			modules[actName] = actModule;
			var actvo:ActVo = DataManager.getInstance().getActByName(actName);
			actModule.init(actvo, ActModuleBase.TYPE_NORMAL);
			
			
		}
		
		/**
		 * 能过模块实例找到对应的活动名
		 * @param	module
		 * @return
		 */
		private function getActNameByModule(module:*):String {
			for (var name:String in modules) 
			{
				if (modules[name]==module) 
				{
					return name;
				}
			}
			return "";
		}
		
		/**
		 * 关闭卸载模块
		 * @param	e
		 */
		private function closeActModule(e:ActModuleEvent):void 
		{
			if (modules[e.act.actName]) 
			{
				(modules[e.act.actName] as Sprite).parent.removeChild(modules[e.act.actName]);
				modules[e.act.actName] = null;
			}
		}
		
		public static function getInstance():ActModuleManager
		{
			if (instance == null)
			{
				instance = new ActModuleManager( new Private() );
			}
			return instance;
		}
		
		//获取当前加载的SWF的实例
		public function  actcontainer():ActModuleBase
		{
			return _actcontainer;
		}
		
		private static var instance:ActModuleManager;
		private var container:DisplayObjectContainer;
		private var backModules:Array;
		private var modules:Object;//模块可视对象的列表
		private var backModuleLoadNum:Number;
		
		public var acts:Array;
		private var _actcontainer:ActModuleBase;
	}
	
}
class Private {}