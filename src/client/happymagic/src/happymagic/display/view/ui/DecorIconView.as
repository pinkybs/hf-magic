package happymagic.display.view.ui 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.geom.Rectangle;
	import happyfish.cacher.SwfClassCache;
	import happyfish.display.view.IconView;
	import happyfish.events.SwfClassCacheEvent;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.DecorVo;
	/**
	 * ...
	 * @author jj
	 */
	public class DecorIconView
	{
		public var maxWidth:Number;
		public var maxHeight:Number;
		private var container:DisplayObjectContainer;
		private var decor:DecorClassVo;
		private var rect:Rectangle;
		public var icon:*;
		
		public function DecorIconView(_container:DisplayObjectContainer,_w:Number,_h:Number,_rect:Rectangle=null) 
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
		
		public function setData(_decor:DecorClassVo):void {
			decor = _decor;
			loadIcon();
		}
		
		private function loadIcon():void
		{
			
			if (!decor) 
			{
				trace("道具不存在");
				return;
			}
			
			icon = new IconView(maxWidth, maxHeight, new Rectangle( -maxWidth / 2, -maxHeight, maxWidth, maxHeight));
			icon.setData(decor.class_name);
			container.addChildAt(icon,0);
		}
		
	}

}