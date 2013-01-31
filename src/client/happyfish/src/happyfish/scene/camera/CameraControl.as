package happyfish.scene.camera 
{
	import com.greensock.TweenLite;
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.display.Sprite;
	import flash.display.Stage;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	
	/**
	 * ...
	 * @author jj
	 */
	public class CameraControl 
	{
		
		public function CameraControl(access:Private) 
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
				throw new Error( "CameraControl"+"单例" );
			}
		}
		
		public function init($worldWidth:Number,$worldHeight:Number):void {
			_worldWidth = $worldWidth;
			_worldHeight = $worldHeight;
			//eventManager.addEventListener(
		}
		
		public function followTarget(target:DisplayObjectContainer, cameraMc:DisplayObjectContainer, _followPoint:Point = null):void {
			if (followTargetMc) 
			{
				stopFollow();
			}
			followTargetMc = target;
			followCameraMc = cameraMc;
			followPoint = _followPoint;
			target.addEventListener(Event.ENTER_FRAME, centerTargetFun);
			target.addEventListener(Event.REMOVED_FROM_STAGE, centerTargetRemoved);
		}
		
		public function stopFollow():void {
			followTargetMc.removeEventListener(Event.REMOVED_FROM_STAGE, centerTargetRemoved);
			followTargetMc.removeEventListener(Event.ENTER_FRAME, centerTargetFun);
			
			followTargetMc = null;
			followCameraMc = null;
			followPoint = null;
		}
		
		private function centerTargetFun(e:Event):void 
		{
			//centerTarget(followTargetMc, followCameraMc, followPoint);
			centerTweenTo(followTargetMc, followCameraMc, followPoint);
		}
		
		private function centerTargetRemoved(e:Event):void 
		{
			stopFollow();
		}
		
		public function centerTweenTo(target:DisplayObjectContainer, cameraMc:DisplayObjectContainer, _centerPoint:Point = null):void {
			
			var btw:Point = getCenterTargetBtw(target, cameraMc, _centerPoint);
			
			TweenLite.to(cameraMc, 1, { x:cameraMc.x +btw.x, y:cameraMc.y + btw.y } );
		}
		
		public function centerTweenToPoint(point:Point, cameraMc:DisplayObjectContainer, _centerPoint:Point = null):void {
			var btw:Point = getCenterPoint(point, cameraMc, _centerPoint);
			
			TweenLite.to(cameraMc, 1, { x:cameraMc.x +btw.x, y:cameraMc.y + btw.y } );
		}
		
		public function centerToPoint(point:Point, cameraMc:DisplayObjectContainer, _centerPoint:Point = null):void {
			
			var btw:Point = getCenterPoint(point, cameraMc, _centerPoint);
			
			cameraMc.x += btw.x;
			cameraMc.y += btw.y;
		}
		
		public function centerTarget(target:DisplayObjectContainer, cameraMc:DisplayObjectContainer, _centerPoint:Point = null):void {
			
			var btw:Point = getCenterTargetBtw(target, cameraMc, _centerPoint);
			
			cameraMc.x += btw.x;
			cameraMc.y += btw.y;
		}
		
		private function getCenterPoint(point:Point, cameraMc:DisplayObjectContainer, _centerPoint:Point = null):Point {
			centerPoint = _centerPoint;
			var stage:Stage = cameraMc.stage;
			if (centerPoint == null) {
				centerPoint = new Point(stage.stageWidth / 2, stage.stageHeight / 2);
			}
			
			
			//var wAdd:Number = Math.abs(stage.stageWidth - _worldWidth);
			//var hAdd:Number = Math.abs(stage.stageHeight - _worldHeight);
			//var rect:Rectangle = new Rectangle(_worldWidth / 2, _worldHeight / 2, -wAdd, -hAdd);
			//centerPoint.x = Math.max(-wAdd/2, centerPoint.x);
			//centerPoint.x = Math.min(wAdd/2, centerPoint.x);
			
			var btw:Point = new Point(centerPoint.x - point.x, centerPoint.y - point.y);
			
			
			
			return btw;
		}
		
		private function getCenterTargetBtw(target:DisplayObjectContainer, cameraMc:DisplayObjectContainer, _centerPoint:Point = null):Point {
			centerPoint = _centerPoint;
			if (centerPoint == null) {
				centerPoint = new Point(target.stage.stageWidth / 2, target.stage.stageHeight / 2);
			}
			
			var targetP:Point = target.parent.localToGlobal(new Point(target.x, target.y));
			
			var btw:Point = new Point(centerPoint.x - targetP.x, centerPoint.y - targetP.y);
			return btw;
		}
		
		public static function showMovieMaskMv(container:Sprite,width:Number,height:Number):void {
			
		}
		
		public static function getInstance():CameraControl
		{
			if (instance == null)
			{
				instance = new CameraControl( new Private() );
			}
			return instance;
		}
		
		
		private static var instance:CameraControl;
		private var camera:Sprite;
		private var centerPoint:Point;
		private var followTargetMc:DisplayObjectContainer;
		private var followCameraMc:DisplayObjectContainer;
		private var followPoint:Point;
		private var eventManager:EventDispatcher;
		private var _worldWidth:Number;
		private var _worldHeight:Number;
		
	}
	
}
class Private {}