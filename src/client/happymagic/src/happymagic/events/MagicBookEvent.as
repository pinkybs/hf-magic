package happymagic.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author jj
	 */
	public class MagicBookEvent extends Event 
	{
		public static const SHOW_MAGICBOOK:String = "showMagicBook";
		public static const MAGICBOOK_CLOSED:String = "magicBookClosed";
		
		public static const CHANGE_MAGIC_TYPE:String = "changeMagicType";
		public static const SHOW_CHANGE_MAGIC_TYPE:String = "showChangeMagicType";
		
		public static const OPENTAB_RED:uint = 0;
		public static const OPENTAB_BLUE:uint = 1;
		public static const OPENTAB_GREEN:uint = 2;
		public static const OPENTAB_TRANS:uint = 3;
		
		
		public var data:*;
		public var openTab:uint=0;
		public function MagicBookEvent(type:String,_openTab:uint=0, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			openTab = _openTab;
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new MagicBookEvent(type,openTab, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("MagicBookEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}