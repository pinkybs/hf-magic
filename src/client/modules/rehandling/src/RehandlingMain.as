package 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.net.registerClassAlias;
	import happyfish.display.view.UISprite;
	import happymagic.command.RehandlingInitCommand;
	import happymagic.command.RehandlingInitstaticCommand;
	import happymagic.display.view.rehandling.RehandlingView;
	
	/**
	 * ...
	 * @author zc
	 */
	public class RehandlingMain extends Sprite 
	{
		
		public function RehandlingMain():void 
		{
			if (stage) init();
			else addEventListener(Event.ADDED_TO_STAGE, init);
		}
		
		private function init(e:Event = null):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, init);
			
			registerClassAlias("RehandlingView2", RehandlingView);
			

			
		}
		
	}
	
}