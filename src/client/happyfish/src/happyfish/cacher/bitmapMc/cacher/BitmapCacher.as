package happyfish.cacher.bitmapMc.cacher
{
	import com.greensock.events.LoaderEvent;
	import flash.events.Event;
	import flash.geom.Point;
	import happyfish.cacher.bitmapMc.display.BitmapMc;
	import happyfish.cacher.bitmapMc.display.BitmapMovieMc;
	import happyfish.cacher.bitmapMc.events.BitmapCacherEvent;
	import happyfish.util.queue.driver.queue.BitmapCacherQueue;
	
	import flash.display.BitmapData;
	import flash.display.MovieClip;
	import flash.events.EventDispatcher;
	import flash.events.TimerEvent;
	import flash.geom.Matrix;
	import flash.geom.Rectangle;
	import flash.utils.Timer;
	import flash.utils.getQualifiedClassName;

	public class BitmapCacher extends EventDispatcher
	{
		//单例
		private static var single:BitmapCacher;
		
		//要缓存的动画
		public var mc:MovieClip;
		
		//绘制范围
		public var rect:Rectangle;
		
		//存储bitmap缓存对象
		private var bitmap_cache_data:Object = new Object;
		
		//存储偏移
		private var bitmap_cache_offset_x:Object = new Object;
		private var bitmap_cache_offset_y:Object = new Object;
		
		//存储lables
		private var bitmap_cache_labels:Object = new Object;
		
		//单个bitmap存储数组
		private var result:Array;
		
		private var endFrame:int;//最后一帧的位置
		
		private var readFrame:int;//预计要读取的帧
		
		//这个mc类的类名称
		private var class_name:String;
		
		private var _readComplete:Boolean;
		
		private var pre_bitmapData:BitmapData;
		
		//记录次mc的labels
		private var labels:Object;
		
		private var pre_lable:String = 'oxuidhwk';
		
		//时间间隔
		private static const TIME_INTERVAL:int = 0;
		
		private static var queueArray:Array = new Array();
		
		private var rectMaxWidth:Number = 0;
		private var rectMaxHeight:Number = 0;
		private var rectMax:Rectangle;
		private var pre_rect:Rectangle;
		private var cur_rect:Rectangle;
		private var startTime:Number;
		private var created:uint;
		
		public function BitmapCacher()
		{
		}
		
		public static function getInstance():BitmapCacher
		{
			if (single === null) {
				single = new BitmapCacher;
			}
			return single;
		}
		
		public function isCached(mcClass:Class):Boolean
		{
			var class_name:String = getQualifiedClassName(mcClass);
			if (this.bitmap_cache_data[class_name] && this.bitmap_cache_data[class_name] !== 1) {
				return true;
			} else {
				return false;
			}
		}
		
		public function getBitmapMc(mcClass:Class):BitmapMc
		{
			return this.getBasicBitmapMc(mcClass);
		}
		
		public function getBasicBitmapMc(mcClass:Class):*
		{	
			var class_name:String = getQualifiedClassName(mcClass);
			if (isCached(mcClass)) {
				//当已经有此对象缓存时
				
				//如果length==1,说明是单帧动画
				//现在故意改为length==-1判断,不可能发生,所以所有对象都用了BitmapMovieMc类来实现 
				if (this.bitmap_cache_data[class_name].length == -1) {
					return new BitmapMc(this.bitmap_cache_data[class_name], this.bitmap_cache_offset_x[class_name],this.bitmap_cache_offset_y[class_name], 
										this.bitmap_cache_data[class_name][0]);
				} else {
					
					var tmp:BitmapMovieMc=new BitmapMovieMc(this.bitmap_cache_data[class_name], this.bitmap_cache_offset_x[class_name],this.bitmap_cache_offset_y[class_name], 
															this.bitmap_cache_labels[class_name], this.bitmap_cache_data[class_name][0]);
					tmp.className = class_name;
					return tmp;
				}
			} else {
				//如果还未有该类的位图缓存,就加入队列开始创建
				
				//如果等待创建队列内没有这个类,才加入队列
				if (!queueArray[class_name]) {
					queueArray[class_name] = true;
					//push类里判断是否和前一个class_name相同.
					var bitmap_cacher_queue:BitmapCacherQueue = BitmapCacherQueue.getInstance();
					bitmap_cacher_queue.push(mcClass);
				}
			}
			return null;
		}
		
		/**
		 * 创建缓存 
		 */
		public function createCache(mcClass:Class):void
		{
			created++;
			this.class_name = getQualifiedClassName(mcClass);
			//如果位图缓存列表中已有这个类的缓存,就直接派发完成消息
			if (this.bitmap_cache_data[this.class_name]) {
				dispatchEvent(new BitmapCacherEvent(BitmapCacherEvent.BITMAP_CACHE_COMPLETED, this.class_name));
				return;
			}

			//缓存
			this.mc = new mcClass();
			this.readFrame = 1;
			this.mc.gotoAndStop(1);
			
			this.rect = this.mc.getBounds(this.mc);
			
			this.endFrame = mc.totalFrames;
			
			//this.setMaxRect();
			
			this.result = [];
			this.labels = new Object();
			this._readComplete = false;
			
			//保存偏移数组
			this.bitmap_cache_offset_x[this.class_name] = new Array();
			this.bitmap_cache_offset_y[this.class_name] = new Array();
			
			this.rectMax = null;
			
			//startTime = new Date().getTime();
			
			/*//计算保存最大矩形
			var timer:Timer;
			timer = new Timer(TIME_INTERVAL, int.MAX_VALUE);
			timer.addEventListener(TimerEvent.TIMER,setMaxRect);
			timer.start();*/
			
			rectMax=mc.getBounds(mc);
			this.readFrame = 1;
			var timer:Timer;
			timer = new Timer(TIME_INTERVAL, int.MAX_VALUE);
			timer.addEventListener(TimerEvent.TIMER,timeHandler);
			timer.start();
		}
		
		/**
		 * 获取此mc最大的区域
		 */
		private function setMaxRect(event:Event):void
		{
			
			//trace("setMaxRect", new Date().getTime() - startTime);
			startTime = new Date().getTime();
			if (mc.currentFrame >= readFrame) {
				readFrame++;
				cur_rect = this.mc.getBounds(this.mc);
				
				if (this.rectMax) {
					
					this.rectMax = this.rectMax.union(cur_rect);
					//trace(this.rectMax.height + ' ' + pre_rect.height);
					//trace(this.rectMax.height + ' ' + cur_rect.height);
				} else {
					this.rectMax = cur_rect;
				}
			
				if (mc.currentFrame >= endFrame) {
					//全部帧完成
					var timer:Timer = event.currentTarget as Timer;
					timer.removeEventListener(TimerEvent.TIMER,timeHandler);
					timer.stop();
					timer = null;
					//trace(this.rectMax);
					this.mc.gotoAndStop(1);
					this.readFrame = 1;

					timer = new Timer(TIME_INTERVAL, int.MAX_VALUE);
					timer.addEventListener(TimerEvent.TIMER,timeHandler);
					timer.start();
					
				} else {
					mc.nextFrame();
				}
			}
			
			/**
			if (this.rectMax.right > this.rectMaxWidth) {
				this.rectMaxWidth = this.rectMax.right;
			}
			
			if (this.rectMax.bottom > this.rectMaxHeight) {
				this.rectMaxHeight = this.rectMax.bottom;
			}
			*/
		}
		
		private function timeHandler(event:TimerEvent):void
		{
			if (mc.currentFrame >= readFrame)
			{
				this.rect = this.mc.getBounds(this.mc);
				var bitmapData:BitmapData = new BitmapData(Math.ceil(this.rectMax.width),Math.ceil(this.rectMax.height),true,0xffffff);
				var m:Matrix;
				if (rect)
				{
					m = new Matrix();
					var tmp_rect_x:Number = this.rectMax.x;
					var tmp_rect_y:Number = this.rectMax.y;
					
					m.translate(-tmp_rect_x,-tmp_rect_y);
					
					this.bitmap_cache_offset_x[this.class_name].push(tmp_rect_x);
					this.bitmap_cache_offset_y[this.class_name].push(tmp_rect_y);
				}
				bitmapData.draw(mc,m,null,null,null,false);
				
				if (this.diffBitmap(this.pre_bitmapData, bitmapData)) {
					this.pre_bitmapData = bitmapData;
					bitmapData = null;
				} else {
					this.pre_bitmapData = bitmapData;
				}

				this.result.push(bitmapData);
				//trace(this.mc.currentLabel);
				//取出标签
				if (this.mc.currentLabel != this.pre_lable) {
					this.pre_lable = this.mc.currentLabel;
					this.labels[this.mc.currentLabel] = mc.currentFrame;
				}

				if (mc.currentFrame >= endFrame) {
					readCompleteHandler(event);//trace('test');
				} else {
					readFrame++;
					mc.nextFrame();
				}
			}
		}
		
		private function readCompleteHandler(event:TimerEvent):void
		{
			var timer:Timer = event.currentTarget as Timer;
			timer.removeEventListener(TimerEvent.TIMER,timeHandler);
			timer.stop();
			timer = null;
			
			this.bitmap_cache_data[this.class_name] = this.result;
			this.bitmap_cache_labels[this.class_name] = this.labels;

			this._readComplete = true;

			dispatchEvent(new BitmapCacherEvent(BitmapCacherEvent.BITMAP_CACHE_COMPLETED, this.class_name));
		}
		
		public function getBitmapMovieMc(mcClass:Class):BitmapMovieMc
		{
			return this.getBasicBitmapMc(mcClass);
		}
		
		/**
		 * 相同返回true,不同返回false
		 */
		private function diffBitmap($bitmap_left:BitmapData, $bitmap_right:BitmapData):Boolean
		{
			if (this.pre_bitmapData != null) {
				var diff_flag:* = $bitmap_left.compare($bitmap_right);
				
				if (diff_flag === 0) {
					return true;
				}
			}
			return false;
		}
	}
}