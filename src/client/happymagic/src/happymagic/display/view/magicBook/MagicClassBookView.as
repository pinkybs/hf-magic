package happymagic.display.view.magicBook 
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import happymagic.display.view.magicBook.MagicClassBookListView;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.MagicType;
	
	/**
	 * ...
	 * @author jj
	 */
	public class MagicClassBookView extends Sprite
	{
		public var type:uint;
		private var infoView:MagicClassInfoView;
		private var list:MagicClassBookListView;
		
		public function MagicClassBookView() 
		{
			x = -238;
			y = -173;
			
			infoView = new MagicClassInfoView();
			addChild(infoView);
			
			list = new MagicClassBookListView(new defaultListUi(), this);
			list.x = 300;
			list.y = 296;
			list.iview.addEventListener(MouseEvent.CLICK, item_select, true);
			list.init(250, 261, 60, 60, -26, -286, "C", "TL");
			
			
		}
		
		private function item_select(e:MouseEvent):void 
		{
			if (e.target is magicClassItemUi) 
			{
				infoView.setData((e.target.control as MagicClassBookItemView).data);
			}
		}
		
		public function setData(__type:uint):void {
			type = __type;
			list.setData(DataManager.getInstance().magicClass, "magic_type", type);
			infoView.setData(list.data[0]);
		}
		
	}

}