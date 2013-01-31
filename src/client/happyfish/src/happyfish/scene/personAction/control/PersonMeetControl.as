package happyfish.scene.personAction.control 
{
	import flash.events.EventDispatcher;
	
	/**
	 * ...
	 * @author jj
	 */
	public class PersonMeetControl extends EventDispatcher
	{
		
		public function PersonMeetControl(access:Private) 
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
				throw new Error( "PersonMeetControl"+"单例" );
			}
		}
		
		public static function getInstance():PersonMeetControl
		{
			if (instance == null)
			{
				instance = new PersonMeetControl( new Private() );
			}
			return instance;
		}
		
		
		private static var instance:PersonMeetControl;
		
		public function requestMeeting(name:String,value:*=null):void {
			
		}
		
	}
	
}
class Private {}