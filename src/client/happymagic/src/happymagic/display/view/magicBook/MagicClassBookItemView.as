package happymagic.display.view.magicBook 
{
	import flash.display.MovieClip;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happyfish.utils.display.BtnStateControl;
	import happyfish.utils.display.FiltersDomain;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.MagicClassVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class MagicClassBookItemView extends GridItem
	{
		public var data:MagicClassVo;
		private var iview:magicClassItemUi;
		private var icon:IconView;
		
		public function MagicClassBookItemView(uiview:MovieClip) 
		{
			super(uiview);
			iview = uiview as magicClassItemUi;
			
			iview.lockIcon.visible=
			iview.needLevelTxt.visible = false;
		}
		
		override public function setData(value:Object):void 
		{
			data = value as MagicClassVo;
			
			if (!DataManager.getInstance().hasLearnMagicClass(data.magic_id)) 
			{
				if (data.need_level>DataManager.getInstance().currentUser.level) 
				{
					//不够等级
					iview.lockIcon.visible =
					iview.needLevelTxt.visible = true;
					iview.needLevelTxt.text = "LV"+data.need_level.toString();
					
				}else {
					//未学习
					//iview.canLearnTxt.visible = true;
					//icon.filters = [FiltersDomain.grayFilter];
				}
				loadIcon();
			} else {
				loadIcon();
			}
		}
		
		private function loadIcon():void
		{
			icon = new IconView(40, 40,new Rectangle(-27,-27,54,54));
			iview.addChildAt(icon, 1);
			icon.setData(data.class_name);
			
			if (!DataManager.getInstance().hasLearnMagicClass(data.magic_id)) 
			{
				icon.filters = [FiltersDomain.grayFilter];
			}
			
			//if (!DataManager.getInstance().getEnoughCrystal(data.coin,0)) 
			//{
				//水晶不足时
				//BtnStateControl.setBtnState(icon, false);
			//}
		}
		
	}

}