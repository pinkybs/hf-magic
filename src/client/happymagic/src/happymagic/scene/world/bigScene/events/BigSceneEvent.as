package happymagic.scene.world.bigScene.events 
{
	import flash.display.Sprite;
	import flash.events.Event;
	
	/**
	 * ...
	 * @author jj
	 */
	public class BigSceneEvent extends Event 
	{
		public static const NPC_CLICK:String = "npcClick";
		public static const ENEMY_CLICK:String = "enemyClick";
		
		public var item:Sprite;
		public function BigSceneEvent(type:String,_item:Sprite=null,bubbles:Boolean=true, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			item = _item;
		} 
		
		public override function clone():Event 
		{ 
			return new BigSceneEvent(type,item, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("BigSceneEvents", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}