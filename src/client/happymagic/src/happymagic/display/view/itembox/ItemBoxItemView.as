package happymagic.display.view.itembox 
{
	import com.greensock.TweenLite;
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.IconView;
	import happyfish.manager.EventManager;
	import happyfish.utils.HtmlTextTools;
	import happymagic.display.view.itembox.events.ItemBoxEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.ItemType;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.MoneyType;
	
	/**
	 * ...
	 * @author jj
	 */
	public class ItemBoxItemView extends GridItem
	{
		private var iview:itemBoxItemUi;
		private var icon:IconView;
		public var data:ItemVo;
		
		public function ItemBoxItemView(uiview:MovieClip) 
		{
			super(uiview);
			iview = uiview as itemBoxItemUi;
			
			iview.mouseChildren = false;
			iview.buttonMode = true;
			iview.addEventListener(MouseEvent.CLICK, clickFun);
			iview.addEventListener(MouseEvent.MOUSE_OVER, overFun);
			iview.addEventListener(MouseEvent.MOUSE_OUT, outFun);
			
			
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			iview.dispatchEvent(new ItemBoxEvent(ItemBoxEvent.ITEM_CLICK, this));
		}
		
		private function outFun(e:MouseEvent):void 
		{
			TweenLite.to(iview, .2, { y: 0 } );
		}
		
		private function overFun(e:MouseEvent):void 
		{
			TweenLite.to(iview, .2, { y: -6 } );
		}
		
		override public function setData(value:Object):void 
		{
			data = value as ItemVo;
			
			iview.nameTxt.text = data.name;
			iview.numTxt.text = "x" + data.num.toString();
			
			if (data.type!=ItemType.FOOD && data.type!=ItemType.SOLUTION) 
			{
				iview.buttonMode = false;
				iview.mouseEnabled = false;
				iview.useBtn.visible = false;
			}
			
			loadIcon();
			Tooltips.getInstance().register(iview, data.content, Tooltips.getInstance().getBg("itemBoxTipsUi"));
		}
		
		private function loadIcon():void
		{
			if (!icon) 
			{
				icon = new IconView(60, 60, new Rectangle(4, 11, 72, 72));
				icon.setData(data.class_name);
				iview.addChildAt(icon, 1);
			}
		}
		
	}

}