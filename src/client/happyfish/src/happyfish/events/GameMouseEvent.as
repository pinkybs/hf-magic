package happyfish.events 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.scene.world.grid.IsoItem;
	/**
	 * ...
	 * @author Beck
	 */
	public class GameMouseEvent extends Event
	{
        public var item:IsoItem;
        public var itemType:String;
        public var mouseEventType:String;
		public var data:Object;
        public static const GAME_MOUSE_EVENT:String = "game mouse event";
        public static const OVER:String = "Over";
        public static const OUT:String = "Out";
        public static const CLICK:String = "Click";
		public var mouseEvent:MouseEvent;


        public function GameMouseEvent($mouse_event_type:String, $item:IsoItem, $item_type:String, $event:MouseEvent = null, $data:Object = null, bubbles:Boolean=true, cancelable:Boolean=false)
        {
            super(GAME_MOUSE_EVENT, bubbles, cancelable);
            this.item = $item;
            this.itemType = $item_type;
            this.mouseEventType = $mouse_event_type;
			this.data = $data;
			this.mouseEvent = $event;
            return;
        }
		
	}

}