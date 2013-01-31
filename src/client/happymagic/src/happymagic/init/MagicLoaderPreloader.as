package happymagic.init
{
	import com.greensock.easing.Expo;
	import com.greensock.TweenLite;
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.display.StageAlign;
	import flash.display.StageScaleMode;
	import flash.events.Event;
	import flash.events.ProgressEvent;
	import flash.net.URLRequest;
	import flash.utils.getDefinitionByName;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class MagicLoaderPreloader extends MovieClip 
	{
		private var outTimeId:uint;
		private var takeError:Boolean;
		private var requestbak:URLRequest;
		public var retry:Boolean = false;
		
		public function MagicLoaderPreloader() 
		{
			stage.scaleMode = StageScaleMode.NO_SCALE;
			stage.align = StageAlign.TOP_LEFT;
			
			addEventListener(Event.ENTER_FRAME, checkFrame);
			loaderInfo.addEventListener(ProgressEvent.PROGRESS, progress);
			
			TweenLite;
			Expo;
			// show loader
		}
		
		private function progress(e:ProgressEvent):void 
		{
			// update loader
		}
		
		private function checkFrame(e:Event):void 
		{
			if (currentFrame == totalFrames) 
			{
				removeEventListener(Event.ENTER_FRAME, checkFrame);
				startup();
			}
		}
		
		private function startup():void 
		{
			// hide loader
			stop();
			loaderInfo.removeEventListener(ProgressEvent.PROGRESS, progress);
			var mainClass:Class = getDefinitionByName("happymagic.init.MagicLoaderMain") as Class;
			addChild(new mainClass() as DisplayObject);
			
		}
		
	}
	
}