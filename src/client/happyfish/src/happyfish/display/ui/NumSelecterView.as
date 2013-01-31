package happyfish.display.ui 
{
	import flash.display.MovieClip;
	import flash.display.SimpleButton;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.MouseEvent;
	import flash.text.TextField;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	
	/**
	 * 数量显示组件
	 * 可 增加\减少\输入 数量
	 * @author jj
	 */
	public class NumSelecterView extends EventDispatcher
	{
	
		public var num:uint;
		private var _minNum:uint;
		private var _maxNum:uint;
		private var checkTimeId:uint;
		private var changeing:Boolean;
		
		public var view:MovieClip;
		public var checkDelay:uint;
		private var numTxt:TextField;
		private var addButton:SimpleButton;
		private var subButton:SimpleButton;
		
		public function NumSelecterView(__uiview:MovieClip,startNum:uint=1,maxLength:uint=5) 
		{
			view = __uiview;
			
			view.addEventListener(MouseEvent.CLICK, clickFun, true);
			numTxt = view.numTxt;
			num = startNum;
			
			minNum = 1;
			maxNum = uint.MAX_VALUE;
			
			
			addButton = view.addButton;
			subButton = view.subButton;
			
			numTxt.restrict = "0-9";
			numTxt.maxChars = maxLength;
			numTxt.addEventListener(Event.CHANGE, textInput);
		}
		
		public function setNum(value:uint):void {
			num = value;
			checkNum();
		}
		
		
		private function textInput(e:Event):void 
		{
			changeing = true;
			num = Number(numTxt.text);
			if (checkTimeId) 
			{
				clearTimeout(checkTimeId);
			}
			checkTimeId=setTimeout(checkNum,checkDelay);
		}
		
		private function checkNum():void {
			
			num = Math.max(minNum,num);
			num = Math.min(maxNum,num);
			
			numTxt.text = num.toString();
			
			changeing = false;
			
			dispatchEvent(new Event(Event.CHANGE));
		}
		
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case addButton:
					num++;
					checkNum();
				break;
				
				case subButton:
					num--;
					checkNum();
				break;
			}
		}
		
		public function setNumLength(value:uint):void {
			numTxt.maxChars = value;
		}
		
		public function get minNum():uint { return _minNum; }
		
		public function set minNum(value:uint):void 
		{
			_minNum = value;
			checkNum();
		}
		
		public function get maxNum():uint { return _maxNum; }
		
		public function set maxNum(value:uint):void 
		{
			_maxNum = value;
			checkNum();
		}
		
		public function get x():Number { return view.x; }
		
		public function set x(value:Number):void 
		{
			view.x = value;
		}
		
		public function get y():Number { return view.y; }
		
		public function set y(value:Number):void 
		{
			view.y = value;
		}
		
		public function get text():String { return numTxt.text; }
		
		public function set text(value:String):void 
		{
			numTxt.text = value;
		}
		
		
	}

}