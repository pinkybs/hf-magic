package happymagic.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author jj
	 */
	public class FriendsEvent extends Event 
	{
		public static const SHOW_FRIENDS_VIEW:String = "showFriendsView";
		public static const HIDE_FRIENDS_VIEW:String = "hideFriendsView";
		public static const	FRIENDS_DATA_COMPLETE:String = "friendsDataComplete";
		public function FriendsEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new FriendsEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("FriendsEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}