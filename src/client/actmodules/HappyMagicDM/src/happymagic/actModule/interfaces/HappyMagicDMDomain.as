package happymagic.actModule.interfaces 
{
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.DisplayObjectContainer;
	import flash.display.Sprite;
	import flash.display.Stage;
	import flash.events.EventDispatcher;
	import happyfish.display.ui.Tooltips;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.modules.gift.interfaces.IGiftUserVo;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author zc
	 */
	public class HappyMagicDMDomain 
	{
		private static var instance:HappyMagicDMDomain;
		private var _stage:Stage;
		public function HappyMagicDMDomain(access:Private) 
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
				throw new Error( "HappyMagicDMDomain"+"单例" );
			}	
		}

		 public static function getInstance():HappyMagicDMDomain
		{
			if (instance == null)
			{
				instance = new HappyMagicDMDomain( new Private() );
			}
			return instance;
		}
		
		//TODO 设置一个动态数据  name 名字 val 内容
		public function setVar(name:String,val:*):void {
			DataManager.getInstance().setVar(name, val);
		}
		
		//TODO 根据名字获取动态数据
		public function getVar(name:String):* {
			return DataManager.getInstance().getVar(name);
		}
		
		public function get stage():Stage 
		{
			return _stage;
		}
		
		public function set stage(value:Stage):void 
		{
			_stage = value;
		}
		
		public function addModule(module:ModuleVo):IModule
		{
			var temp:IModule = ModuleManager.getInstance().addModule(module)
			ModuleManager.getInstance().showModule(module.name);
			return temp;
		}
		
		//TODO 返回事件中心
		public function getEventManager():EventDispatcher
		{			
			return EventManager.getInstance();
		}		
		
		//TODO 根据名字获取接口地址
		public function getInterfaceUrl(name:String):String
		{
			return InterfaceURLManager.getInstance().getUrl(name);
		}	
		
		//设置背景变黑
		//target 窗口对象
		public function setBg(target:IModule):void {
			if (target) 
			{
				ModuleManager.getInstance().setModuleBg(target.name,createMaskBg());
			}
		}
			
	    public function createMaskBg():Sprite {
			var bd:BitmapData = new BitmapData(_stage.width, _stage.height, false, 0x000000);
			
			//bd.draw(stage);
			var bt:Bitmap = new Bitmap(bd);
			bt.alpha = .5;
			var mat:Array = [  1, 0, 0, 0, -50, 
							   0, 1, 0, 0, -50, 
							   0, 0, 1, 0, -50, 
							   0, 0, 0, 1, 0 ];
			var bg:Sprite = new Sprite();
			bg.addChild(bt);
			return bg;
		}	
			
	}

}
class Private {}