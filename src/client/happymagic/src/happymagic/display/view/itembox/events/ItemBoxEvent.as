package happymagic.display.view.itembox.events 
{
	import flash.events.Event;
	import happymagic.display.view.itembox.ItemBoxItemView;
	import happymagic.model.vo.ItemVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class ItemBoxEvent extends Event 
	{
		public static const ITEM_CLICK:String = "itemClick";
		public static const SHOW_ITEMBOX:String = "showItemBox";
		public static const HIDE_ITEMBOX:String = "hideItemBox";
		public static const REFRESH_CUR_PAGE:String = "refreshCurPage";
		
		public var item:ItemBoxItemView;
		public function ItemBoxEvent(type:String,_item:ItemBoxItemView=null, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			item = _item;
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new ItemBoxEvent(type,item, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("ItemBoxEvent", "type","item", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}