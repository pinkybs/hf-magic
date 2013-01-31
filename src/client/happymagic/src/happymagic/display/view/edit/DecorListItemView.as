package happymagic.display.view.edit 
{
	import com.adobe.utils.ArrayUtil;
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.DecorType;
	import happymagic.model.vo.DecorVo;
	import happymagic.scene.world.control.MouseDecorStampAction;
	import happymagic.scene.world.control.MouseDoorStampAction;
	import happymagic.scene.world.control.MouseEditAction;
	import happymagic.scene.world.control.MouseFloorStampAction;
	import happymagic.scene.world.control.MouseMagicAction;
	import happymagic.scene.world.control.MouseWallDecorStampAction;
	import happymagic.scene.world.control.MouseWallStampAction;
	import happymagic.scene.world.grid.item.Decor;
	
	/**
	 * ...
	 * @author jj
	 */
	public class DecorListItemView extends GridItem
	{
		public var data:DecorVo;
		private var iview:ui_decor;
		private var icon:IconView;
		
		public function DecorListItemView(uiview:MovieClip) 
		{
			super(uiview);
			iview = uiview as ui_decor;
			
			view.addEventListener(MouseEvent.CLICK, clickFun);
			
		}
		
		override public function setData(value:Object):void 
		{
			data = value as DecorVo;
			
			icon = new IconView(68, 68, new Rectangle(5, 10, 68, 68));
			icon.setData(data.class_name);
			view.addChild(icon);
			
			iview.starIcon.gotoAndStop(data.level);
			initNumTxt();
		}
		
		private function initNumTxt():void {
			iview.diynum.text = data.num.toString();
		}
		
		override public function add(value:Object):void 
		{
			data.add(value as DecorVo);
			initNumTxt();
		}
		
		public function getAndDelOne():DecorVo {
			var tmp:DecorVo = data.clone();
			tmp.ids = [data.ids[0]];
			return tmp;
		}
		
		override public function delNum(num:uint):Array 
		{
			var arr:Array = data.delNum(num);
			if (data.num<=0) 
			{
				ArrayUtil.removeValueFromArray(DataManager.getInstance().decorBagList,data);
				DisplayManager.buildingItemList.initData();
			}else {
				initNumTxt();
			}
			return arr;
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			
			switch (e.target) 
			{
				default:
					if (data.type) {
						
						if (data.type == DecorType.FLOOR) {
							new MouseFloorStampAction(DataManager.getInstance().worldState, this);
							break;
						} else if (data.type == DecorType.WALL_PAPER) {
							new MouseWallStampAction(DataManager.getInstance().worldState, this);
							break;
						} else if (data.type == DecorType.WALL_DECOR) {
							new MouseWallDecorStampAction(DataManager.getInstance().worldState, this);
							break;
						} else if (data.type == DecorType.DOOR) {
							new MouseDoorStampAction(DataManager.getInstance().worldState, this);
							break;
						}
					}
					//新建一个印章
					new MouseDecorStampAction(DataManager.getInstance().worldState, this);
			}
		}
		
		
	}

}