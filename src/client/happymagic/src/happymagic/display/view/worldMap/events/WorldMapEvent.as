package happymagic.display.view.worldMap.events 
{
	import flash.events.Event;
	import happymagic.display.view.worldMap.WorldMapSceneIconView;
	
	/**
	 * ...
	 * @author jj
	 */
	public class WorldMapEvent extends Event 
	{
		public static const SCENEICON_CLICK:String = "sceneClick";
		public var scene:WorldMapSceneIconView;
		public function WorldMapEvent(type:String, _scene:WorldMapSceneIconView, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			scene = _scene;
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new WorldMapEvent(type,scene, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("WorldMapEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}