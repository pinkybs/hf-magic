package happyfish.display.view 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.text.TextField;
	import flash.text.TextFormat;
	/**
	 * ...
	 * @author jj
	 */
	public class PersonChatsView extends Sprite
	{
		private var bg:MovieClip;
		private var container:DisplayObjectContainer;
		private var tipsTxt:TextField;
		private var tipsformat:TextFormat;
		private var buffer:int=5;
		private var showPoint:Point;
		
		public function PersonChatsView(__bg:MovieClip,__container:DisplayObjectContainer,toPoint:Point) 
		{
			bg = __bg;
			container = __container;
			showPoint = toPoint;
		}
		
		public function setChats(str:String):void {
			createTips();
			initTips(str,bg);
		}
		
		private function createTips():void {
			tipsTxt = new TextField();
			
			tipsformat = tipsTxt.getTextFormat();
			tipsformat.align = "left";
			addChild(tipsTxt);
		}
		
		private function initTips(str:String, _bg:MovieClip ):void {
			
			var ox:Number;
			var oy:Number;
			
			tipsTxt.autoSize = "left";
			tipsTxt.multiline = true;
			tipsTxt.wordWrap = false;
			
			tipsTxt.htmlText = str;
			
			tipsTxt.setTextFormat(tipsformat);
			
			tipsTxt.x = 
			tipsTxt.y = buffer;
			addChild(tipsTxt);
			
			
			addChildAt(bg,0);
			bg.width = tipsTxt.textWidth+buffer*4;
			bg.height = tipsTxt.textHeight +buffer*4;
			bg.x = 0;
			bg.y = 0;
			
			alginTips(showPoint);
			
			container.addChild(this);
			
		}
		
		private function alginTips(point:Point):void
		{
			var rect:Rectangle = getBounds(this);
			
			x = point.x - rect.width / 2;
			y = point.y - rect.height;
			
			tips.visible = true;
		}
		
	}

}