package happyfish.actModule.giftGetAct.view.myWish 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happyfish.actModule.giftGetAct.model.vo.GiftMyWishVo;
	import happyfish.actModule.giftGetAct.view.current.CurrentItemView;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	/**
	 * ...
	 * @author ZC
	 */
	public class MyWishItemView extends GridItem
	{
		
		private var iview:MyWishItemViewUi;
		public var data:GiftMyWishVo;
		private var classname:String;
		private var name:String;

		public function MyWishItemView(_uview:MovieClip) 
		{
			super(_uview);
			iview = _uview as MyWishItemViewUi;
			iview.noselectstate.visible = false;
			iview.DeleteBtn.visible = false;
	        iview.mouseChildren = true;
		}
				
		override public function setData(vaule:Object):void
		{
			data = vaule as GiftMyWishVo;
			
			if (data.id =="0")
			{
				iview.noselectstate.visible = true;
			}
			else
			{
			    iview.DeleteBtn.visible = true;					
				loadicon();
			}
			

		}
		
		private function loadicon():void 
		{
			var icon:IconView = new IconView(50, 50, new Rectangle(22, 5, 50, 50));
			icon.setData(data.className);
			iview.addChild(icon);
			GiftDomain.getInstance().showTips(icon, data.name);
		}
		
		
	}

}