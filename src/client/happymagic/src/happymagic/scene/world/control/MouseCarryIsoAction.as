package happymagic.scene.world.control 
{
	import flash.events.MouseEvent;
	import flash.utils.setTimeout;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.WorldState;
	import happyfish.utils.display.CameraSharkControl;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.UserInfoChangeVo;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.DecorType;
	import happymagic.model.vo.DecorVo;
	import happymagic.scene.world.grid.item.Desk;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.item.WallDecor;
	import happymagic.scene.world.MagicWorld;
	/**
	 * 移动场景中已有的物品
	 * @author Beck
	 */
	public class MouseCarryIsoAction extends MouseMagicAction
	{
		private var _isoItem:IsoItem;
		public function MouseCarryIsoAction($iso_item:IsoItem, $state:WorldState) 
		{
			super($state, true);
			this._isoItem = $iso_item;
			_isoItem.mouseEnabled = false;
			
			//门在移动时需要先重置门后的墙的门洞,
			//并从门列表中移除,以使课桌寻路计算正常
			if (_isoItem is Door) 
			{
				(_isoItem as Door).resetWallView();
				state.world.removeToGrid(_isoItem);
				//通知world从门队列中清除此门
				(state.world as MagicWorld).removeDoorFromList(_isoItem as Door);
			}
			
			_isoItem.physics = false;
			
			DisplayManager.uiSprite.mouseChildren=
			DisplayManager.uiSprite.mouseEnabled = false;
		}
		
        override public function remove($stack_flg:Boolean = true) : void
        {
            this._isoItem.finishMove();
			_isoItem.mouseEnabled = true;
            super.remove($stack_flg);
			
			this.recordChangeData(this._isoItem);
			
			
			
            return;
        }

        override public function onBackgroundClick(event:GameMouseEvent) : void
        {
            event.stopImmediatePropagation();
			var e:DataManagerEvent = new DataManagerEvent(DataManagerEvent.USERINFO_CHANGE);
			
			
			//如果是在区域外,则删除
			if (this._isoItem.outOfArea() || this._isoItem.isoUiSprite != null) {
				
				//判断桌子上是否有人或钱,有如就不可移下
				if (_isoItem is Desk) 
				{
					if ((_isoItem as Desk).student || (_isoItem as Desk).crystal) 
					{
						//桌上有人或钱
						//飘字显示不可移下
						EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("deskCantMove"));
						
						return;
						
					}
				}
				
				//门的话,不可以一个门也没有
				if (_isoItem is Door) 
				{
					if ((state.world as MagicWorld).doorList.length==0) 
					{
						//飘字显示不可移下
						EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("doorCantMove"));
						
						return;
					}
				}
				
				
				//显示最大魔法值变化表现
				var infoChange:UserInfoChangeVo = new UserInfoChangeVo();
				infoChange.piao = true;
				infoChange.maxMp = -(_isoItem["decorVo"] as DecorVo).max_magic;
				
				if (!infoChange.isEmpty) 
				{
					e.userChange = infoChange;
				}
				
				
				this._isoItem.remove();
				
				var tmpitem:Object = (_isoItem.data as DecorVo).clone();
				tmpitem.num = 1;
				
				//背包显示变更
				DisplayManager.buildingItemList.addItem(tmpitem as DecorVo);
				
				//保存修改到背包数据
				//this._isoItem.data.num  = 1;
				this.recordBagChangeData(tmpitem);
				
				super.remove(true);
				DisplayManager.uiSprite.mouseChildren=
				DisplayManager.uiSprite.mouseEnabled = true;
				
				
				//(DataManager.getInstance().worldState.world as MagicWorld).checkAllDeskCantWalk();
				
				EventManager.getInstance().dispatchEvent(e);
				
				
				
				return;
			}
			
            if (this._isoItem.positionIsValid())
            {
				
				//保存移动或移上
                this.remove();
				DisplayManager.uiSprite.mouseChildren=
				DisplayManager.uiSprite.mouseEnabled = true;
				if (!(_isoItem is WallDecor)) 
				{
					_isoItem.view.container.y = -4;
					_isoItem.view.container.vy = -4;
					state.physicsControl.physicsFun(_isoItem);
				}
				
				_isoItem.physics = true;
            }
			
			//(DataManager.getInstance().worldState.world as MagicWorld).checkAllDeskCantWalk();
			
			EventManager.getInstance().dispatchEvent(e);
            return;
        }
		
        override public function onMouseMove(event:MouseEvent) : void
        {
            this._isoItem.move(worldPosition());
        }
		
	}

}