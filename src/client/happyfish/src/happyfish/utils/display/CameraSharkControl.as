package happyfish.utils.display 
{
	import com.adobe.utils.ArrayUtil;
	import flash.display.DisplayObject;
	import flash.events.TimerEvent;
	import flash.utils.setTimeout;
	import flash.utils.Timer;
	/**
	 * ...
	 * @author jj
	 */
	public class CameraSharkControl
	{
		private static var sharkTargets:Array=new Array();
		private static var sharkTimer:Timer;
		
		private static const timerTime:int = 33;
		public function CameraSharkControl() 
		{
			
		}
		
		/**
		 * 增加一个物件进行抖动
		 * @param	target	目标
		 * @param	dist	抖动的范围
		 * @param	time	抖动的时间,以毫秒计
		 * @param	delay	延迟开始时间,以毫秒计
		 */
		public static function shark(target:DisplayObject, dist:uint, time:uint,callBack:Function=null, delay:uint=0):void {
			if (!sharkTargets) 
			{
				sharkTargets = new Array();
			}
			setTimeout(addTarget, delay, target, dist, time,callBack);
		}
		
		private static function addTarget(target:DisplayObject, dist:uint, time:uint,callBack:Function):void {
			sharkTargets.push( { target:target, dist:dist, time:time, ox:target.x, oy:target.y, callback:callBack } );
			startShark();
		}
		
		
		
		private static function startShark():void {
			if (!sharkTimer) 
			{
				sharkTimer = new Timer(timerTime);
				sharkTimer.addEventListener(TimerEvent.TIMER, sharkTimeFun);
			}
			
			if (!sharkTimer.running) 
			{
				sharkTimer.start();
			}
		}
		
		static private function sharkTimeFun(e:TimerEvent):void 
		{
			var tmpobj:Object;
			for (var i:int = 0; i < sharkTargets.length; i++) 
			{
				tmpobj = sharkTargets[i];
				if (tmpobj.time<=0) 
				{
					stopShark(tmpobj);
				}else {
					sharkFun(tmpobj.target, tmpobj.ox, tmpobj.oy, tmpobj.dist);
					tmpobj.time-= timerTime;
				}
			}
		}
		
		static public function stopShark(targetObj:Object):void
		{
			targetObj.target.x = targetObj.ox;
			targetObj.target.y = targetObj.oy;
			//执行回调
			if (targetObj.callback) 
			{
				targetObj.callback.call();
			}
			//从队列内删除
			ArrayUtil.removeValueFromArray(sharkTargets, targetObj);
			//如果队列为空了,就停止timer
			if (sharkTargets.length==0) 
			{
				sharkTimer.stop();
			}
			
		}
		
		public static function hasTarget(target:DisplayObject):Boolean {
			return ArrayUtil.arrayContainsValue(sharkTargets, target);
		}
		
		private static function sharkFun(target:DisplayObject,ox:int,oy:int, dist:uint):void {
			var tx:int = Math.floor(Math.random() * dist);
			Math.floor(Math.random() * 1) ? tx = -tx : tx = tx;
			var ty:int = Math.floor(Math.random() * dist);
			Math.floor(Math.random() * 1) ? ty = -ty : ty = ty;
			
			target.x = ox+tx;
			target.y = oy+ty;
		}
		
	}

}