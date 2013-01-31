package happymagic.display.view.magicBook 
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import happymagic.display.view.magicBook.TransInfoView;
	import happymagic.display.view.magicBook.TransListView;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author jj
	 */
	public class TransMagicView extends Sprite
	{
		private var infoView:TransInfoView;
		private var list:TransListView;
		
		public function TransMagicView() 
		{
			x = -238;
			y = -173;
			
			infoView = new TransInfoView();
			addChild(infoView);
			
			list = new TransListView(new defaultListUi(), this);
			list.x = 300;
			list.y = 296;
			list.iview.addEventListener(MouseEvent.CLICK, item_select, true);
			list.init(250, 261, 60, 60, -26, -286, "C", "TL");
			
			setData();
		}
		
		private function item_select(e:MouseEvent):void 
		{
			if (e.target is transMagicItemUi) 
			{
				infoView.setData((e.target.control as TransMagicItemView).data);
			}
		}
		
		public function setData():void {
			
			list.setData(DataManager.getInstance().transMagicClass);
			infoView.setData(list.data[0]);
		}
		
	}

}