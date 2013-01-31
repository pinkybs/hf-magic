package happymagic.scene.world.control 
{
	import flash.events.MouseEvent;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.ItemRender;
	import happyfish.events.GameMouseEvent;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.grid.Wall;
	import happyfish.scene.world.WorldState;
	import happymagic.display.view.edit.DecorListItemView;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.DecorVo;
	
	/**
	 * ...
	 * @author Beck
	 */
	public class MouseWallStampAction extends MouseStampAction
	{
		public function MouseWallStampAction($state:WorldState, $item_render:GridItem, $stack_flg:Boolean = false) 
		{
			super($state, $item_render, true);
			
			var tmpdata:DecorVo = (itemRender as DecorListItemView).data.clone();
			tmpdata.delNum(1);
			this._isoItem = new Wall( tmpdata, this.state);
			_isoItem.mouseEnabled = false;
			
			//TODO 需要转移位置
			this.state.world.addItem(this._isoItem,true);
			this._isoItem.move(worldPosition());
			
			this.init();
		}
		
        override public function remove($stack_flg:Boolean = true) : void
        {
            super.remove($stack_flg);
			
			var change_data:Object = (state.world.nodeWallTileItems[this._isoItem.x][this._isoItem.z].data as DecorVo).clone();
			change_data.num = 1;
			
			//增加要移下的墙纸到道具箱内
			DisplayManager.buildingItemList.addItem(change_data as DecorVo);
            
			//提交数据替换
			this.recordBagChangeData(change_data);
			
			//记录新的wall数据到墙纸列表
			state.world.saveWallTileNodeItem(this._isoItem);
			
			//如果该墙纸还有,就继续拖动
			if ((itemRender as DecorListItemView).data.num > 0) {
				new MouseWallStampAction(this.state, itemRender);
			}
			
			return;
        }
		
		override protected function getMaxMpChange():int 
		{
			var change_data:Object
			if (state.world.nodeWallTileItems[this._isoItem.gridPos.x]) 
			{
				if (state.world.nodeWallTileItems[this._isoItem.gridPos.x][this._isoItem.gridPos.z]) 
				{
					change_data=state.world.nodeWallTileItems[this._isoItem.gridPos.x][this._isoItem.gridPos.z].data;
				}
			}
			if (change_data) 
			{
				var change:int=(_isoItem["decorVo"] as DecorVo).max_magic - change_data.max_magic;
				return change;
			}
			
			return 0;
			
		}
	}

}