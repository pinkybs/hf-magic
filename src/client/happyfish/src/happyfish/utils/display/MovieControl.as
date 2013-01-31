package happyfish.utils.display
{
	import flash.display.MovieClip;
	import flash.events.Event;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class MovieControl 
	{
		
		public function MovieControl(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
				}
			}
			else
			{	
				throw new Error( "MovieControl"+"单例" );
			}
		}
		
		public function checkMovieToEnd(target:MovieClip):void {
			target.addEventListener(Event.ENTER_FRAME, checkEndToRemove);
		}
		
		private function checkEndToRemove(e:Event):void {
			var target:MovieClip = e.target as MovieClip;
			if (target.currentFrame==target.totalFrames) 
			{
				target.removeEventListener(Event.ENTER_FRAME, checkEndToRemove);
				target.dispatchEvent(new Event(Event.COMPLETE));
				target.parent.removeChild(target);
			}
			
		}
		
		public static function getInstance():MovieControl
		{
			if (instance == null)
			{
				instance = new MovieControl( new Private() );
			}
			return instance;
		}
		
		
		private static var instance:MovieControl;
		
	}
	
}
class Private {}