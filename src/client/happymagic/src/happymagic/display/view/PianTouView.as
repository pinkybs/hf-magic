package happymagic.display.view 
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	
	/**
	 * ...
	 * @author jj
	 */
	public class PianTouView extends Sprite
	{
		private var piantou:MovieClip;
		
		public function PianTouView(_piantou:MovieClip) 
		{
			piantou = _piantou;
			addChild(piantou);
			
			piantou.addEventListener(MouseEvent.CLICK, clickFun);
			
			addEventListener(Event.ADDED_TO_STAGE, addToStage);
			
			
		}
		
		private function addToStage(e:Event):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, addToStage);
			x = stage.stageWidth / 2;
			y = stage.stageHeight / 2;
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target.name) 
			{
				case "nextBtn":
				if (piantou.currentFrame==piantou.totalFrames) 
				{
					closeMe();
				}else {
					piantou.nextFrame();
				}
				break;
				
				case "prevBtn":
				piantou.prevFrame();
				break;
				
				case "closeBtn":
				closeMe();
				break;
				
				case "enterGameBtn":
				closeMe();
				break;
			}
		}
		
		private function closeMe():void
		{
			piantou.removeEventListener(MouseEvent.CLICK, clickFun);
			
			dispatchEvent(new Event(Event.COMPLETE));
			
			parent.removeChild(this);
			
			
		}
		
	}

}