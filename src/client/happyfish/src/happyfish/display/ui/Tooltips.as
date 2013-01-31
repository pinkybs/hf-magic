package happyfish.display.ui
{
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.text.TextField;
	import flash.text.TextFormat;
	import flash.utils.clearTimeout;
	import flash.utils.Dictionary;
	import flash.utils.setTimeout;
	
	/**
	 * ToolTips类
	 * 单例 是管理类
	 * @author jj
	 */
	public class Tooltips 
	{
		private static var instance:Tooltips;
		private var map:Dictionary;
		private var maxWidth:uint=120;
		private var maxHeight:uint=30;
		private var tips:Sprite;
		private var container:DisplayObjectContainer;
		private var tipsTxt:TextField;
		private var showTimeId:uint;
		public var delay:uint=300;
		private var tipsformat:TextFormat;
		private var bg:MovieClip;
		private var bgs:Object;
		
		public var buffer:int=5;
		
		public static const TYPE_DRAG:String = "dragTips";
		public static const TYPE_STAND:String = "standTips";
		
		public function Tooltips(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
					//init
				bgs = new Object();
				map = new Dictionary();
				tips = new Sprite();
				tips.mouseChildren =
				tips.mouseEnabled = false;
				createTips();
				}
			}
			else
			{	
				throw new Error( "Tooltips"+"单例" );
			}
		}
		
		public static function getInstance():Tooltips
		{
			if (instance == null)
			{
				instance = new Tooltips( new Private() );
				
			}
			return instance;
		}
		
		public function setContainer(_Container:DisplayObjectContainer):void {
			container = _Container;
		}
		
		public function setBg(_bg:MovieClip):void {
			bg = _bg;
		}
		
		public function saveBg(bgname:String,dis:MovieClip):void {
			bgs[bgname] = dis;
		}
		
		public function getBg(bgname:String):MovieClip {
			return bgs[bgname];
		}
		
		
		public function register(target:DisplayObject, content:String="",_bg:MovieClip=null,type:String=TYPE_DRAG,_standPoint:Point=null):void {
			if (!map[target]) 
			{
				target.addEventListener(MouseEvent.MOUSE_OVER, overFun);
				target.addEventListener(MouseEvent.MOUSE_OUT, outFun);
				target.addEventListener(MouseEvent.ROLL_OUT, outFun);
				target.addEventListener(Event.REMOVED_FROM_STAGE, beRemovedFun);
				map[target] = {type:type, bg:_bg, content:content,standPoint:_standPoint};
			}else {
				map[target].content = content;
			}
		}
		
		private function beRemovedFun(e:Event):void 
		{
			e.currentTarget.removeEventListener(Event.REMOVED_FROM_STAGE, beRemovedFun);
			unRegister(e.currentTarget as DisplayObject);
		}
		
		public function unRegister(target:DisplayObject):void {
			if (map[target]) 
			{
				delete map[target];
				target.removeEventListener(MouseEvent.MOUSE_OVER, overFun);
				target.removeEventListener(MouseEvent.MOUSE_OUT, outFun);
				target.removeEventListener(MouseEvent.ROLL_OUT, outFun);
			}
		}
		
		private function outFun(e:MouseEvent):void 
		{
			if (showTimeId) 
			{
				clearTimeout(showTimeId);
			}
			tips.visible = false;
			container.removeEventListener(Event.ENTER_FRAME, dragFun);
		}
		
		private function overFun(e:MouseEvent):void 
		{
			
			var tmpP:Point;
			if (showTimeId) 
			{
				clearTimeout(showTimeId);
			}
			if (map[e.currentTarget].type==TYPE_DRAG) 
			{
				//bg.visible = true;
				tmpP = new Point(container.mouseX, container.mouseY);
				showTimeId=setTimeout(initTips,delay,map[e.currentTarget].content,tmpP ,map[e.currentTarget].type,map[e.currentTarget].bg);
			}else {
				//bg.visible = false;
				if (map[e.currentTarget].standPoint) 
				{
					tmpP = map[e.currentTarget].standPoint;
				}else {
					tmpP = new Point(e.currentTarget.x+17, e.currentTarget.y-15);
					tmpP = e.currentTarget.parent.localToGlobal(tmpP);
					tmpP = container.globalToLocal(tmpP);
				}
				
				showTimeId=setTimeout(initTips,delay,map[e.currentTarget].content, tmpP,map[e.currentTarget].type,map[e.currentTarget].bg);
			}
			
		}
		
		private function createTips():void {
			tipsTxt = new TextField();
			
			tipsformat = tipsTxt.getTextFormat();
			tipsformat.align = "left";
			tips.addChild(tipsTxt);
			tips.visible = false;
		}
		
		
		
		private function initTips(str:String, point:Point, type:String, _bg:MovieClip = null ):void {
			
			while (tips.numChildren>0) 
			{
				tips.removeChildAt(0);
			}
			
			var ox:Number;
			var oy:Number;
			
			switch (type) 
			{
				case TYPE_DRAG:
					oy = -25;
				break;
				
				case TYPE_STAND:
					
					//tips.x = ;
				break;
			}
			
			tipsTxt.autoSize = "left";
			//tipsTxt.width = maxWidth;
			tipsTxt.multiline = true;
			tipsTxt.wordWrap = false;
			
			tipsTxt.htmlText = str;
			
			tipsTxt.setTextFormat(tipsformat);
			
			tipsTxt.x = 
			tipsTxt.y = buffer;
			tips.addChild(tipsTxt);
			
			
			tips.addChildAt(_bg,0);
			_bg.width = tipsTxt.textWidth+buffer*4;
			_bg.height = tipsTxt.textHeight +buffer*4;
			_bg.x = 0;
			_bg.y = 0;
			
			alginTips(point);
			
			container.addChild(tips);
			tips.visible = false;
			
			if (type==TYPE_DRAG) 
			{
				container.addEventListener(Event.ENTER_FRAME, dragFun);
			}
			
		}
		
		private function alginTips(point:Point):void
		{
			var rect:Rectangle = tips.getBounds(tips);
			
			tips.x = point.x - rect.width / 2;
			tips.y = point.y - rect.height;
			
			
			var tmpx:Number;
			var tmpy:Number;
			
			tmpx = Math.min(tips.x, container.stage.stageWidth - rect.width);
			tmpx = Math.max(0, tips.x);
			
			tmpy = Math.max(0, tips.y);
			tmpy = Math.min(tips.y, container.stage.stageWidth - rect.height);
			
			tips.x = tmpx;
			tips.y = tmpy;
			
			tips.visible = true;
		}
		
		private function dragFun(e:Event):void 
		{
			var point:Point = new Point(container.mouseX, container.mouseY);
			alginTips(point);
		}
		
		public function setMaxSize(_w:uint, _h:uint = 0 ):void {
			maxWidth = _w;
			maxHeight = _h;
			if (tipsTxt) 
			{
				tipsTxt.width = maxWidth;
			}
		}
		
	}
	
}
class Private {}