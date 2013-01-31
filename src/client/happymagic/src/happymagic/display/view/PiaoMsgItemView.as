package happymagic.display.view 
{
	import com.greensock.easing.Circ;
	import com.greensock.TweenLite;
	import com.greensock.TweenMax;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.text.TextFormat;
	import flash.utils.setTimeout;
	import happyfish.manager.module.ModuleManager;
	import happyfish.utils.HtmlTextTools;
	import happymagic.manager.DisplayManager;
	/**
	 * ...
	 * @author jj
	 */
	public class PiaoMsgItemView extends Sprite
	{
		
		
		private var icon:MovieClip;
		private var txtMc:MovieClip;
		//开始出现的位置
		private var toY:int;
		private var toX:int;
		//最后飞去的位置
		private var toPoint:Point;
		private var type:uint;
		private var value:*;
		
		public var now:Boolean;
		public var justShow:Boolean;
		
		public function PiaoMsgItemView() 
		{
			mouseEnabled=
			mouseChildren = false;
			
			cacheAsBitmap = true;
			
			scaleX = scaleY = 1.5;
			
			visible = false;
		}
		
		public function setData(_type:uint, _value:*, _toX:int, _toY:int,_delay:uint=0):void {
			
			
			type = _type;
			value = _value;
			
			if (!justShow && type!=PiaoMsgType.TYPE_BAD_STRING && type!=PiaoMsgType.TYPE_GOOD_STRING) 
			{
				toPoint = (ModuleManager.getInstance().getModule("mainInfo") as MainInfoView).getValuePosition(type);
				toPoint = parent.globalToLocal(toPoint);
			}
			
			
			toX=_toX;
			toY=_toY;
			var tmpf:TextFormat;
			//创建不同的ICON和文字样式
			switch (type) 
			{
				case PiaoMsgType.TYPE_COIN:
				icon = new mCoinIcon();
				txtMc = new piaoNumberTxt();
				setTxtAutoSize();
				tmpf = txtMc.txt.getTextFormat();
				tmpf.color = 0x2F5873;
				txtMc.txt.text = (value > 0 ? "+ " : "") + value.toString();
				if (value < 0) toPoint = null;
				txtMc.txt.setTextFormat(tmpf);
				break;
				
				
				case PiaoMsgType.TYPE_GEM:
				icon = new mGemIcon();
				txtMc = new piaoNumberTxt();
				setTxtAutoSize();
				tmpf = txtMc.txt.getTextFormat();
				tmpf.color = 0xD89D03;
				txtMc.txt.text = (value > 0 ? "+ " : "") + value.toString();
				if (value < 0) toPoint = null;
				txtMc.txt.setTextFormat(tmpf);
				break;
				
				case PiaoMsgType.TYPE_EXP:
				icon = new mExpIcon();
				txtMc = new piaoExpTxt();
				setTxtAutoSize();
				tmpf = txtMc.txt.getTextFormat();
				tmpf.color = 0xFFCC00;
				txtMc.txt.text = (value > 0 ? "+ " : "") + value.toString();
				txtMc.txt.setTextFormat(tmpf);
				break;
				
				case PiaoMsgType.TYPE_MAGIC:
				icon = new magicIcon();
				txtMc = new piaoNumberTxt();
				setTxtAutoSize();
				tmpf = txtMc.txt.getTextFormat();
				tmpf.color = 0x33CCCC;
				txtMc.txt.text = (value > 0 ? "+ " : "") + value.toString();
				if (value < 0) toPoint = null;
				txtMc.txt.setTextFormat(tmpf);
				break;
				
				case PiaoMsgType.TYPE_MAX_MAGIC:
				icon = new magicIcon();
				txtMc = new piaoNumberTxt();
				setTxtAutoSize();
				tmpf = txtMc.txt.getTextFormat();
				tmpf.color = 0x33CCCC;
				txtMc.txt.text = (value > 0 ? "+ " : "") + value.toString();
				if (value < 0) toPoint = null;
				txtMc.txt.setTextFormat(tmpf);
				break;
				
				case PiaoMsgType.TYPE_ROOM_EXP:
				icon = new maxMpIcon();
				txtMc = new piaoNumberTxt();
				setTxtAutoSize();
				tmpf = txtMc.txt.getTextFormat();
				tmpf.color = 0x33CCCC;
				txtMc.txt.text = (value > 0 ? "+ " : "") + value.toString();
				if (value < 0) toPoint = null;
				txtMc.txt.setTextFormat(tmpf);
				break;
				
				case PiaoMsgType.TYPE_GOOD_STRING:
				txtMc = new piaoFontTxt();
				setTxtAutoSize();
				tmpf = txtMc.txt.getTextFormat();
				tmpf.letterSpacing = 1;
				txtMc.txt.htmlText = HtmlTextTools.fontWord(value.toString(), "#339900");
				txtMc.txt.setTextFormat(tmpf);
				if (value < 0) toPoint = null;
				break;
				
				case PiaoMsgType.TYPE_BAD_STRING:
				txtMc = new piaoFontTxt();
				setTxtAutoSize();
				tmpf = txtMc.txt.getTextFormat();
				tmpf.letterSpacing = 1;
				txtMc.txt.htmlText = HtmlTextTools.fontWord(value.toString(),"#FFCC00");
				txtMc.txt.setTextFormat(tmpf);
				if (value < 0) toPoint = null;
				break;
				
			}
			
			if (txtMc) 
			{
				//txtMc.txt.cacheAsBitmap=true;
			}
			
			//把文字和ICON排序
			if (icon) 
			{
				icon.cacheAsBitmap = true;
				addChild(icon);
				var iconRect:Rectangle = icon.getBounds(icon);
				icon.x = -(iconRect.width + txtMc.txt.textWidth) / 2 - iconRect.x;
				txtMc.x = icon.getBounds(this).right-5;
			}else {
				txtMc.x = -(txtMc.txt.textWidth) / 2;
			}
			
			addChild(txtMc);
			
			setTimeout(showMe, _delay);
			
			visible = true;
			
			//通知显示渲染完成
			dispatchEvent(new Event(Event.INIT));
		}
		
		private function setTxtAutoSize():void {
			if (txtMc) 
			{
				txtMc.txt.multiline = false;
				txtMc.txt.autoSize = "left";
			}
		}
		
		private function showMe():void {
			x = toX;
			alpha = 0;
			if (now) 
			{
				y = toY;
				TweenLite.to(this, .5, { y:"-40",alpha:1, ease:Circ.easeOut, onComplete:mvGo } );
			}else {
				
				//y = toY + 25;
				y = toY+40;
				TweenLite.to(this, .5, { y:toY, alpha:1, ease:Circ.easeOut, onComplete:mvGo } );
			}
			
			
			//TweenLite.to(this, .5, { y:toY, alpha:1, ease:Circ.easeOut,onComplete:onComplete, ease:Circ.easeOut } );
			
			
			
		}
		
		private function mvGo():void {
			var curP:Point = new Point(x, y);
			
			if (toPoint) {
				var btw:Number = Point.distance(curP, toPoint);
				var mvTime:Number = btw / 600;
				var bezerX:Number = toPoint.x > curP.x ? toPoint.x - btw : toPoint.x + btw;
				var bezerY:Number = y - btw / 5;
				TweenMax.to(this, mvTime, {   x:toPoint.x, y:toPoint.y,bezier:[{x:bezerX,y:bezerY},], ease:Circ.easeOut,onComplete:onComplete } );
				//TweenMax.to(this, .3, {  delay:mvTime, tint:0xffffff,yoyo:true,repeat:1, onComplete:onComplete, ease:Circ.easeOut } );
			}else {
				TweenLite.to(this, .5, { y:"-25" } );
				TweenLite.to(this, .5, { delay:.5, alpha:0,  onComplete:onComplete, ease:Circ.easeOut } );
			}
			
		}
		
		private function onComplete():void
		{
			var maininfo:MainInfoView = DisplayManager.uiSprite.getModule(ModuleDict.MODULE_MAININFO) as MainInfoView;
			maininfo.flashValue(type, value);
			parent.removeChild(this);
		}
		
	}

}