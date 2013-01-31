package happyfish.cacher.bitmapMc.display
{
	import flash.display.Bitmap;
	import flash.display.BitmapData;

	public class BitmapMc extends Bitmap
	{
		//存放bitmapData的array
		protected var bitmap_data_array:Array;
		
		//存放偏移x数组
		protected var offset_x_array:Array;
		
		protected var offset_y_array:Array;
		
		public function BitmapMc($bitmaps:Array, $offset_x_array:Array, $offset_y_array:Array, bitmapData:BitmapData=null, pixelSnapping:String='auto', smoothing:Boolean=false)
		{
			this.bitmap_data_array = $bitmaps;
			
			this.offset_x_array = $offset_x_array;
			this.offset_y_array = $offset_y_array;
			
			super(bitmapData, pixelSnapping, smoothing);
		}
		
		public function clear():void
		{
        	for each (var bitmapData:BitmapData in this.bitmap_data_array.length)
        		bitmapData.dispose();
		}
		
        override public function set x(param1:Number) : void
        {
            super.x = int(this.offset_x_array[0] + param1);
            return;
        }// end function

        override public function set y(param1:Number) : void
        {
            super.y = int(this.offset_y_array[0] + param1);
            return;
        }// end function

        override public function get x() : Number
        {
            return super.x - this.offset_x_array[0];
        }// end function

        override public function get y() : Number
        {
            return super.y - this.offset_y_array[0];
        }// end function
		
		public function setSuperX($x:int):void
		{
			super.x = $x;
		}
		
		public function setSuperY($y:int):void
		{
			super.y = $y;
		}
		
	}
}