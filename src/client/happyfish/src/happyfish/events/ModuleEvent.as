package happyfish.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author jj
	 */
	public class ModuleEvent extends Event 
	{
		public static const MODULE_OPEN:String = "moduleOpen";
		public static const MODULE_INIT_COMPLETE:String = "moduleInitComplete";
		public static const MODULE_CLOSE:String = "moduleClose";
		
		public var moduleName:String;
		public function ModuleEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new ModuleEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("ModuleEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}