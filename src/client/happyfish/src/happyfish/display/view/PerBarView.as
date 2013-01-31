package happyfish.display.view 
{
	import com.greensock.TweenLite;
	import flash.display.MovieClip;
	import flash.events.Event;
	/**
	 * ...
	 * @author jj
	 */
	public class PerBarView
	{
		public var _view:MovieClip;
		private var maxW:Number;
		private var _maxValue:uint;
		private var value:Number;
		private var per:Number;
		private var barView:MovieClip;
		public var minW:uint = 0;
		public var tweenTime:uint = 1;
		public function PerBarView(ui:MovieClip,_maxW:Number,__maxValue:uint=100) 
		{
			_view = ui;
			maxW = _maxW;
			_maxValue = __maxValue;
			
			if (_view["bar"]) 
			{
				barView = _view["bar"] as MovieClip;
			}else {
				barView = _view;
			}
			
			barView.width = 1;
		}
		
		public function setData(_value:uint):void {
			
			value = Math.min(_value,maxValue);
			per = value / maxValue;
			//_view.width = Math.max(Math.floor(maxW * per),minW);
			
			if (_view["txt"]) 
			{
				_view["txt"].text = _value.toString();
			}
			
			var toWidth:Number = Math.max(Math.floor(maxW * per), minW);
			TweenLite.to(barView,tweenTime,{width:toWidth});
		}
		
		public function get maxValue():uint { return _maxValue; }
		
		public function set maxValue(_value:uint):void 
		{
			_maxValue = _value;
			setData(value);
		}
		
		
		
	}

}