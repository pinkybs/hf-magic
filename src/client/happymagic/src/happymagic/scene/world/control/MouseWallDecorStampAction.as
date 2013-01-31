package happymagic.scene.world.control 
{
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.ItemRender;
	import happyfish.scene.world.WorldState;
	import happymagic.display.view.edit.DecorListItemView;
	import happymagic.scene.world.grid.item.WallDecor;
	
	/**
	 * ...
	 * @author Beck
	 */
	public class MouseWallDecorStampAction extends MouseStampAction
	{
		
		public function MouseWallDecorStampAction($state:WorldState, $item_render:GridItem, $stack_flg:Boolean = false) 
		{
			super($state, $item_render, true);
			
			this._isoItem = new WallDecor( (itemRender as DecorListItemView).data, this.state);
			_isoItem.mouseEnabled = false;
			this.state.world.addItem(this._isoItem);
			this._isoItem.move(worldPosition());
			
			this.init();
		}
		
	}

}