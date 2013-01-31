package happymagic.display.view.magicBook 
{
	import flash.display.MovieClip;
	import flash.filters.ColorMatrixFilter;
	import flash.geom.Rectangle;
	import happyfish.utils.display.ItemOverControl;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happyfish.utils.HtmlTextTools;
	import happymagic.display.view.ui.DecorIconView;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.MixMagicVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class MixMagicItemView extends GridItem
	{
		public var data:MixMagicVo;
		private var iview:mixMagicItemUi;
		private var icon:IconView;
		private var decor:DecorClassVo;
		public function MixMagicItemView(uiview:MovieClip) 
		{
			super(uiview);
			iview = uiview as mixMagicItemUi;
			
			//iview.notEnoughTxt.visible=
			iview.lockIcon.visible=
			iview.needLevelTxt.visible=
			iview.addMpTxt.visible=
			iview.starIcon.visible = 
			iview.magicIconMc.visible = false;
			iview.starIcon.gotoAndStop(1);
			ItemOverControl.getInstance().addOverItem(iview, ItemOverControl.getInstance().showGlow, ItemOverControl.getInstance().hideGlow,true);
		}
		
		override public function setData(value:Object):void 
		{
			data = value as MixMagicVo;
			decor = DataManager.getInstance().getDecorClassByDid(data.d_id);

			HtmlTextTools.setTxtSaveFormat(iview.nameTxt, data.name);
			if (!decor) 
			{
				
				return;
			}
			if (data.needLevel>DataManager.getInstance().currentUser.level) 
			{
				//等级不足
				iview.lockIcon.visible=
				iview.needLevelTxt.visible = true;
				iview.needLevelTxt.text = "LV" + data.needLevel.toString();
			}else {
				iview.addMpTxt.visible=
				iview.starIcon.visible = 
				iview.magicIconMc.visible = true;
				
				iview.addMpTxt.text = "+" + decor.max_magic;
				iview.starIcon.gotoAndStop(decor.level);
			}

			loadIcon();
		}
		
		private function loadIcon():void
		{
			icon = new IconView(50, 55,new Rectangle(11,2,50,55));
			icon.setData(decor.class_name);
			iview.addChildAt(icon, 0);
			
			if (data.needLevel>DataManager.getInstance().currentUser.level) 
			{
				var mat:Array = [  1,0,0,0,-50,
							   0,1,0,0,-50,
							   0,0,1,0,-50,
							   0,0,0,1,0 ];
				icon.filters = [new ColorMatrixFilter(mat)];
			}
		}
		
		
		
	}

}