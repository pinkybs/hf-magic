package happyfish.display.ui.events 
{
	import flash.events.Event;
	import happyfish.display.ui.GridItem;
	
	/**
	 * ...
	 * @author jj
	 */
	public class GridPageEvent extends Event 
	{
		public static const	ITEM_SELECT:String = "itemSelect";
		
		public var data:*;
		public var item:GridItem;
		public function GridPageEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new GridPageEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("GridPageEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}