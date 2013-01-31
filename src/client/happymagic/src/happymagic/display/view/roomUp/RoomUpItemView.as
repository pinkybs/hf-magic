package happymagic.display.view.roomUp 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import happyfish.display.ui.GridItem;
	import happyfish.utils.display.ItemOverControl;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.RoomLevelVo;
	import happymagic.model.vo.RoomSizeVo;
	import happymagic.model.vo.UserVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class RoomUpItemView extends GridItem
	{
		private var iview:roomUpListItemUi;
		public var data:RoomSizeVo;
		
		public function RoomUpItemView(uiview:MovieClip) 
		{
			super(uiview);
			iview = uiview as roomUpListItemUi;
			
			iview.mouseChildren = false;
			iview.buttonMode = true;
			iview.addEventListener(MouseEvent.CLICK, clickFun);
			
			ItemOverControl.getInstance().addOverItem(iview);
		}
		
		override public function setData(value:Object):void 
		{
			data = value as RoomSizeVo;
			
			if (data.sizeX==0) 
			{
				iview.txt.text = "8x8";
			}else {
				iview.txt.text = data.sizeX.toString() + "x" + data.sizeZ.toString();
			}
			
			
			var roomlevel:RoomLevelVo = DataManager.getInstance().getRoomLevel(data.needLevel);
			if (roomlevel) 
			{
				var needMaxMp:uint = roomlevel.needMaxMp;
				if (data.needLevel>DataManager.getInstance().currentUser.roomLevel) 
				{
					iview.maxMpTxt.text = needMaxMp.toString();
				}else {
					iview.maxMpTxt.visible=
					iview.mpIcon.visible = false;
				}
			}else {
				iview.maxMpTxt.visible=
				iview.mpIcon.visible = false;
			}
			
			var curUser:UserVo = DataManager.getInstance().currentUser;
			var nextRoomSize:RoomSizeVo = DataManager.getInstance().getNextRoomSizeVo(curUser.tile_x_length);
			
			
			if (curUser.roomLevel<data.needLevel) 
			{
				//未解锁
				iview.stateIcon.gotoAndStop(1);
			}else if (nextRoomSize.id==data.id) {
				//最近一级可升
				iview.stateIcon.gotoAndStop(2);
			}else if (nextRoomSize.id < data.id) {
				//可升,但前面还有未升的
				iview.stateIcon.gotoAndStop(1);
			}else {
				//已升
				iview.stateIcon.gotoAndStop(3);
			}
			
			
			
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			
		}
		
	}

}