package happymagic.display.view.roomUp 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.SoundEffectManager;
	import happyfish.utils.display.AlginControl;
	import happyfish.utils.display.BtnStateControl;
	import happyfish.utils.HtmlTextTools;
	import happymagic.display.view.ui.NeedCrystalLabelView;
	import happymagic.manager.DataManager;
	import happymagic.model.command.RoomUpgradeCommand;
	import happymagic.model.vo.MoneyType;
	import happymagic.model.vo.RoomSizeVo;
	/**
	 * ...
	 * @author jj
	 */
	public class RoomUpView extends UISprite
	{
		private var datas:Array;
		private var iview:roomUpUi;
		private var list:DefaultListView;
		private var needCrystalItem:NeedCrystalLabelView;
		private var nextRoomSize:RoomSizeVo;
		private var needGemItem:NeedCrystalLabelView;
		
		public function RoomUpView() 
		{
			super();
			_view = new roomUpUi();
			
			iview = _view as roomUpUi;
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			list = new DefaultListView(new defaultListUi(), iview, 4,false);
			list.x = -196;
			list.y = 110;
			list.setGridItem(RoomUpItemView, roomUpListItemUi);
			list.init(360, 90, 83, 90, 30,-50);
			
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case iview.closeBtn:
				closeMe();
				break;
				
				case iview.crystalBuyBtn:
				roomUp(nextRoomSize.id,1);
				break;
				
				case iview.gemBuyBtn:
				roomUp(nextRoomSize.id,2);
				break;
			}
		}
		
		public function roomUp(id:uint,type:uint):void
		{
			iview.mouseChildren = false;
			var command:RoomUpgradeCommand = new RoomUpgradeCommand();
			command.addEventListener(Event.COMPLETE, roomUp_complete);
			command.upgrade(id,type);
		}
		
		private function roomUp_complete(e:Event):void 
		{
			iview.mouseChildren = true;
			e.target.removeEventListener(Event.COMPLETE, roomUp_complete);
			
			if (e.target.data.result.isSuccess) 
			{
				//修改用户信息
				DataManager.getInstance().currentUser.tile_x_length = nextRoomSize.sizeX;
				DataManager.getInstance().currentUser.tile_z_length = nextRoomSize.sizeZ;
			}
			
			closeMe();
		}
		
		public function setData(value:Array):void {
			datas = value;
			
			list.setData(datas);
			
			//找到当前最近一级可升级的
			nextRoomSize = DataManager.getInstance().getNextRoomSizeVo(DataManager.getInstance().currentUser.tile_x_length);
			
			setNextRoomSizeView(nextRoomSize);
			
			list.setData(value);
			
			
		}
		
		private function setNextRoomSizeView(value:RoomSizeVo):void {
			//清除原有信息
			iview.roomSizeTxt.text = "";
			
			//如果无信息
			if (!value) 
			{
				BtnStateControl.setBtnState(iview.crystalBuyBtn, false);
				BtnStateControl.setBtnState(iview.gemBuyBtn, false);
				return;
			}
			
			iview.roomSizeTxt.text = value.sizeX + "x" + value.sizeZ;
			
			var curUserLevel:uint = DataManager.getInstance().currentUser.roomLevel;
			var needMaxMp:uint = DataManager.getInstance().getRoomLevel(value.needLevel).needMaxMp;
			var curMaxMp:uint = DataManager.getInstance().currentUser.max_mp;
			
			if (value.needLevel<=DataManager.getInstance().currentUser.roomLevel) {
				HtmlTextTools.setTxtSaveFormat(iview.maxMpTxt, needMaxMp.toString(), 0xffffff);
				iview.lockIcon.visible = false;
			}else {
				HtmlTextTools.setTxtSaveFormat(iview.maxMpTxt, needMaxMp.toString(), 0xFF0000);
				iview.lockIcon.visible = true;
			}
			
			var enoughFriend:Boolean;
			if (DataManager.getInstance().friends.length>=value.needFriendNum) 
			{
				enoughFriend = true;
				if (value.needFriendNum==0) 
				{
					iview.needFriendTxt.visible=
					iview.friendNumIcon.visible = false;
					
					iview.needCoinIcon.y = 
					iview.coinTxt.y = -46;
					
					
				}else {
					iview.needFriendTxt.visible=
					iview.friendNumIcon.visible = true;
					
					iview.needCoinIcon.y = 
					iview.coinTxt.y = -34;
					
					HtmlTextTools.setTxtSaveFormat(iview.needFriendTxt, value.needFriendNum.toString(), 0xffffff);
				}
				
			}else {
				enoughFriend = false;
				HtmlTextTools.setTxtSaveFormat(iview.needFriendTxt, value.needFriendNum.toString(), 0xFF0000);
			}
			
			var enough:Boolean;
			
			enough = DataManager.getInstance().getEnouthCrystalType(MoneyType.COIN, value.coin);
			if ( enough ) 
			{
				HtmlTextTools.setTxtSaveFormat(iview.coinTxt,value.coin.toString(),0xffffff);
			}else {
				HtmlTextTools.setTxtSaveFormat(iview.coinTxt,value.coin.toString(),0xFF0000);
			}
			BtnStateControl.setBtnState(iview.crystalBuyBtn, enough && needMaxMp <= curMaxMp && enoughFriend );
			
			
			enough = DataManager.getInstance().getEnouthCrystalType(MoneyType.GEM, value.gem);
			if ( enough ) 
			{
				HtmlTextTools.setTxtSaveFormat(iview.gemTxt,value.gem.toString(),0xffffff);
			}else {
				HtmlTextTools.setTxtSaveFormat(iview.gemTxt,value.gem.toString(),0xFF0000);
			}
			BtnStateControl.setBtnState(iview.gemBuyBtn, enough && needMaxMp <= curMaxMp );
			
		}
	}

}