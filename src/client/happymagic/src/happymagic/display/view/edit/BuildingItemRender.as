package happymagic.display.view.edit 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	
	import happyfish.cacher.CacheSprite;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.ItemRender;
	import happyfish.display.view.PageList;
	import happyfish.events.GameMouseEvent;
	
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.PublicDomain;
	import happymagic.model.vo.DecorType;
	import happymagic.model.vo.DecorVo;
	import happymagic.scene.world.control.MouseDecorStampAction;
	import happymagic.scene.world.control.MouseDoorStampAction;
	import happymagic.scene.world.control.MouseEditAction;
	import happymagic.scene.world.control.MouseFloorStampAction;
	import happymagic.scene.world.control.MouseMagicAction;
	import happymagic.scene.world.control.MouseStampAction;
	import happymagic.scene.world.control.MouseWallDecorStampAction;
	import happymagic.scene.world.control.MouseWallStampAction;
	
	/**
	 * ...
	 * @author Beck
	 */
	public class BuildingItemRender extends ItemRender
	{
		protected var _iview:ui_decor;
		protected var asset:CacheSprite;
		public function BuildingItemRender() 
		{
			this._view = new ui_decor;
			this._iview = this._view as ui_decor;
			
			_iview.owner = this;
			_iview.mouseChildren = false;
			
			this._iview.addEventListener(MouseEvent.CLICK, clickFun, false);
		}
		
		override public function set data($data:Object):void
		{
			if (this.asset) {
				this._iview.jar.removeChild(this.asset);
			}
			
			this._data = $data;
			
			this.asset = new CacheSprite();
			this.asset.setScaleFlg(true);
			this.asset.className = this._data.class_name;
			
			//设置数据
			this._iview.jar.addChild(this.asset);
			this._iview.diynum.text = this._data.num;
		}
		
		override public function set numDiff($vl:int):void
		{
			trace(this._data.num);
			//同时改变了数组内的值,引用传递
			this._data.num += $vl;
			//PageList(this._iview.parent).removeRenderList(this.data as DecorVo, $vl);
			DisplayManager.buildingItemList.page_list.removeRenderList(this.data as DecorVo, $vl);

			this.data = this._data;
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			//判断是否有物品拖拽中,这时则将此物品放入背包
			if (DataManager.getInstance().worldState.mouseAction is MouseMagicAction) {
				if (DataManager.getInstance().worldState.mouseAction is MouseEditAction) {
					
				} else {

					return;
				}
			}
			
			switch (e.target) 
			{
				default:
					if (this._data.type) {
						trace(this._data.type);
						if (this._data.type == DecorType.FLOOR) {
							new MouseFloorStampAction(DataManager.getInstance().worldState, this as GridItem);
							break;
						} else if (this._data.type == DecorType.WALL_PAPER) {
							new MouseWallStampAction(DataManager.getInstance().worldState, this as GridItem);
							break;
						} else if (this._data.type == DecorType.WALL_DECOR) {
							new MouseWallDecorStampAction(DataManager.getInstance().worldState, this as GridItem);
							break;
						} else if (this._data.type == DecorType.DOOR) {
							new MouseDoorStampAction(DataManager.getInstance().worldState, this as GridItem);
							break;
						}
					}
					//新建一个印章
					new MouseDecorStampAction(DataManager.getInstance().worldState, this as GridItem);
			}
		}
		
	}

}