package happymagic.scene.world.control 
{
	import com.friendsofed.isometric.Point3D;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.ItemRender;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.grid.Tile;
	import happyfish.scene.world.WorldState;
	import happyfish.scene.world.WorldView;
	import happyfish.utils.display.CameraSharkControl;
	import happymagic.display.view.edit.DecorListItemView;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.UserInfoChangeVo;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.DecorVo;
	/**
	 * ...
	 * @author Beck
	 */
	public class MouseFloorStampAction extends MouseStampAction
	{
 		public function MouseFloorStampAction($state:WorldState, $item_render:GridItem, $stack_flg:Boolean = false) 
		{
			super($state, $item_render, true);
			
			_isoItem = new Tile( (itemRender as DecorListItemView).data, state);
			
			_isoItem.mouseEnabled = false;
			
			//TODO 需要转移位置
			state.view.isoView.backgroundContainer.addChild(_isoItem.view.container);
			//state.world.addItem(_isoItem);
			_isoItem.move(worldPosition());

		}
		
        override public function remove($stack_flg:Boolean = true) : void
        {
            super.remove($stack_flg);
			
			var change_data:Object = (DataManager.getInstance().worldState.world.nodeWallTileItems[this._isoItem.x][this._isoItem.z].data as DecorVo).clone();
			change_data.num = 1;
			//替换
			DisplayManager.buildingItemList.addItem(change_data as DecorVo);
            
			//提交数据替换
			recordBagChangeData(change_data);
			
			//这里是新的tile数据
			DataManager.getInstance().worldState.world.saveWallTileNodeItem(_isoItem);
			if ((itemRender as DecorListItemView).data.num > 0) {
				new MouseFloorStampAction(this.state, itemRender);
			}
			return;
        }
		
        override public function onBackgroundClick(event:GameMouseEvent) : void
        {
            event.stopImmediatePropagation();
			
			var e:DataManagerEvent = new DataManagerEvent(DataManagerEvent.USERINFO_CHANGE);
			//显示最大魔法值变化表现
			var infoChange:UserInfoChangeVo = new UserInfoChangeVo();
			infoChange.piao = true;
			
			
			
			//如果是在区域外,则删除
			if (_isoItem.outOfArea() || _isoItem.isoUiSprite != null) {
				//state.world.removeItem(_isoItem);
				state.view.isoView.backgroundContainer.removeChild(_isoItem.view.container);
				//清除印章对象
				_isoItem.removeIsoUiSprite();
				super.superRemove(true);
				return;
			}
			
            if (_isoItem.positionIsValid())
            {
				if ((_isoItem as Tile)) 
				{
					
				}
				
				var p:Point3D = IsoUtil.isoToGrid(_isoItem.view.position);
				var change_data:DecorVo = (state.world.nodeWallTileItems[p.x][p.z].data as DecorVo);
				
				//修改最大魔法值加成
				infoChange.maxMp = getMaxMpChange() - change_data.max_magic;
				e.userChange = infoChange;
				EventManager.getInstance().dispatchEvent(e);
				
                remove();
            }
            return;
        }
		
		
	}

}