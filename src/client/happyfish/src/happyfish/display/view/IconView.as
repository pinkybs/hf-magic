package happyfish.display.view 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.geom.Rectangle;
	
	import happyfish.cacher.SwfClassCache;
	import happyfish.events.SwfClassCacheEvent;
	
	/**
	 * ...
	 * @author jj
	 */
	public class IconView extends Sprite
	{
		public var maxWidth:Number;
		public var maxHeight:Number;
		private var container:DisplayObjectContainer;
		private var className:String;
		private var rect:Rectangle;
		private var autoCenter:Boolean;
		private var icon2:Sprite;
		private var toFrame:*;
		public var icon:MovieClip;
		public var realIcon:MovieClip;
		public function IconView(_w:Number=0,_h:Number=0,_rect:Rectangle=null,_autoCenter:Boolean=false) 
		{
			container = this;
			autoCenter = _autoCenter;
			mouseChildren = false;
			maxWidth = _w;
			maxHeight = _h;
			if (_rect) 
			{
				rect = _rect;
			}else {
				rect=new Rectangle(0, 0, maxWidth, maxHeight);
			}
		}
		
		
		public function setData(value:String,frame:*=null):void {
			className = value;
			toFrame = frame;
			loadIcon();
		}
		
		private function loadIcon():void
		{
			
			if (!className) 
			{
				trace("class不存在");
				return;
			}
			SwfClassCache.getInstance().addEventListener(SwfClassCacheEvent.COMPLETE, classGeted);
			SwfClassCache.getInstance().loadClass(className);
			
		}
		
		private function classGeted(e:SwfClassCacheEvent):void 
		{
			if (e.className==className) 
			{
				SwfClassCache.getInstance().removeEventListener(SwfClassCacheEvent.COMPLETE,classGeted);
				
				createIcon();
			}
		}
		
		private function createIcon():void
		{
			var tmpclass:Class = SwfClassCache.getInstance().getClass(className);
			var tmpicon:MovieClip;
			if (tmpclass) 
			{
				tmpicon = new tmpclass() as MovieClip;
			}else {
				tmpicon = new MovieClip();
			}
			
			realIcon=tmpicon;
			if (toFrame) {
				gotoAndStop(toFrame);
			}else {
				gotoAndStop(1);
			}
			
			if (autoCenter) 
			{
				icon = new MovieClip();
				icon.addChild(tmpicon);
				
				
				var tmprect:Rectangle = tmpicon.getBounds(tmpicon);
				tmpicon.x = -tmprect.width/2-tmprect.x;
				tmpicon.y = -tmprect.height/2-tmprect.y;
			}else {
				icon = tmpicon;
			}
			
			
			container.addChild(icon);
			
			layoutBody();
			
			
		}
		
		public function gotoAndStop(frame:*):void {
			if (realIcon) {
				realIcon.gotoAndStop(frame);
			}
		}
		
		
		public function layoutBody():void {
			
			//if (autoCenter) 
			//{
				//var tmprect2:Rectangle = icon.getBounds(icon);
				//icon.x = tmprect2.x+tmprect2.width;
				//icon.y = tmprect2.y;
				//
				//return;
			//}
			
			if ((maxWidth == maxHeight) && maxWidth==0) 
			{
				return;
			}
			
			var iconrect:Rectangle = icon.getBounds(icon);
			
			var biger:Boolean;
			if (iconrect.width<maxWidth && iconrect.height<maxHeight) 
			{
				biger = true;
			}
			
			var iconScale:Number;
			var wScale:Number = maxWidth / iconrect.width;
			var hScale:Number = maxHeight / iconrect.height;
			if (biger) 
			{
				iconScale = Math.min(wScale, hScale);
			}else {
				iconScale = Math.min(wScale, hScale);
			}
			iconScale = Number(iconScale.toFixed(2));
			icon.scaleX=
			icon.scaleY = iconScale;
			
			var tmprect:Rectangle = rect;
			
			x = tmprect.x + (tmprect.width / 2) - iconrect.width * iconScale / 2 -(iconrect.left - icon.x) * iconScale;
			
			y = tmprect.y + (tmprect.height / 2) - iconrect.height * iconScale / 2 - (iconrect.top - icon.y) * iconScale;
			
			dispatchEvent(new Event(Event.COMPLETE));
		}
		
	}

}