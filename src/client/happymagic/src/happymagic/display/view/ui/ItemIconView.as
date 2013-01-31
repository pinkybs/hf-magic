package happymagic.display.view.ui 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.geom.Rectangle;
	import happyfish.cacher.SwfClassCache;
	import happyfish.events.SwfClassCacheEvent;
	import happymagic.model.vo.ItemClassVo;
	/**
	 * ...
	 * @author jj
	 */
	public class ItemIconView
	{
		public var maxWidth:Number;
		public var maxHeight:Number;
		private var container:DisplayObjectContainer;
		private var item:ItemClassVo;
		private var rect:Rectangle;
		public var icon:MovieClip;
		public function ItemIconView(_container:DisplayObjectContainer,_w:Number,_h:Number,_rect:Rectangle=null) 
		{
			container = _container;
			maxWidth = _w;
			maxHeight = _h;
			if (_rect) 
			{
				rect = _rect;
			}else {
				rect=new Rectangle(0, 0, maxWidth, maxHeight);
			}
		}
		
		public function setData(_item:ItemClassVo):void {
			item = _item;
			loadIcon();
		}
		
		private function loadIcon():void
		{
			
			if (!item) 
			{
				trace("道具不存在");
				return;
			}
			SwfClassCache.getInstance().addEventListener(SwfClassCacheEvent.COMPLETE, classGeted);
			SwfClassCache.getInstance().loadClass(item.class_name);
			
		}
		
		private function classGeted(e:SwfClassCacheEvent):void 
		{
			if (e.className==item.class_name) 
			{
				SwfClassCache.getInstance().removeEventListener(SwfClassCacheEvent.COMPLETE,classGeted);
				
				createIcon();
			}
		}
		
		private function createIcon():void
		{
			var tmpclass:Class = SwfClassCache.getInstance().getClass(item.class_name);
			icon = new tmpclass() as MovieClip;
			container.addChild(icon);
			
			layoutBody();
		}
		
		public function layoutBody():void {
			var iconrect:Rectangle = icon.getBounds(icon);
			
			var iconScale:Number;
			var wScale:Number = maxWidth / iconrect.width;
			var hScale:Number = maxHeight / iconrect.height;
			if (wScale>hScale) 
			{
				iconScale = hScale;
			}else {
				iconScale = wScale;
			}
			iconScale = Number(iconScale.toFixed(2));
			icon.scaleX=
			icon.scaleY = iconScale;
			
			var tmprect:Rectangle = rect;
			
			icon.x = tmprect.x + (tmprect.width / 2) - iconrect.width * iconScale / 2 -(iconrect.left-icon.x)* iconScale-6*iconScale;
			
			icon.y = tmprect.y + (tmprect.height / 2) - iconrect.height * iconScale / 2 - (iconrect.top - icon.y) * iconScale-6 * iconScale;
		}
		
	}

}