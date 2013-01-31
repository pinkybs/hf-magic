package happymagic.scene.world.control
{
	import com.friendsofed.isometric.Point3D;
    import flash.geom.*;

    public class AvatarCommand extends Object
    {
		//到达点后的朝向目标点
        public var fiddleTowards:Point3D;
		//目的地
        public var destination:Point3D;
		//朝向目标点后做的动画动作
        public var fiddleAnimation:String;
		//回调函数
        private var callback:Function;
		private var callbackParams:Array;
		public var type:String;
		//到达后表现动画时间
        public var fiddleDuration:Number;
		//动画结束回调
		public var fiddleCallback:Function;
		
		public var mustGo:Boolean;
		public var truePoint:Point;
		/**
		 * 
		 * @param	$destination	[Point3D] 格子坐标，目标位置
		 * @param	$func	完成后的回调方法
		 * @param	$fiddleTowards	到达后人物朝向位置
		 * @param	$fiddleDuration	到达后表现动画时间
		 * @param	$fiddleAnimation	动画标签
		 * @param	$end_func		最后动画表现完成后回调方法
		 * @param	truePoint		最终要走到的坐标,此坐标不是格子坐标,而是像素坐标
		 */
        public function AvatarCommand($destination:Point3D, $func:Function = null, $fiddleTowards:Point3D = null, $fiddleDuration:Number = 0, $fiddleAnimation:String = null, $end_func:Function = null,$type:String="",_mustGo:Boolean=false,_callbackParams:Array=null,_truePoint:Point=null)
        {
			type = $type;
			this.destination = $destination;
			truePoint = _truePoint;
            
            this.callback = $func;
			callbackParams = _callbackParams;

            this.fiddleAnimation = $fiddleAnimation;
            this.fiddleTowards = $fiddleTowards;
            this.fiddleDuration = $fiddleDuration;
			
			this.fiddleCallback = $end_func;
			
			mustGo = _mustGo;
            return;
        }
		
		/**
		 * 开始队列处理
		 */
        public function doIt() : void
        {
            if (this.callback != null)
            {
				callback.apply(null,callbackParams);
            }
            return;
        }
		
		public function fiddleDoIt():void
		{
            if (this.fiddleCallback != null)
            {
                this.fiddleCallback();
            }
            return;
		}

    }
}
