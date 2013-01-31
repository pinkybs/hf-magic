package happymagic.display.view.magicBook 
{
	import flash.display.MovieClip;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happyfish.utils.display.BtnStateControl;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.TransMagicVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class TransMagicItemView extends GridItem
	{
		private var iview:transMagicItemUi;
		private var icon:IconView;
		public var data:TransMagicVo;
		
		public function TransMagicItemView(uiview:MovieClip) 
		{
			super(uiview);
			iview = uiview as transMagicItemUi;
			
			iview.lockIcon.visible=
			iview.needLevelTxt.visible = false;
		}
		
		override public function setData(value:Object):void 
		{
			data = value as TransMagicVo;
			
			if (DataManager.getInstance().hasLearnTrans(data.trans_mid)) 
			{
				//已学
				loadIcon(true);
			}else {
				//未学
				if (data.needLevel>DataManager.getInstance().currentUser.level) 
				{
					iview.mouseEnabled = false;
					//不够等级
					iview.lockIcon.visible =
					iview.needLevelTxt.visible = true;
					iview.needLevelTxt.text = "LV"+data.needLevel.toString();
				}else {
					iview.mouseEnabled = true;
					loadIcon(false);
				}
			}
			
		}
		
		private function loadIcon(hasLearn:Boolean):void
		{
			icon = new IconView(40, 40,new Rectangle(-27,-27,54,54));
			icon.setData(data.class_name);
			iview.addChildAt(icon, 1);
			if (!hasLearn) 
			{
				BtnStateControl.setBtnState(icon, false,true);
			}
		}
	}

}