package happymagic.display.view.itembox 
{
	import com.greensock.TweenLite;
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.IconView;
	import happyfish.utils.display.ItemOverControl;
	import happyfish.utils.HtmlTextTools;
	import happymagic.display.view.itembox.events.ItemShopEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.ItemClassVo;
	import happymagic.model.vo.MoneyType;
	
	/**
	 * ...
	 * @author jj
	 */
	public class ItemShopItemView extends GridItem
	{
		public var data:ItemClassVo;
		private var iview:newshopitemui;
		private var icon:IconView;	
		
		public function ItemShopItemView(uiview:MovieClip) 
		{
			super(uiview);
			iview = uiview as newshopitemui;
			
			iview.mouseChildren = false;
			iview.buttonMode = true;
			iview.addEventListener(MouseEvent.CLICK, clickFun);
			ItemOverControl.getInstance().addOverItem(iview);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			iview.dispatchEvent(new ItemShopEvent(ItemShopEvent.ITEM_CLICK, data));
		}
		
		
		override public function setData(value:Object):void 
		{
			data = value as ItemClassVo;
			
			iview.nameTxt.text = data.name;
			
			var type:uint;
			var num:uint;
			if (data.coin) 
			{
				type = MoneyType.COIN;
				num = data.coin;
			}else {
				type = MoneyType.getPriceType(data);
				num = MoneyType.getPriceNum(data);
			}
			
			
			if (DataManager.getInstance().getEnouthCrystalType(type,num)) 
			{
				HtmlTextTools.setTxtSaveFormat(iview.priceTxt, num.toString(), 0x000000);
			}else {
				HtmlTextTools.setTxtSaveFormat(iview.priceTxt, num.toString(), 0xff0000);
			}
			
			
			Tooltips.getInstance().register(iview, data.content, Tooltips.getInstance().getBg("defaultBg"));
			iview.priceTypeIcon.gotoAndStop(type);
			iview.priceTypeIcon.scaleX = 0.8;
			iview.priceTypeIcon.scaleY = 0.8;
			
			loadIcon();
		}
		
		private function loadIcon():void
		{
			icon = new IconView(67, 72, new Rectangle(19, 33, 67, 72));
			icon.setData(data.class_name);
			iview.addChildAt(icon,1);
		}
		
	}

}