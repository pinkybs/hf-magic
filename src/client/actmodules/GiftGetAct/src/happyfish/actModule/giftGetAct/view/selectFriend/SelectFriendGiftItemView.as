package happyfish.actModule.giftGetAct.view.selectFriend 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	/**
	 * ...
	 * @author ZC
	 */
	public class SelectFriendGiftItemView extends GridItem
	{
		private var iview:FreeSendItemUi;
		private var data:Object;
		private var classname:String;
		private var itemname:String;
		public function SelectFriendGiftItemView(_uview:MovieClip) 
		{
			super(_uview);
			iview = _uview as FreeSendItemUi;
			iview.graybackground.visible = false;
			iview.lockui.visible = false;
			iview.yesbtn.visible = false;
			iview.redbackground.visible = true;		
		}
		
		
		override public function setData(value:Object):void 
		{
			data = value;
			
			iview.nametxt.text = data.name;
			
			loadicon()
		}
		
		private function loadicon():void 
		{
			var icon:IconView = new IconView(55, 50, new Rectangle(25,25,55, 50));
			icon.setData(data.className);
			iview.addChild(icon);
		}
		
	}

}