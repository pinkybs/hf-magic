package happymagic.scene.world.control 
{
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.ItemRender;
	import happyfish.scene.world.WorldState;
	import happymagic.display.view.edit.DecorListItemView;
	import happymagic.model.vo.DecorVo;
	import happymagic.scene.world.grid.item.Door;
	
	/**
	 * ...
	 * @author Beck
	 */
	public class MouseDoorStampAction extends MouseStampAction
	{
		
		public function MouseDoorStampAction($state:WorldState, $item_render:GridItem, $stack_flg:Boolean = false) 
		{
			super($state, $item_render, true);
			
			var doorData:DecorVo = (itemRender as DecorListItemView).getAndDelOne();
			
			doorData.door_left_time = doorData.door_refresh_time;
			this._isoItem = new Door(doorData , this.state);
			
			_isoItem.mouseEnabled = false;
			
			(_isoItem as Door).diyState = true;
			this.state.world.addItem(this._isoItem);
			this._isoItem.move(worldPosition());
			
			this.init();
		}
		
	}

}