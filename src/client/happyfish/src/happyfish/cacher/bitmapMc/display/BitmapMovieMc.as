package happyfish.cacher.bitmapMc.display
{
	import flash.display.BitmapData;
	import flash.events.Event;

	public class BitmapMovieMc extends BitmapMc
	{
		public var className:String;
		
		public var end_frame:int;
		public var start_frame:int = 0;
		
		public var cur_frame:int;
		
		//是否正在播放
		private var is_playing:Boolean = false;
		
		protected var labels:Object;
		private var labels_frame_index:Array = new Array();
		
		//当前使用的label
		private var label:String;
		
		private var label_flag:Boolean = false;
		
		//是否到某一帧停止
		private var to_stop:Boolean = false;
		private var drawLimitFrame:uint=1;
		private var _drawFrame:uint=1;
		
		/**
		 * 
		 * @param	$bitmaps
		 * @param	$offset_x_array
		 * @param	$offset_y_array
		 * @param	$lables	内部结构:key=标签名,值为标签开始帧数
		 * @param	bitmapData
		 * @param	pixelSnapping
		 * @param	smoothing
		 */
		public function BitmapMovieMc($bitmaps:Array, $offset_x_array:Array, $offset_y_array:Array, $lables:Object = null, bitmapData:BitmapData=null, pixelSnapping:String='auto', smoothing:Boolean=false)
		{
			
			
			this.end_frame = $bitmaps.length;
			this.cur_frame = this.start_frame;
			this.labels = $lables;
			
			for each(var i:int in $lables) {
				labels_frame_index[i] = 1;
			}
			
			super($bitmaps, $offset_x_array, $offset_y_array, bitmapData, pixelSnapping, smoothing);
		}
		
		public function play($to_stop:Boolean = false):void
		{
			if (this.is_playing) {
				return;
			}
			
			//如果只有一帧,则不播放 XXX
			if (this.end_frame === 1) {
				return;
			}
			
			if ($to_stop == false) {
				this.to_stop = false;
			}
			
			this.is_playing = true;
			this.addEventListener(Event.ENTER_FRAME, reDraw);
			
			this.setLabelFlag(false);
		}
		
		public function playToStop($frame:int = 0):void
		{
			if (!$frame) {
				$frame = this.end_frame;
			}
			this.play(true);
			
			this.to_stop = true;
		}
		
		private function reDraw(event:Event = null):void
		{
			if (drawLimitFrame>0) 
			{
				drawLimitFrame--;
			}else {
				
				
				//如果只有一帧,则不重绘,提高单幅效率
				if (this.end_frame === 1) {
					return;
				}
				if (label_flag) 
				{
					if (labels[this.label]) {
					}else {
						return;
					}
				}
				this.dynamicChangeCurframe();
				
				if (this.bitmap_data_array[this.cur_frame - 1] != null) {
					this.bitmapData = this.bitmap_data_array[this.cur_frame - 1];
				}
				drawLimitFrame = drawFrame;
			}
		}
		
		public function gotoAndPlay($frame_num:int):void
		{
			label = "";
			this.cur_frame = $frame_num;
			
			this.play();
		}
		
		public function gotoAndStop($frame_num:int):void
		{
			label = "";
			this.cur_frame = $frame_num;
			
			this.is_playing = false;
			this.stop();
		}
		
		public function gotoAndPlayLabels($label:String,$to_stop:Boolean=false):void
		{	
			
			if (($label!="" && $label!=null)) 
			{
				//如果标签相同,则无视
				if (this.label == $label) {
					
				}else {
					this.cur_frame = this.labels[$label];
					this.label = $label;
				}
				
				to_stop=$to_stop;
				
				this.play(to_stop);
				this.setLabelFlag(true);
				
			}else {
				//如果label为空就转到第一帧
				cur_frame = 1;
				label = $label;
				gotoAndStop(cur_frame);
				this.setLabelFlag(false);
			}
			
		}
		
		public function gotoAndStopLabels($label:String):void
		{	
			//如果标签相同,则无视
			this.cur_frame = this.labels[$label];
			this.label = $label;

			this.setLabelFlag(true);
			this.stop();
		}
		
		private function dynamicChangeCurframe():void
		{
			//标签播放时
			if (this.label_flag) {
				if (this.labels_frame_index[this.cur_frame + 1] || this.cur_frame >= this.end_frame) {
					if (to_stop) 
					{
						stop();
					}else {
						this.cur_frame = this.labels[this.label];
					}
				}else {
					cur_frame++;
				}
			}else {
				cur_frame++;
			}
			
			if (this.cur_frame > this.end_frame) {
				
				if (this.to_stop) {
					this.stop();
					return;
				}
				this.cur_frame = this.start_frame;
			}
		}
		
		public function playLabels():void
		{

		}
		
		private function setLabelFlag($flag:Boolean):void
		{
			this.label_flag = $flag;
		}
		
		public function stop():void
		{
			
			this.removeEventListener(Event.ENTER_FRAME, reDraw);
			this.is_playing = false;
			this.bitmapData = this.bitmap_data_array[this.cur_frame - 1];
			to_stop = false;
		}
		
		public function get drawFrame():uint { return _drawFrame; }
		
		public function set drawFrame(value:uint):void 
		{
			_drawFrame = value;
			drawLimitFrame = drawFrame;
		}
	}
}