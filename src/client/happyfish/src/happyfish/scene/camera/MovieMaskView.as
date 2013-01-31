package happyfish.scene.camera 
{
	import com.greensock.TweenLite;
	import flash.display.DisplayObjectContainer;
	import flash.display.Sprite;
	import flash.events.Event;
	/**
	 * ...
	 * @author slamjj
	 */
	public class MovieMaskView extends Sprite
	{
		private var topMask:Sprite;
		private var bottomMask:Sprite;
		private var container:DisplayObjectContainer;
		private var time:uint;
		
		public function MovieMaskView() 
		{
			
		}
		
		public function showMaskMv(_container:DisplayObjectContainer, _w:Number, _h:Number, maskHeight:Number, _time:uint = 1):void {
			container = _container;
			time = _time;
			topMask = new Sprite();
			topMask.cacheAsBitmap = true;
			bottomMask = new Sprite();
			bottomMask.cacheAsBitmap = true;
			
			addChild(topMask);
			addChild(bottomMask);
			
			_container.addChild(this);
			x = _container.stage.stageWidth / 2;
			y = _container.stage.stageHeight / 2;
			
			topMask.graphics.beginFill(0x000000);
			topMask.graphics.drawRect(0, 0, _w, 1);
			topMask.graphics.endFill();
			topMask.x = -_w / 2;
			topMask.y = -_h / 2;
			
			
			bottomMask.graphics.beginFill(0x000000);
			bottomMask.graphics.drawRect(0, -1, _w, 1);
			bottomMask.graphics.endFill();
			
			bottomMask.x = -_w / 2;
			bottomMask.y = _h/2+1;
			
			TweenLite.to(topMask, time, { height:maskHeight } );
			TweenLite.to(bottomMask, time, { height:maskHeight } );
			
			_container.stage.addEventListener(Event.RESIZE, resizeFun);
		}
		
		public function closeMaskMv():void {
			TweenLite.to(topMask, time, { height:0 } );
			TweenLite.to(bottomMask, time, { height:0,onComplete:remove } );
		}
		
		public function remove():void {
			container.stage.removeEventListener(Event.RESIZE, resizeFun);
			removeChild(topMask);
			removeChild(bottomMask);
			
			topMask = bottomMask = null;
			
			if (parent) 
			{
				parent.removeChild(this);
			}
		}
		
		private function resizeFun(e:Event):void 
		{
			x = container.stage.stageWidth / 2;
			y = container.stage.stageHeight / 2;
		}
		
	}

}