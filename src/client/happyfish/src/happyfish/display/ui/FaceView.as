package happyfish.display.ui 
{
	import flash.display.Bitmap;
	import flash.display.Loader;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.geom.Rectangle;
	import flash.net.URLRequest;
	import flash.system.ApplicationDomain;
	import flash.system.LoaderContext;
	
	/**
	 * 头像显示类
	 * @author slamjj
	 */
	public class FaceView extends Sprite
	{
		private var size:uint;
		private var faceUrl:String;
		
		public function FaceView(_size:uint=50) 
		{
			size = _size;
		}
		
		/**
		 * 加载指定头像图片
		 * @param	face
		 */
		public function loadFace(face:String):void {
			
			if (face == faceUrl) return;
			
			faceUrl = face;
			
			while (numChildren>0) 
			{
				removeChildAt(0);
			}
			
			var loader:Loader = new Loader();
			loader.contentLoaderInfo.addEventListener(Event.COMPLETE, loadFace_complete);
			loader.contentLoaderInfo.addEventListener(IOErrorEvent.IO_ERROR, loadFace_ioError);
			try {
				loader.load(new URLRequest(face));
			}catch (e:Error) {
				
			}
			
		}
		
		private function loadFace_ioError(e:IOErrorEvent):void 
		{
			e.target.removeEventListener(Event.COMPLETE, loadFace_complete);
			e.target.removeEventListener(IOErrorEvent.IO_ERROR, loadFace_ioError);
			
			dispatchEvent(new Event(Event.COMPLETE));
		}
		
		private function loadFace_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, loadFace_complete);
			e.target.removeEventListener(IOErrorEvent.IO_ERROR, loadFace_ioError);
			
			//var tmpbt:Bitmap = e.target.loader.content as Bitmap;
			var tmpbt:Loader = e.target.loader as Loader;
			
			//tmpbt.smoothing = true;
			addChild(tmpbt);
			
			var iconScale:Number;
			var wScale:Number = size / tmpbt.width;
			var hScale:Number = size / tmpbt.height;
			if (wScale>hScale) 
			{
				iconScale = wScale;
			}else {
				iconScale = hScale;
			}
			iconScale = Number(iconScale.toFixed(2));
			tmpbt.scaleX=
			tmpbt.scaleY = iconScale;
			
			scrollRect = new Rectangle(0, 0, size, size);
			//width = height = size;
			
			dispatchEvent(new Event(Event.COMPLETE));
		}
		
	}

}