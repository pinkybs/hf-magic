package happyfish.manager.mouse 
{
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.display.SimpleButton;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.ui.Mouse;
	import flash.utils.Dictionary;
	
	/**
	 * ...
	 * @author jj
	 */
	public class MouseManager 
	{
		public static const STATE_DEFAULT:String = "default";
		public static const STATE_LIUCHEN:String = "liuchen";
		public static const STATE_OBJECT_OVER:String = "objectOver";
		public static const STATE_OBJECT_DOWN:String = "objectDown";
		
		private static var instance:MouseManager;
		private var container:DisplayObjectContainer;
		//手型字典
		private var iconDict:Dictionary=new Dictionary();
		/**
		 * 注册目标字典
		 * key为目标对象:DisplayObject
		 * 值为数组,第一项为over手型,第二项为down手型
		 */
		private var registIcons:Dictionary=new Dictionary();
		//当前手型
		private var currentIcon:Sprite;
		
		private var currentObject:DisplayObject;
		
		private var defaultIcon:Sprite;
		private var tmpIcon:Sprite;
		private var objectRegistIcon:Sprite;
		private var liuchenIcon:Sprite;
		
		private var state:String;
		
		//当前是否在跟随
		private var following:Boolean;
		private var callBack:Function;
		private var callBackParms:Array;
		private var _overFlag:Boolean;
		private var _downFlag:Boolean;
		//当前TMP手型的优先级
		private var curTmpIconPriority:int;
		
		public function MouseManager(access:Private) 
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
				throw new Error( "MouseManager"+"单例" );
			}
		}
		
		/**
		 * 设置manager
		 * @param	_container	鼠标手型容器
		 */
		public function initManager(_container:DisplayObjectContainer):void {
			container = _container;
			//侦听鼠标over事件
			container.stage.addEventListener(MouseEvent.MOUSE_OVER, objectOverEvent,true);
			container.stage.addEventListener(MouseEvent.MOUSE_DOWN, objectDownEvent,true);
			container.stage.addEventListener(MouseEvent.MOUSE_OUT, objectOutEvent,true);
			container.stage.addEventListener(MouseEvent.MOUSE_UP, objectUpEvent, true);
			container.stage.addEventListener(MouseEvent.MOUSE_OUT, stageOut);
			
			
		}
		
		private function stageOut(e:MouseEvent):void 
		{
			hideMouse();
		}
		
		private function hideMouse():void
		{
			if (currentIcon) currentIcon.visible = false;
			//container.stage.addEventListener(MouseEvent.MOUSE_OVER, stageIn);
		}
		
		private function stageIn(e:MouseEvent=null):void 
		{
			if (currentIcon) currentIcon.visible = true;
		}
		
		public function addMouseIcon(key:String,value:Sprite):void {
			iconDict[key] = value;
		}
		
		public function getMouseIcon(key:String):Sprite {
			return iconDict[key] as Sprite;
		}
		
		
		public function set defaultMouseIcon(icon:Sprite):void {
			defaultIcon = icon;
		}
		
		public function get overFlag():Boolean { return _overFlag; }
		
		public function set overFlag(value:Boolean):void 
		{
			_overFlag = value;
			if (!currentIcon) 
			{
				return;
			}
			
			if (currentIcon is MovieClip) 
			{
				if (_overFlag) 
				{
					(currentIcon as MovieClip).gotoAndStop("over");
				}else {
					(currentIcon as MovieClip).gotoAndStop(1);
				}
			}
		}
		
		public function get downFlag():Boolean { return _downFlag; }
		
		public function set downFlag(value:Boolean):void 
		{
			_downFlag = value;
		}
		
		/**
		 * 设置一个临时手型
		 * @param	icon
		 * @param	_priority	优先级
		 */
			public function setTmpIcon(icon:Sprite, _priority:int = 0):void {
			if (curTmpIconPriority<_priority) 
			{
				curTmpIconPriority = _priority;
				tmpIcon = icon;
				setIcon();
			}
		}
		
		public function clearTmpIcon(_priority:int = 0):void {
			if (curTmpIconPriority<=_priority) 
			{
				setTmpIcon(null, _priority);
				curTmpIconPriority = 0;
			}
		}
		
		/**
		 * 设置一个流程手型
		 * @param	icon
		 */
		public function setLiuChenIcon(icon:Sprite,_callBack:Function=null,_callBackParms:Array=null):void {
			if (icon) 
			{
				liuchenIcon = icon;
				callBack = _callBack;
				callBackParms = _callBackParms;
				state = STATE_LIUCHEN;
				startLiuchen();
			}
			setIcon();
		}
		
		/**
		 * 注册目标对象
		 * @param	object	
		 * @param	over
		 * @param	down
		 */
		public function registObjectMouseIcon(object:DisplayObject,over:Sprite,down:Sprite=null):void {
			registIcons[object] = [over, down];
			
			object.addEventListener(Event.REMOVED_FROM_STAGE, removeRegistObject);
			
		}
		
		private function removeRegistObject(e:Event):void 
		{
			e.target.removeEventListener(Event.REMOVED_FROM_STAGE, removeRegistObject);
			
			delete registIcons[e.target];
		}
		
		//*******鼠标事件***************
		
		private function objectOverEvent(e:MouseEvent):void 
		{
			stageIn();
			if (e.target is SimpleButton) 
			{
				overFlag = e.target.mouseEnabled;
			}else if(e.target is Sprite){
				if (e.target.buttonMode) 
				{
					overFlag = e.target.mouseEnabled;
				}else {
					overFlag = false;
				}
			}else {
				overFlag = false;
			}
			
			if (registIcons[e.target]) 
			{
				currentObject = e.target as DisplayObject;
				state = STATE_OBJECT_OVER;
				setIcon();
			}
		}
		
		private function objectUpEvent(e:MouseEvent):void 
		{
			if (registIcons[e.target]) 
			{
				currentObject = e.target as DisplayObject;
				state = STATE_OBJECT_OVER;
				setIcon();
			}
		}
		
		private function objectOutEvent(e:MouseEvent):void 
		{
			if (registIcons[e.target]) 
			{
				currentObject = null;
				state = STATE_DEFAULT;
				setIcon();
			}
		}
		
		private function objectDownEvent(e:MouseEvent):void 
		{
			if (registIcons[e.target]) 
			{
				currentObject = e.target as DisplayObject;
				state = STATE_OBJECT_DOWN;
				setIcon();
			}
		}
		
		/**
		 * 立即更换当前鼠标手型
		 * @param	icon
		 */
		public function setIcon():void {
			var icon:Sprite = checkIcon();
			
			if (icon) 
			{
				if (currentIcon) 
				{
					container.removeChild(currentIcon);
				}
				currentIcon = icon;
				currentIcon.mouseChildren=
				currentIcon.mouseEnabled = false;
				container.addChild(currentIcon);
				Mouse.hide();
				if (!following) 
				{
					startIconFollowMouse();
				}
			}else {
				//清除手型
				if (currentIcon) 
				{
					container.removeChild(currentIcon);
				}
				
				currentIcon = null;
				Mouse.show();
				stopFollow();
			}
		}
		
		/*public function setMouseIconByHard():void {
			// Create a MouseCursorData object
			var cursorData:MouseCursorData = new MouseCursorData();
			// Specify the hotspot
			cursorData.hotSpot = new Point(15,15);
			// Pass the cursor bitmap to a BitmapData Vector
			var bitmapDatas:Vector.<BitmapData> = new Vector.<BitmapData>(1, true);
			// Create the bitmap cursor 
			// The bitmap must be 32x32 pixels or smaller, due to an OS limitation
			var bitmap:Bitmap = new zoomCursor();
			// Pass the value to the bitmapDatas vector
			bitmapDatas[0] = bitmap.bitmapData;
			// Assign the bitmap to the MouseCursor object
			cursorData.data = bitmapDatas;
			// Register the MouseCursorData to the Mouse object with an alias
			Mouse.registerCursor("myCursor", cursorData);
			// When needed for display, pass the alias to the existing cursor property
			Mouse.cursor = "myCursor";
		}*/
		
		/**
		 * 获得当前优先级最高的鼠标
		 * @return
		 */
		private function checkIcon():Sprite
		{
			if (tmpIcon) 
			{
				return tmpIcon;
			}
			if (currentObject) 
			{
				switch (state) 
				{
					case STATE_OBJECT_DOWN:
					if (registIcons[currentObject][1]) 
					{
						return registIcons[currentObject][1];
					}else {
						return null;
					}
					
					break;
					
					case STATE_OBJECT_OVER:
					
					return registIcons[currentObject][0];
					
					break;
				}
				
			}
			
			if (liuchenIcon) 
			{
				return liuchenIcon;
			}
			if (defaultIcon) 
			{
				return defaultIcon;
			}
			return null;
		}
		
		
		
		/**
		 * 开始一次点击流程
		 */
		private function startLiuchen():void {
			container.stage.addEventListener(MouseEvent.MOUSE_UP, liuchenMouseUpEvent, true);
		}
		
		private function liuchenMouseUpEvent(e:MouseEvent):void 
		{
			e.target.removeEventListener(MouseEvent.MOUSE_UP, liuchenMouseUpEvent);
			if (callBack!=null) 
			{
				callBack.apply(null,callBackParms);
			}
			liuchenIcon = null;
			callBack = null;
			callBackParms = null;
			state = STATE_DEFAULT;
			setIcon();
		}
		
		/**
		 * 鼠标手型开始跟随鼠标
		 */
		private function startIconFollowMouse():void {
			if (currentIcon && !following) 
			{
				following = true;
				container.addEventListener(Event.ENTER_FRAME, iconFollowEvent);
			}
		}
		
		/**
		 * 手型对齐事件
		 * 逐帧把手型与鼠标所在位置对齐
		 * @param	e
		 */
		private function iconFollowEvent(e:Event):void 
		{
			if (!currentIcon) 
			{
				return;
			}
			
			currentIcon.x = container.mouseX;
			currentIcon.y = container.mouseY;
			
		}
		
		private function stopFollow():void {
			if (following) 
			{
				following = false;
				container.removeEventListener(Event.ENTER_FRAME, iconFollowEvent);
			}
			
		}
		
		public static function getInstance():MouseManager
		{
			if (instance == null)
			{
				instance = new MouseManager( new Private() );
			}
			return instance;
		}
		
	}
	
}
class Private {}