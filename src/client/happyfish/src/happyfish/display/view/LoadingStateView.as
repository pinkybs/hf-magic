package happyfish.display.view 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.events.Event;
	/**
	 * ...
	 * @author jj
	 */
	public class LoadingStateView extends Sprite
	{
		public var showing:Boolean;
		private var _container:DisplayObjectContainer;
		private var mv:MovieClip;
		private var _w:Number;
		private var _h:Number;
		private var bg:Shape;
		
		public function LoadingStateView(__container:DisplayObjectContainer,__w:Number,__h:Number) 
		{
			_container = __container;
			_w = __w;
			_h = __h;
			
			x = _w / 2;
			y = _h / 2;
			
			_container.stage.addEventListener(Event.RESIZE, resize_fun);
		}
		
		private function resize_fun(e:Event):void 
		{
			_w = _container.stage.stageWidth;
			_h = _container.stage.stageHeight;
			
			x = _w / 2;
			y = _h / 2;
			
			initBg(_w, _h);
		}
		
		public function set body(__mv:MovieClip):void {
			mv = __mv;
			mv.stop();
			addChild(mv);
		}
		
		public function showMe():void {
			
			if (showing) 
			{
				return;
			}
			
			showing = true;
			if (!bg) 
			{
				initBg(_w,_h);
			}
			
			mv.gotoAndPlay(1);
			addChild(mv);
			_container.addChild(this);
			visible = true;
		}
		
		public function initBg(__w:Number=0,__h:Number=0):void
		{
			if (bg) 
			{
				removeChild(bg);
			}
			bg = new Shape();
			bg.graphics.beginFill(0x000000, .5);
			bg.graphics.drawRect( -__w / 2, -__h / 2, __w, __h);
			bg.graphics.endFill();
			addChild(bg);
		}
		
		public function closeMe():void {
			showing = false;
			//_container.removeChild(this);
			visible = false;
		}
	}

}