package happyfish.actModule.giftGetAct.view.selectFriend 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happyfish.actModule.giftGetAct.model.vo.GiftFriendUserVo;
	import happyfish.display.ui.GridItem;
	/**
	 * ...
	 * @author ZC
	 */
	public class SelectFriendListItemView extends GridItem
	{
		
		private var iview:SelectFriendListItemViewUi;
		private var data:GiftFriendUserVo;
		public function SelectFriendListItemView(_uiview:MovieClip) 
		{
			super(_uiview);
			iview = _uiview as SelectFriendListItemViewUi;
			
			iview.addEventListener(MouseEvent.CLICK, clickrun);
		}
		
		override public function setData(value:Object):void 
		{
			data = value as GiftFriendUserVo;
			iview.friendyesbtn.visible = false;
			
			var savelist:Array = GiftDomain.getInstance().getVar("selectlist");
			
			var i:int = 0;
			for (i = 0; i < savelist.length; i++ )
			{
                if (data.uid == savelist[i])
				{
					iview.friendyesbtn.visible = true;
				}
			}
			
			iview.nametxt.text = data.name;
			

		}
		
		private function clickrun(e:MouseEvent):void 
		{
			SelectFriendView.Update(data.uid);			
			if (iview.friendyesbtn.visible)
			{
				iview.friendyesbtn.visible = false;
			}
			else
			{
				iview.friendyesbtn.visible =  true;
			}
			

		}
		
		
		
	}

}