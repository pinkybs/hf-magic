package happyfish.utils.display 
{
	import flash.events.TimerEvent;
	import flash.geom.Rectangle;
	import flash.text.TextField;
	import flash.utils.Timer;
	/**
	 * ...
	 * @author jj
	 */
	public class TextFieldTools
	{
		private var data:String;
		private var txt:TextField;
		private var timer:Timer;
		private var curIndex:Number;
		private var autoCenter:Boolean;
		private var rect:Rectangle;
		public var typeEnd:Boolean;
		
		public function TextFieldTools( _autoCenter:Boolean=false) 
		{
			autoCenter = _autoCenter;
			
		}
		
		public function typeEffect(textField:TextField, str:String, delaytime:uint = 300 ):void {
			typeEnd = false;
			data = str;
			txt = textField;
			curIndex = 0;
			txt.text = "";
			
			if (autoCenter && !rect) 
			{
				rect = textField.getRect(textField.parent);
			}
			
			
			timer = new Timer(delaytime);
			timer.addEventListener(TimerEvent.TIMER, timerFun);
			timer.start();
			
			timerFun();
		}
		
		private function timerFun(e:TimerEvent=null):void 
		{
			txt.appendText(data.slice(curIndex, curIndex + 1));
			if (autoCenter) {
				txt.x = rect.left + rect.width / 2 - txt.textWidth / 2;
				txt.y = rect.top + rect.height / 2 - txt.textHeight / 2;
			}
			curIndex++;
			if (curIndex==data.length) 
			{
				stopTimer();
			}
		}
		
		public function stopTimer(toEnd:Boolean=false):void
		{
			timer.removeEventListener(TimerEvent.TIMER, timerFun);
			timer.stop();
			if (toEnd) txt.text = data;
			
			typeEnd = true;
		}
		
	}

}