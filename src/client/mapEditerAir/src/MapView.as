package  
{
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Matrix;
	import flash.geom.Rectangle;
	import happyfish.cacher.SwfClassCache;
	import happyfish.events.SwfClassCacheEvent;
	import happyfish.scene.world.control.MapDrag;
	import happymagic.scene.world.bigScene.BigSceneBg;
	/**
	 * ...
	 * @author jj
	 */
	public class MapView extends Bitmap
	{
		private var main:Main;
		private var className:String;
		private var toFrame:uint;
		
		public function MapView(_main:Main) 
		{
			main = _main;
			
			//x = -1000;
			//y = -650;
			
		}
		
		public function load(value:Object):void {
			loadClass(value.className);
			toFrame = value.frame;
		}
		
		public function loadClass(_className:String):void {
			className = _className;
			
			if (SwfClassCache.getInstance().hasClass(className)) 
			{
				create();
			}else {
				SwfClassCache.getInstance().addEventListener(SwfClassCacheEvent.COMPLETE, loadClass_complete);
				SwfClassCache.getInstance().loadClass(className);
			}
			
		}
		
		private function loadClass_complete(e:Event):void 
		{
			SwfClassCache.getInstance().removeEventListener(SwfClassCacheEvent.COMPLETE, loadClass_complete);
			create();
		}
		
		private function create():void
		{
			var tmpclass:Class = SwfClassCache.getInstance().getClass(className);
			smoothing = true;
			//bitmapData = new tmpclass(2000, 1300) as BitmapData;
			var tmpmc:MovieClip = new tmpclass() as MovieClip;
			tmpmc.gotoAndStop(toFrame);
			
			var rect:Rectangle = tmpmc.getRect(tmpmc);
			var tmpmatrix:Matrix = new Matrix();
					tmpmatrix.translate(-rect.x,-rect.y);
					
			var bd:BitmapData = new BitmapData(tmpmc.width, tmpmc.height, true, 0xffffff);
			
			bd.draw(tmpmc, tmpmatrix);
			bitmapData = bd;
			
			x = -rect.width/2;
			y = -rect.height/2;
		}
	}

}