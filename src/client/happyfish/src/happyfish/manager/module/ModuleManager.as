package happyfish.manager.module
{
	import com.friendsofed.isometric.Point3D;
	import com.greensock.easing.Back;
	import com.greensock.easing.Circ;
	import com.greensock.TweenMax;
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.display.Stage;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.media.Sound;
	import happyfish.display.view.UISprite;
	import happyfish.events.ModuleEvent;
	import happyfish.manager.module.interfaces.IClassManager;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.manager.module.interfaces.ISoundManager;
	import happyfish.manager.module.vo.ModuleStateType;
	import happyfish.manager.module.vo.ModuleVo;
	import happymagic.manager.DisplayManager;

	
	/**
	 * ...
	 * @author jj
	 */
	public class ModuleManager extends EventDispatcher
	{
		private static var instance:ModuleManager;
		private var classManager:IClassManager;
		private var moduleSettings:Object;
		public var modules:Object;
		private var openSound:Sound;
		private var closeSound:Sound;
		private var soundManager:ISoundManager;
		private var waitModules:Array;
		private var curSingleModule:IModule;
		private var containers:Array;
		
		public function ModuleManager(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
					modules = new Object();
					moduleSettings = new Object();
					waitModules = new Array();
					
				}
			}
			else
			{	
				throw new Error( "ModuleManager"+"单例" );
			}
		}
		
		public function initManager(_containers:Array,_classManager:IClassManager,_soundManager:ISoundManager):void {
			containers = _containers;
			classManager = _classManager;
			soundManager = _soundManager;
			
			if (containers[0].stage) 
			{
				containers[0].stage.addEventListener(Event.RESIZE, resize_fun);
			}else {
				(containers[0] as DisplayObject).addEventListener(Event.ADDED_TO_STAGE, init_stageFun);
			}
		}
		
		private function init_stageFun(e:Event):void 
		{
			e.target.removeEventListener(Event.ADDED_TO_STAGE, init_stageFun);
			
			e.target.stage.addEventListener(Event.RESIZE, resize_fun);
		}
		
		public function setSound(_openSound:Sound, _closeSound:Sound):void {
			openSound = _openSound;
			closeSound = _closeSound;
		}
		
		public function setData(moduleArr:Array):void {
			var tmp:ModuleVo;
			var tmpClass:Class;
			for (var i:int = 0; i < moduleArr.length; i++) 
			{
				tmp = new ModuleVo().setValue(moduleArr[i]);
				addModule(tmp);
				showModule(tmp.name);
			}
		}
		
		public function addModule(value:ModuleVo):IModule {
			if (!modules[value.name]) 
			{
				var tmpclass:Class = classManager.getClass(value.className);
				var tmpmd:IModule= new tmpclass() as IModule;
				tmpmd.name = value.name;
				modules[value.name] = tmpmd;
				moduleSettings[value.name] = value;
				//container.addChild(tmpmd);
				
				return tmpmd;
			}
			return modules[value.name] as IModule;
		}
		
		public function getModule(mname:String):IModule {
			if (modules[mname]) 
			{
				return modules[mname];
			}else {
				return null;
			}
			
		}
		
		public function showModule(moduleName:String):IModule {
			//当前是否有显示单独窗口
			if (!moduleSettings[moduleName]) 
			{
				return null;
			}
			if (curSingleModule && moduleSettings[moduleName].single) 
			{
				//保存进队列
				waitModules.push(moduleName);
				
				return null;
			}
			if (modules[moduleName]) 
			{
				var tmpmd:IModule = modules[moduleName] as IModule;	
				var targetContainer:DisplayObjectContainer=containers[moduleSettings[tmpmd.name].layer];
				targetContainer.addChild(tmpmd.view);
				
				//tmpmd.view.scaleX = 0;
				//tmpmd.view.scaleY = 0;
				//tmpmd.view.x = 0;
				//tmpmd.view.y = 0;
				if ((modules[moduleName] as IModule).state!=ModuleStateType.SHOWING) 
				{
					
					
					moudleShowMv(moduleSettings[moduleName]);
					if (tmpmd.maskbg) 
					{
						if (!tmpmd.maskbg.parent) 
						{
							setModuleBg(tmpmd.name, tmpmd.maskbg);
						}
					}
					
					if (moduleSettings[moduleName].single) 
					{
						curSingleModule = tmpmd;
					}
					
					
					return tmpmd;
				}
			}
			return null;
		}
		
		public function setModuleBg(moduleName:String,bg:DisplayObject):void {
			if (modules[moduleName]) 
			{
				if ((modules[moduleName] as IModule).state==ModuleStateType.SHOWING) 
				{
					if ((modules[moduleName] as IModule).maskbg) 
					{
						var tmpmaskbg:DisplayObject = (modules[moduleName] as IModule).maskbg;
						if (tmpmaskbg.parent) 
						{
							//tmpmaskbg.parent.removeChild(tmpmaskbg);
							//tmpmaskbg = null;
							return;
						}
					}
					(modules[moduleName] as IModule).maskbg = bg;
					var targetContainer:DisplayObjectContainer = containers[moduleSettings[moduleName].layer];
					targetContainer.addChildAt(bg,targetContainer.getChildIndex((modules[moduleName] as IModule).view));
				}
			}
		}
		
		public function closeModuleBg(moduleName:String):void {
			var tmpmd:IModule = modules[moduleName] as IModule;
				
				if (tmpmd.maskbg) 
				{
					if (tmpmd.maskbg.parent) 
					{
						tmpmd.view.parent.removeChild(tmpmd.maskbg);
					}
					tmpmd.maskbg = null;
				}
		}
		
		private function moudleShowMv(module:ModuleVo):void {
			var tmpmd:IModule = modules[module.name] as IModule;
			
			
			TweenMax.killTweensOf(tmpmd.view);
			
			tmpmd.state = ModuleStateType.SHOWING;
			tmpmd.view.visible = true;
			var toP:Point = getModuleXY(module.name,module.algin, module.fx, module.fy);
			switch (module.mvType) 
			{
				case ModuleMvType.NONE:
				alginModule(module.name, false);
				moduleShowComplete(tmpmd);
				break;
				
				case ModuleMvType.CNETER:
				alginModule(module.name, false);
				tmpmd.view.scaleX=
				tmpmd.view.scaleY = 0;
				TweenMax.to(tmpmd.view, module.mvTime, { scaleX:1, scaleY:1, ease:Back.easeOut,onComplete:moduleShowComplete,onCompleteParams:[tmpmd]} );
				break;
				
				case ModuleMvType.FROM_BOTTOM:
				alginModule(module.name,false);
				TweenMax.from(tmpmd.view, module.mvTime, { y:toP.y,onComplete:moduleShowComplete,onCompleteParams:[tmpmd] } );
				break;
				
				case ModuleMvType.FROM_TOP:
				alginModule(module.name,false);
				TweenMax.from(tmpmd.view, module.mvTime, { y:toP.y,onComplete:moduleShowComplete,onCompleteParams:[tmpmd] } );
				break;
				
				case ModuleMvType.FROM_LEFT:
				alginModule(module.name,false);
				TweenMax.from(tmpmd.view, module.mvTime, { x:toP.x,onComplete:moduleShowComplete,onCompleteParams:[tmpmd] } );
				break;
				
				case ModuleMvType.FROM_RIGHT:
				alginModule(module.name,false);
				TweenMax.from(tmpmd.view, module.mvTime, { x:toP.x,onComplete:moduleShowComplete,onCompleteParams:[tmpmd] } );
				break;
			}
			if (openSound) 
			{
				soundManager.playSound(openSound);
			}
			
		}
		
		/**
		 * 模块出现动画完成后
		 */
		private function moduleShowComplete(tmpModule:IModule):void 
		{
			tmpModule.init();
			
			//广播有模块打开
			var tmpe:ModuleEvent = new ModuleEvent(ModuleEvent.MODULE_OPEN);
			tmpe.moduleName = tmpModule.name;
			dispatchEvent(tmpe);
		}
		
		public function closeModule(moduleName:String,del:Boolean=false):void {
			if (modules[moduleName]) 
			{
				var tmpmd:IModule = modules[moduleName] as IModule;
				
				tmpmd.state = ModuleStateType.HIDEING;
				
				closeModuleBg(moduleName);
				
				var moduleVo:ModuleVo = moduleSettings[moduleName];
				var toP:Point = getModuleXY(moduleVo.name,moduleVo.algin, moduleVo.fx, moduleVo.fy);
				switch (moduleVo.mvType) 
				{
					case ModuleMvType.NONE:
					closeModule_complete(moduleName, del);
					
					break;
					
					case ModuleMvType.CNETER:
					TweenMax.to(tmpmd.view, moduleVo.mvTime, { scaleX:0, scaleY:0,onComplete:closeModule_complete,onCompleteParams:[moduleName,del], ease:Circ.easeOut } );
					break;
					
					case ModuleMvType.FROM_BOTTOM:
					TweenMax.to(tmpmd.view, moduleVo.mvTime, { y:toP.y, onComplete:closeModule_complete, onCompleteParams:[moduleName, del] } );
					break;
					case ModuleMvType.FROM_TOP:
					TweenMax.to(tmpmd.view, moduleVo.mvTime, { y:toP.y, onComplete:closeModule_complete, onCompleteParams:[moduleName, del] } );
					break;
					
					case ModuleMvType.FROM_LEFT:
					TweenMax.to(tmpmd.view, moduleVo.mvTime, { x:toP.x, onComplete:closeModule_complete, onCompleteParams:[moduleName, del] } );
					break;
					case ModuleMvType.FROM_RIGHT:
					TweenMax.to(tmpmd.view, moduleVo.mvTime, { x:toP.x, onComplete:closeModule_complete, onCompleteParams:[moduleName, del] } );
					break;
				}
				
				
				if (openSound) 
				{
					soundManager.playSound(closeSound);
				}
			}
		}
		
		private function closeModule_complete(moduleName:String,del:Boolean):void
		{
			
			var tmpmd:IModule = modules[moduleName] as IModule;
			if (tmpmd) 
			{
				//tmpmd.state = ModuleStateType.HIDEING;
				
				tmpmd.view.visible = false;
				tmpmd.scaleX=
				tmpmd.scaleY = 1;
				
				//如果关闭的是当前单独窗口
				if (curSingleModule==tmpmd) 
				{
					//显示下一个等待窗口
					curSingleModule = null;
					showModule(waitModules.shift());
				}
				//清除等待显示单独窗口
				clearWaitModule(tmpmd);
				
				if (del) 
				{
					tmpmd.view.parent.removeChild(tmpmd.view);
					modules[moduleName] = null;
					moduleSettings[moduleName] = null;
				}
			}
			
			
			//广播有模块关闭
			var tmpe:ModuleEvent = new ModuleEvent(ModuleEvent.MODULE_CLOSE);
			tmpe.moduleName = moduleName;
			dispatchEvent(tmpe);
			
			if (tmpmd) 
			{
				tmpmd["dispatchEvent"](new Event(Event.CLOSE));
			}
		}
		
		private function clearWaitModule(tmpmd:IModule):void 
		{
			for (var i:int = 0; i < waitModules.length; i++) 
			{
				var item:IModule = getModule(waitModules[i]);
				if (item.name==tmpmd.name) 
				{
					waitModules.splice(i, 1);
					return;
				}
			}
		}
		
		private function resize_fun(e:Event=null):void 
		{
			for (var name:String in modules) 
			{
				if (modules[name]) 
				{
					alginModule(name);
					var moduleName:String = name;
					if (modules[moduleName].maskbg) 
					{
						var tmpbg:DisplayObject = modules[moduleName].maskbg as DisplayObject;
						if (tmpbg.parent) 
						{
							tmpbg.parent.removeChild(tmpbg);
						}
						tmpbg = DisplayManager.uiSprite.createMaskBg();
						modules[moduleName].maskbg = tmpbg;
						if ((modules[moduleName] as IModule).state==ModuleStateType.SHOWING) 
						{
							var targetContainer:DisplayObjectContainer = containers[moduleSettings[moduleName].layer];
							targetContainer.addChildAt(tmpbg,targetContainer.getChildIndex(modules[moduleName].view));
						}
						
					}
				}
			}
		}
		
		private function getModuleXY(moduleName:String,type:String, tx:Number, ty:Number):Point
		{
			var sw:Number = containers[0].stage.stageWidth;
			var sh:Number = containers[0].stage.stageHeight;
			
			var mrect:Rectangle;
			var mSelfRect:Rectangle;
			var tmpmodule:Sprite;
			var setting:Object;
			
			tmpmodule = modules[moduleName].view;
			setting = moduleSettings[moduleName];
			mrect = tmpmodule.getBounds(containers[0]);
			mSelfRect = tmpmodule.getBounds(tmpmodule);
			
			var toX:Number;
			var toY:Number;
			//设置Y
			if (type==AlginType.TC || type==AlginType.TR || type==AlginType.TL) 
			{
				toY = ty;
			}else if (type==AlginType.Cl || type==AlginType.CENTER || type==AlginType.CR) {
				toY =  sh / 2 - mrect.height / 2 - mSelfRect.y + ty;
			}else if (type==AlginType.BC || type==AlginType.BL || type==AlginType.BR) {
				toY = sh  - ty;
			}
			
			//设置x
			if (type==AlginType.TL || type==AlginType.Cl || type==AlginType.BL) 
			{
				toX = tx;
			}else if (type==AlginType.TC || type==AlginType.CENTER || type==AlginType.BC) {
				toX = sw / 2 - mrect.width / 2 - mSelfRect.x+tx;
			}else if (type==AlginType.TR || type==AlginType.CR || type==AlginType.BR) {
				toX = sw-tx;
			}
			
			return new Point(toX, toY);
		}
		
		private function alginModule(moduleName:String, tween:Boolean = true):void {
			tmpmodule = modules[moduleName].view;
			setting = moduleSettings[moduleName];
			
			var tmpscale:Number = tmpmodule.scaleX;
			
			tmpmodule.scaleX=
			tmpmodule.scaleY = 1;
			
			var sw:Number = containers[0].stage.stageWidth;
			var sh:Number = containers[0].stage.stageHeight;
			
			var tmpmodule:Sprite;
			var setting:Object;
			var mrect:Rectangle;
			var mSelfRect:Rectangle;
			var toX:int;
			var toY:int;
			
			
			mrect = tmpmodule.getBounds(tmpmodule.parent);
			mSelfRect = tmpmodule.getBounds(tmpmodule);
			
			//设置Y
			if (setting.algin==AlginType.TC || setting.algin==AlginType.TR || setting.algin==AlginType.TL) 
			{
				toY = setting.y;
			}else if (setting.algin==AlginType.Cl || setting.algin==AlginType.CENTER || setting.algin==AlginType.CR) {
				toY = sh / 2 - mrect.height / 2 - mSelfRect.y + setting.y;
			}else if (setting.algin==AlginType.BC || setting.algin==AlginType.BL || setting.algin==AlginType.BR) {
				toY = sh  - setting.y;
			}
			
			//设置x
			if (setting.algin==AlginType.TL || setting.algin==AlginType.Cl || setting.algin==AlginType.BL) 
			{
				toX = setting.x;
			}else if (setting.algin==AlginType.TC || setting.algin==AlginType.CENTER || setting.algin==AlginType.BC) {
				toX = sw / 2 - mrect.width / 2 - mSelfRect.x+setting.x;
			}else if (setting.algin==AlginType.TR || setting.algin==AlginType.CR || setting.algin==AlginType.BR) {
				toX = sw-setting.x;
			}
			
			tmpmodule.scaleX=
			tmpmodule.scaleY = tmpscale;
			
			if (tween) 
			{
				TweenMax.to(tmpmodule, .5, { x:toX, y:toY } );
			}else {
				tmpmodule.x = toX;
				tmpmodule.y = toY;
			}
		}
		
		public static function getInstance():ModuleManager
		{
			if (instance == null)
			{
				instance = new ModuleManager( new Private() );
			}
			return instance;
		}
		
	}
	
}
class Private {}