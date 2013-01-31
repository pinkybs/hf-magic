package happymagic.scene.world.control 
{
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.ItemRender;
	import happyfish.scene.world.WorldState;
	import happymagic.display.view.edit.DecorListItemView;
	import happymagic.model.vo.DecorType;
	import happymagic.model.vo.DecorVo;
	import happymagic.scene.world.grid.item.Decor;
	import happymagic.scene.world.grid.item.Desk;
	/**
	 * ...
	 * @author Beck
	 * Decor对象印章
	 * 
	 */
	public class MouseDecorStampAction extends MouseStampAction
	{
		
		public function MouseDecorStampAction($state:WorldState, $item_render:GridItem, $stack_flg:Boolean = false) 
		{
			super($state, $item_render, true);
			var tmpdata:DecorVo = (itemRender as DecorListItemView).getAndDelOne();
			if (tmpdata.type==DecorType.DESK) 
			{
				_isoItem = new Desk(tmpdata, state);
			}else {
				_isoItem = new Decor( (itemRender as DecorListItemView).getAndDelOne(), this.state);
			}
			
			_isoItem.mouseEnabled = false;
			
			this.state.world.addItem(this._isoItem);
			this._isoItem.move(worldPosition());
			
			this.init();
		}
		
	}

}