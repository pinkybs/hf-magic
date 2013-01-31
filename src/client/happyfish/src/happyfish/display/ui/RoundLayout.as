package happyfish.display.ui 
{
	import com.aquioux.AqMath;
	import com.greensock.TweenLite;
	import flash.display.DisplayObject;
	/**
	 * 圆型列表抽像类
	 * 
	 * @author jj
	 */
	public class RoundLayout
	{
		private var _dist:Number;
		public var objects:Array;
		public var autoLayout:Boolean;
		private var _rotation:Number;
		private var tweener:TweenLite;
		
		/**
		 * 构造
		 * @param	__dist 圆型的半径
		 */
		public function RoundLayout(__dist:Number) 
		{
			_rotation = 0;
			autoLayout = false;
			_dist = __dist;
			objects = new Array();
			trace("objects", objects.length);
		}
		
		/**
		 * 批量增加内容
		 * @param	... args
		 */
		public function addObjects(... args):void {
			if (!objects) 
			{
				objects = new Array();
			}
			objects=objects.concat(args);
			if (autoLayout) 
			{
				layout();
			}
		}
		
		/**
		 * 设置半径
		 * @param	value
		 */
		public function setDist(value:Number):void {
			dist = value;
			if (autoLayout) 
			{
				layout();
			}
		}
		
		/**
		 * 设置圆型列表的旋转角度
		 * @param	value
		 */
		public function setRotation(value:Number):void {
			_rotation = value;
			if (autoLayout) 
			{
				layout();
			}
		}
		
		/**
		 * 立即排列一次
		 */
		public function layout():void {
			var degree:Number;
			var tmpObject:DisplayObject;
			if (!objects) {
				return;
			}
			for (var i:int = 0; i < objects.length; i++) 
			{
				tmpObject = objects[i] as DisplayObject;
				degree = ( 360 / objects.length ) * i+_rotation;
				tmpObject.x = Math.sin( AqMath.d2r( degree ) ) * dist;
				tmpObject.y = Math.cos( AqMath.d2r( degree ) ) * dist;
			}
		}
		
		/**
		 * 缓动到指定样式表现
		 * @param	_dist	半径
		 * @param	__rotation	旋转
		 * @param	time	缓动时间
		 */
		public function TweenTo(_dist:Number, __rotation:Number, time:Number = 1):void {
			if(tweener)	tweener.complete();
			tweener=TweenLite.to(this, time, { dist:_dist, rotation:__rotation } );
		}
		
		/**
		 * 从指定样式缓动到现有样式
		 * @param	_dist	半径
		 * @param	__rotation	旋转
		 * @param	time	缓动时间
		 */
		public function TweenFrom(_dist:Number, __rotation:Number, time:Number = 1):void {
			if(tweener)	tweener.complete();
			tweener= TweenLite.from(this, time, { dist:_dist, rotation:__rotation } );
		}
		
		/**
		 * 清空
		 */
		public function clear():void {
			objects = null;
		}
		
		public function get dist():Number { return _dist; }
		
		public function set dist(value:Number):void 
		{
			_dist = value;
			layout();
		}
		
		public function get rotation():Number { return _rotation; }
		
		public function set rotation(value:Number):void 
		{
			_rotation = value;
			layout();
		}
		
	}

}