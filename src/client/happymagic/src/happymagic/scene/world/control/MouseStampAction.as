package happymagic.scene.world.control 
{
	import flash.events.MouseEvent;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.ItemRender;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.WorldState;
	import happyfish.utils.display.CameraSharkControl;
	import happymagic.display.view.edit.DecorListItemView;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.UserInfoChangeVo;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.DecorVo;
	import happymagic.scene.world.grid.item.Decor;
	import happymagic.scene.world.grid.item.Desk;
	/**
	 * 印章基类,将一个新物品放入场景中
	 * @author Beck
	 */
	public class MouseStampAction extends MouseMagicAction
	{
		protected var itemRender:GridItem;
		protected var _isoItem:IsoItem;
 
		public function MouseStampAction($state:WorldState, $item_render:GridItem, $stack_flg:Boolean = false) 
		{
			super($state, true);
			
			this.itemRender = $item_render;
		}
		
		public function init():void
		{
			//_isoItem.view.sortPriority += 1;
			this._isoItem.addIsoTile();
		}
		
		/**
		 * 放置成功
		 * @param	$stack_flg
		 */
        override public function remove($stack_flg:Boolean = true) : void
        {
			//目标排序优先级
			//_isoItem.view.sortPriority -= 1;
			//目标完成拖动
            this._isoItem.finishMove();
			
			_isoItem.mouseEnabled = true;
			
			//关闭action
            super.remove($stack_flg);
			
			//diy数据变更
			recordChangeData(_isoItem);
			
			//重新渲染背包数据
			//背包数据减去一个移上的物品
			itemRender.delNum(1);
			
            return;
        }
		
		public function superRemove($stack_flg:Boolean = true):void
		{
			super.remove($stack_flg);
		}

        override public function onBackgroundClick(event:GameMouseEvent) : void
        {
            event.stopImmediatePropagation();
			var e:DataManagerEvent = new DataManagerEvent(DataManagerEvent.USERINFO_CHANGE);
			//显示最大魔法值变化表现
			var infoChange:UserInfoChangeVo = new UserInfoChangeVo();
			infoChange.piao = true;
			
			//如果是在区域外,则删除,放置失败
			if (this._isoItem.outOfArea() || this._isoItem.isoUiSprite != null) {
				
				this._isoItem.remove();
				super.remove(true);
				
				EventManager.getInstance().dispatchEvent(e);
				
				return;
			}
			
			//如果是可放置位置
            if (this._isoItem.positionIsValid())
            {
				//如果刘课桌，判断课桌上限
				if(_isoItem is Desk){
					if (DataManager.getInstance().getDeskInRoom()> DataManager.getInstance().getRoomLevel(DataManager.getInstance().currentUser.roomLevel).desk_limit) 
					{
						EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("deskTooMuch"));
						this._isoItem.remove();
						super.remove(true);
						return;
					}
				}
				//修改最大魔法值加成
				infoChange.maxMp = getMaxMpChange();
				
				if (!infoChange.isEmpty) 
				{
					e.userChange = infoChange;
				}
				remove();
            }
			
			EventManager.getInstance().dispatchEvent(e);
            return;
        }
		
		protected function getMaxMpChange():int 
		{
			return (_isoItem["decorVo"] as DecorVo).max_magic;
		}
		
        override public function onMouseMove(event:MouseEvent) : void
        {
            this._isoItem.move(worldPosition());
        }
		
	}

}