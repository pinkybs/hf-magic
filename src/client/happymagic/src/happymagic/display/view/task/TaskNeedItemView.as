package happymagic.display.view.task 
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.IconView;
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.HtmlTextTools;
	import happymagic.display.view.ui.NeedIconView;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.ItemClassVo;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.MagicClassVo;
	import happymagic.model.vo.MixMagicVo;
	import happymagic.model.vo.TransMagicVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class TaskNeedItemView extends GridItem
	{
		private var iview:MovieClip;
		private var size:Number;
		private var rect:Rectangle;
		public var showEnough:Boolean;
		public var data:ConditionVo;
		public var showNeed:Boolean;
		public var currentNum:uint;
		public function TaskNeedItemView(_ui:MovieClip, _size:Number, _rect:Rectangle,__showEnough:Boolean=false) 
		{
			showEnough = __showEnough;
			size = _size;
			rect = _rect;
			super(_ui);
			iview = _ui;
		}
		
		override public function setData(value:Object):void {
			
			
			data = value as ConditionVo;
			
			currentNum = data.currentNum;
			
			var needDecor:DecorClassVo;
			var needItem:ItemClassVo;
			var magicclass:MagicClassVo;
			var mixvo:MixMagicVo;
			var transvo:TransMagicVo;
			
			var body:Sprite;
			var enough:Boolean;
			var currentNum:uint;
			
			var showName:String="";
			switch (data.type) 
			{
				case ConditionType.DECOR:
				needDecor = DataManager.getInstance().getDecorClassByDid(uint(data.id));
				body = new IconView(size, size, rect) as Sprite;
				(body as IconView).setData(needDecor.class_name);
				showName=needDecor.name;
				//enough = DataManager.getInstance().getEnoughDecors([[data.id, data.num]]);
				//currentNum = DataManager.getInstance().getDecorNum(uint(data.id));
				break;
				
				case ConditionType.ITEM:
				needItem = DataManager.getInstance().getItemClassByIid(uint(data.id));
				body = new IconView(size, size, rect) as Sprite;
				(body as IconView).setData(needItem.class_name);
				showName = needItem.name;
				iview["numTxt"].text = data.num.toString();
				//enough = DataManager.getInstance().getEnoughItems([[uint(data.id), data.num]]);
				//currentNum = DataManager.getInstance().getItemNum(uint(data.id));
				break;
				
				case ConditionType.USER:
				//body = new conditionNeedIcon() as Sprite;
				body = new IconView(size, size, rect) as Sprite;
				(body as IconView).setData("conditionNeedIcon",data.id);
				showName = LocaleWords.getInstance().getWord("conditionName_" + data.id);
				iview["numTxt"].text = "+" + data.num.toString();
				break;
				
				case ConditionType.MAGIC_CLASS:
				magicclass = DataManager.getInstance().getMagicClass(uint(data.id));
				body = new IconView(size, size, rect) as Sprite;
				(body as IconView).setData(magicclass.class_name);
				showName = magicclass.name;
				break;
				
				case ConditionType.MIX:
				mixvo = DataManager.getInstance().getMixMagicByMid(uint(data.id));
				body = new IconView(size, size, rect) as Sprite;
				var tmpitem:ItemClassVo = DataManager.getInstance().getItemClassByIid(mixvo.d_id);
				(body as IconView).setData(tmpitem.class_name);
				showName = mixvo.name;
				break;
				
				case ConditionType.TRANS:
				transvo = DataManager.getInstance().getTransMagicClassByTid(uint(data.id));
				body = new IconView(size, size, rect) as Sprite;
				(body as IconView).setData(transvo.class_name);
				showName = transvo.name;
				break;
				
				case ConditionType.SCENE_UPGRADE:
				body = new IconView(size, size, rect) as Sprite;
				(body as IconView).setData("sceneUpgradeIcon");
				LocaleWords.getInstance().getWord("roomUp");
				iview["numTxt"].text = data.num.toString() + "x" + data.num.toString();
				break;
				
			}
			
			if (iview["nameTxt"]) iview["nameTxt"].text = showName;
			
			enough = currentNum >= data.num;
			
			var outStr:String;
			if (showNeed) 
			{
				outStr = currentNum.toString() + "/" + data.num.toString();
			}else {
				//outStr = data.num.toString();
				outStr = iview["numTxt"].text;
			}
			
			if (enough || !showEnough) 
			{
				HtmlTextTools.setTxtSaveFormat(iview["numTxt"],outStr,0x000000);
			}else {
				HtmlTextTools.setTxtSaveFormat(iview["numTxt"],outStr,0xff0000);
			}
			
			iview.addChildAt(body, 1);
			
			if(showName) Tooltips.getInstance().register(iview, showName, Tooltips.getInstance().getBg("defaultBg"));
		}
		
	}

}