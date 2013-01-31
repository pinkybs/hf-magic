package happyfish.scene.personAction.control 
{
	import flash.events.EventDispatcher;
	import happyfish.events.TriggerEvent;
	
	/**
	 * ...
	 * @author jj
	 */
	public class TriggerControl extends EventDispatcher
	{
		private var triggers:Object=new Object();
		public function TriggerControl(access:Private) 
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
				throw new Error( "TriggerControl"+"单例" );
			}
		}
		
		public static function getInstance():TriggerControl
		{
			if (instance == null)
			{
				instance = new TriggerControl( new Private() );
			}
			return instance;
		}
		
		public function addTrigger(name:String, triggerKey:uint,value:*=null):void {
			if (triggers[name]) trace("this trigger has existed:",name);
			triggers[name] = { triggerKey:triggerKey,state:0,value:value };
		}
		
		public function changeTrigger(name:String, change:int=1):void {
			if (triggers[name]) {
				triggers[name].state += change;
				checkTrigger(name);
			}
		}
		
		public function clearTrigger(name:String):void {
			if (triggers[name]) {
				triggers[name] = null;
			}
		}
		
		public function finishTrigger(name:String):void {
			if (triggers[name]) {
				dispatchEvent(new TriggerEvent(TriggerEvent.TRIGGER_COMPLETE, name,triggers[name].value));
				triggers[name] = null;
			}
		}
		
		public function resetTrigger(name:String):void {
			if (triggers[name]) {
				triggers[name].state = 0;
			}
		}
		
		private function checkTrigger(name:String):void
		{
			if (triggers[name].state>=triggers[name].triggerKey) 
			{
				finishTrigger(name);
			}
		}
		
		public function getTriggerState(name:String):uint {
			return triggers[name].state;
		}
		
		public function hasTrigger(name:String):Boolean {
			if (triggers[name]) return true;
			return false;
		}
		
		
		private static var instance:TriggerControl;
		
	}
	
}
class Private {}