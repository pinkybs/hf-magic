package happymagic.display.view.itembox.events 
{
	import flash.events.Event;
	import happymagic.model.vo.ItemClassVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class ItemShopEvent extends Event 
	{
		public static const ITEM_CLICK:String = "itemShopItemClick";
		
		public var item:ItemClassVo;
		public function ItemShopEvent(type:String,_item:ItemClassVo, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			item = _item;
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new ItemShopEvent(type,item, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("ItemShopEvent", "type","item", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}