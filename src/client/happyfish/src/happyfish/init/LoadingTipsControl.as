package happyfish.init 
{
	import flash.events.TimerEvent;
	import flash.text.TextField;
	import flash.utils.Timer;
	/**
	 * ...
	 * @author jj
	 */
	public class LoadingTipsControl
	{
		private var txt:TextField;
		private var tips:Array;
		private var timer:Timer;
		private var currentIndex:uint;
		
		public function LoadingTipsControl(_txt:TextField,_tips:Array) 
		{
			txt = _txt;
			tips = _tips;
			
			currentIndex = 0;
		}
		
		public function start():void {
			if (!timer) 
			{
				timer = new Timer(10000);
				timer.addEventListener(TimerEvent.TIMER, changeTipsTimerFun);
			}
			timer.start();
			changeTipsTimerFun();
		}
		
		private function changeTipsTimerFun(e:TimerEvent=null):void 
		{
			currentIndex = Math.floor(Math.random() * (tips.length-1));
			txt.text = tips[currentIndex];
		}
		
		public function stop():void {
			timer.stop();
			timer.removeEventListener(TimerEvent.TIMER, changeTipsTimerFun);
			timer = null;
		}
	}

}