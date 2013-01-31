package happymagic.display.view.ui 
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import flash.text.TextFormat;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.IconView;
	import happyfish.utils.HtmlTextTools;
	import happymagic.display.view.itembox.BuyItemMsgView;
	import happymagic.display.view.magicBook.CrystalNumView;
	import happymagic.display.view.magicBook.event.MixMagicEvent;
	import happymagic.display.view.ModuleDict;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.ItemClassVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class NeedIconView extends Sprite
	{
		private var body:MovieClip;
		private var maxWidth:Number;
		private var maxHeight:Number;
		public var showEnough:Boolean;
		public var showSwitchView:Boolean;//一个感叹号的UI是否要显示
		public var data:*;
		public var num:uint;
		public var type:uint;
		public var times:uint;
		private var colonui:colonUI;
		
		public static const CRYSTAL:uint = 1;
		public static const DECOR:uint = 2;
		public static const ITEM:uint = 3;
		
		
		public function NeedIconView(_w:Number=55,_h:Number=45,_showEnough:Boolean=true,_showSwitchView:Boolean = false) 
		{
			maxWidth = _w;
			maxHeight = _h;
			showEnough = _showEnough;
			showSwitchView = _showSwitchView
			mouseChildren = false;
			
		}
		
		/**
		 * 
		 * @param	value 	1 2 3 4代表水晶需求, itemClassVo或DecorClassVo代表物品和装饰物
		 */
		public function setData(value:*, _num:uint,_times:uint=1,_uiClass:Class=null):void {
			data = value;
			num = _num;
			times = _times;
		    colonui = new colonUI();
			colonui.y = -20;
			var enough:Boolean;
			
			if (value is uint) 
			{
				body = new CrystalNumView(value, num*times,true);
				addChild(body);
				
				type = NeedIconView.CRYSTAL;
				
				return;
			}
			
			
			
			if (value is DecorClassVo) 
			{
				enough = DataManager.getInstance().getEnoughDecors([[(value as DecorClassVo).d_id, num * times]]);
				
				if (_uiClass) 
				{
					body = new _uiClass() as MovieClip;
				}else {
					body = new needItemDecorUi() as MovieClip;
				}
				
				if (body["nameTxt"]) 
				{
					body["nameTxt"].text = value.name;
				}
				
				body.mouseChildren = false;
				
				if (enough || !showEnough) 
				{
					body.numTxt.text = (num * times).toString();
					if (showSwitchView)
					{
					   if (body.contains(colonui))
					   {
					    body.removeChild(colonui);						
					   }
					}
					
				}
				else 
				{
					if (showSwitchView)
					{
					    if (body.contains(colonui))
					    {
						
					    }
					    else
					    {
						body.addChild(colonui);
					    }						
					}					
					body.numTxt.htmlText = HtmlTextTools.redWords((num*times).toString());
				}
				
				var decorIcon:IconView = new IconView( maxWidth, maxHeight,new Rectangle(-maxWidth/2,-maxWidth,maxWidth,maxWidth));
				decorIcon.setData(value.class_name);
				body.addChildAt(decorIcon, 0);
				addChild(body);
				type = 2;
				
				type = NeedIconView.DECOR;
				
				Tooltips.getInstance().register(body, (value as DecorClassVo).name, Tooltips.getInstance().getBg("defaultBg"));
			}
			
			if (value is ItemClassVo) 
			{
				enough = DataManager.getInstance().getEnoughItems([[(value as ItemClassVo).i_id, num * times]]);
				
				if (_uiClass) 
				{
					body = new _uiClass() as MovieClip;
				}else {
					body = new needItemDecorUi() as MovieClip;
				}
				
				if (body["nameTxt"]) 
				{
					body["nameTxt"].text = value.name;
				}
				
				if (body["numTxt"]) 
				{
					body["numTxt"].text = "x"+_num.toString();
				}
				
				body.mouseChildren = false;
				if (enough) 
				{
					body.numTxt.text = (num * times).toString();
					
					if (showSwitchView)
					{
					   if (body.contains(colonui))
					   {
					    body.removeChild(colonui);						
					   }
					}
					
				}else {
					body.numTxt.htmlText = HtmlTextTools.redWords((num * times).toString());
					   if (showSwitchView)
					    {
					       if (body.contains(colonui))
					       {
						
					       }
					       else
					       {
					        	body.addChild(colonui);
					       }						
					}	
				}
				
				var itemIcon:IconView = new IconView( maxWidth, maxHeight,new Rectangle(-maxWidth/2,-maxWidth,maxWidth,maxWidth));
				body.addChildAt(itemIcon, 0);
				itemIcon.setData(value.class_name);
				addChild(body);
				
				type = NeedIconView.ITEM;
				
				Tooltips.getInstance().register(body, (value as ItemClassVo).name, Tooltips.getInstance().getBg("defaultBg"));
			}
			
			
		}
		
		public function setTimes(value:uint):void {
			times = value;
			var enough:Boolean;
			if (type==NeedIconView.CRYSTAL) 
			{
				body.setNum(num*times);
			}else {
				if (type==NeedIconView.DECOR) 
				{
					enough = DataManager.getInstance().getEnoughDecors([[(data as DecorClassVo).d_id,num*times]]);
				}else if(type==NeedIconView.ITEM){
					enough = DataManager.getInstance().getEnoughItems([[(data as ItemClassVo).i_id,num*times]]);
				}
				if (enough) 
				{
					body.numTxt.text = (num*times).toString();
				}else {
					body.numTxt.htmlText = HtmlTextTools.redWords((num*times).toString());
				}
				
			}
			
			
		}
		
	}

}