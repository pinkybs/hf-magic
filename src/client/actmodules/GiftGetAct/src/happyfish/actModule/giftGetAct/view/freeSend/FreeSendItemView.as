package happyfish.actModule.giftGetAct.view.freeSend 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.actModule.giftGetAct.event.GiftGetActEvent;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happyfish.actModule.giftGetAct.model.vo.GiftVo;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happyfish.manager.EventManager;
	/**
	 * ...
	 * @author ZC
	 */
	
	//免费赠送中的列表里所显示的物品
	
	public class FreeSendItemView extends GridItem
	{
		public var data:GiftVo;
		private var iview:FreeSendItemUi;
		public function FreeSendItemView(uview:MovieClip) 
		{
			super(uview)
			iview = uview as FreeSendItemUi;
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			EventManager.getInstance().addEventListener(GiftGetActEvent.SELECT, selectstate);
			
		}
		
		//选择状态
		private function selectstate(e:GiftGetActEvent):void 
		{
		     if (iview.yesbtn.visible)
			 {
				 iview.yesbtn.visible = false;
				 iview.redbackground.visible = false;
			 }
		}
		
		override public function setData(value:Object):void 
		{
		     data = value as GiftVo;
			 iview.graybackground.visible = false;
			 
			 if ((GiftDomain.getInstance().getVar("senditemid") as GiftVo).id == data.id)
			 {
				 iview.yesbtn.visible = true;
				 iview.redbackground.visible = true;
			 }
			 else
			 {
				 iview.yesbtn.visible = false;
				 iview.redbackground.visible = false;
			 }
			 
			 iview.nametxt.text = data.name;	
			 
			 if (data.lockLevel > GiftDomain.getInstance().currentUser.level)
			 {
                 iview.removeEventListener(MouseEvent.CLICK, clickrun);
				 iview.lockui.levelnum.text = String(data.lockLevel);
				 iview.graybackground.visible = true;
			 }
			 else
			 {
				 iview.lockui.visible = false;
			 }
			 loadicon()
		}
		
		private function loadicon():void 
		{
			var iconview:IconView = new IconView(55, 50, new Rectangle(20, 20, 55, 50));
			iconview.setData(data.className);
			iview.addChildAt(iconview,iview.getChildIndex(iview.yesbtn));
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			EventManager.getInstance().dispatchEvent(new GiftGetActEvent(GiftGetActEvent.SELECT));
			if (!iview.yesbtn.visible)
			{
				iview.yesbtn.visible = true;
				iview.redbackground.visible = true;
			}
		}
		
	}

}