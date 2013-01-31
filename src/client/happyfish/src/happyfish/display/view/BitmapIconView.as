package happyfish.display.view 
{
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.geom.Matrix;
	import flash.geom.Rectangle;
	import happyfish.cacher.SwfClassCache;
	import happyfish.events.SwfClassCacheEvent;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class BitmapIconView extends Sprite 
	{
		//是否放大
		public var noBiger:Boolean;
		public var className:String;
		//是否需要立即开始播放
		private var container:BitmapIconView;
		private var autoCenter:Boolean;
		private var maxWidth:Number;
		private var maxHeight:Number;
		private var icon:Bitmap;
		private var rect:Rectangle;
		private var toFrame:*;
		
		public function BitmapIconView(_w:Number=0,_h:Number=0,_rect:Rectangle=null,_autoCenter:Boolean=false) 
		{
			mouseChildren = false;
			
			container = this;
			autoCenter = _autoCenter;
			maxWidth = _w;
			maxHeight = _h;
			if (_rect) 
			{
				rect = _rect;
			}else {
				rect=new Rectangle(0, 0, maxWidth, maxHeight);
			}
			
		}
		
		public function setClass(value:String,frame:*=null):void {
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
			
			tmpicon = new tmpclass() as MovieClip;
			
			if (tmpclass) 
			{
				
			}else {
				tmpicon = new MovieClip();
			}
			if (toFrame) {
				tmpicon.gotoAndStop(toFrame);
			}else {
				tmpicon.gotoAndStop(1);
			}
			
			setData(tmpicon);
		}
		
		public function setData(mc:Sprite):void {
			var rect:Rectangle=mc.getBounds(mc);
			var tmpbd:BitmapData = new BitmapData(rect.width, rect.height, true, 0xffffff);
			
			var m:Matrix= new Matrix();
			m.translate(-rect.x,-rect.y);
			tmpbd.draw(mc, m);
			
			var tmpbt:Bitmap = new Bitmap(tmpbd, "auto", true);
			setBody(tmpbt);
		}
		
		private function setBody(bitmapMovieMc:Bitmap):void 
		{
			icon = bitmapMovieMc;
			addChild(icon);
			
			layoutBody();
		}
		
		public function layoutBody():void {
			
			var iconrect:Rectangle = icon.getBounds(icon);
			var iconScale:Number = 1;
			
			if (!((maxWidth == maxHeight) && maxWidth==0)) 
			{
				var biger:Boolean;
				if (iconrect.width<maxWidth && iconrect.height<maxHeight) 
				{
					biger = true;
				}
				
				
				var wScale:Number = maxWidth / iconrect.width;
				var hScale:Number = maxHeight / iconrect.height;
				if (biger) 
				{
					iconScale = Math.min(wScale, hScale);
				}else {
					iconScale = Math.min(wScale, hScale);
				}
				iconScale = Number(iconScale.toFixed(2));
				
				if (iconScale>1 && noBiger) 
				{
					iconScale = 1;
				}else {
					icon.scaleX=
					icon.scaleY = iconScale;
				}
				
				
			}
			
			
			var tmprect:Rectangle = rect;
			
			x = tmprect.x + (tmprect.width / 2) - iconrect.width * iconScale / 2 -(iconrect.left - icon.x) * iconScale;
			
			y = tmprect.y + (tmprect.height / 2) - iconrect.height * iconScale / 2 - (iconrect.top - icon.y) * iconScale;
			
			dispatchEvent(new Event(Event.COMPLETE));
		}
	}

}