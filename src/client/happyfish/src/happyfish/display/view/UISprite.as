package happyfish.display.view 
{
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.events.EventDispatcher;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleStateType;
	/**
	 * ...
	 * @author Beck
	 */
	public class UISprite extends EventDispatcher implements IModule
	{
		protected var _view:MovieClip;
		private var _state:uint;
		private var _maskbg:DisplayObject;
		public var initStated:Boolean;
		public function UISprite() 
		{
			_state = ModuleStateType.HIDEING;
		}
		
		public function closeMe(del:Boolean = false):void {
			
			ModuleManager.getInstance().closeModule(name,del);
		}
		
		/* INTERFACE happyfish.manager.module.interfaces.IModule */
		
		public function init():void
		{
			initStated = true;
		}
		
		/* INTERFACE happyfish.manager.module.interfaces.IModule */
		
		public function set maskbg(value:DisplayObject):void
		{
			_maskbg = value;
		}
		
		public function get maskbg():DisplayObject
		{
			return _maskbg;
		}
		
		/* INTERFACE happyfish.manager.module.interfaces.IModule */
		
		public function get scaleX():Number
		{
			return view.scaleX;
		}
		
		public function set scaleX(value:Number):void
		{
			view.scaleX = value;
		}
		
		public function get scaleY():Number
		{
			return view.scaleY;
		}
		
		public function set scaleY(value:Number):void
		{
			view.scaleY = value;
		}
		
		/* INTERFACE happyfish.manager.module.interfaces.IModule */
		
		public function set x(value:Number):void
		{
			view.x=value;
		}
		
		public function set y(value:Number):void
		{
			view.y=value;
		}
		
		public function get x():Number
		{
			return view.x;
		}
		
		public function get y():Number
		{
			return view.y;
		}
		
		public function set name(str:String):void
		{
			view.name = str;
		}
		
		public function get name():String
		{
			return view.name;
		}
		
		public function get view():MovieClip
		{
			return this._view;
		}
		
		public function get state():uint { return _state; }
		
		public function set state(value:uint):void 
		{
			_state = value;
		}
		
		
	}

}