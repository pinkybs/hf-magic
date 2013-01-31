package com.friendsofed.isometric
{
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.DisplayObject;
	public class DrawnIsoTile extends IsoObject
	{
		protected var _height:Number;
		protected var _color:uint;
		
		public function DrawnIsoTile(size:Number, color:uint, height:Number = 0)
		{
			super(size);
			_color = color;
			_height = height;
			draw();
		}
		
		/**
		 * Draws the tile.
		 */
		protected function draw():void
		{
			graphics.clear();
			graphics.beginFill(_color);
			graphics.lineStyle(0, 0, .5);
			graphics.moveTo(-size, 0);
			graphics.lineTo(0, -size * .5);
			graphics.lineTo(size, 0);
			graphics.lineTo(0, size * .5);
			graphics.lineTo( -size, 0);
			
			this.alpha = 0.3;
			//var tmpbm:Bitmap = new Bitmap(new tilePng(45, 23) as BitmapData);
			//tmpbm.x = -22.5;
			//tmpbm.y = -11.5;
			//addChild(tmpbm);
			//cacheAsBitmap = true;
			//cacheAsBitmap = true;
		}
		
		
		/**
		 * Sets / gets the height of this object. Not used in this class, but can be used in subclasses.
		 */
		override public function set height(value:Number):void
		{
			_height = value;
			draw();
		}
		override public function get height():Number
		{
			return _height;
		}
		
		/**
		 * Sets / gets the color of this tile.
		 */
		public function set color(value:uint):void
		{
			_color = value;
			draw();
		}
		public function get color():uint
		{
			return _color;
		}
	}
}