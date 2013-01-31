package happyfish.scene.world.control 
{
	import flash.display.Sprite;
	import flash.display.Stage;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.WorldView;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	/**
	 * ...
	 * @author Beck Xu
	 */
	public class MapDrag
	{
		private static var single:MapDrag;
		private var drag_area:Sprite;
		private var mouseDown:Boolean;
		public function MapDrag($drag_area:Sprite) 
		{
			this.drag_area = $drag_area;
			this.init();
		}
		
		public static function getInstance($drag_area:Sprite):MapDrag
		{
			if (single === null) {
				single = new MapDrag($drag_area);
			}
			return single;
		}
		
		public function init():void
		{
			drag_area.addEventListener(MouseEvent.MOUSE_DOWN, beginDrag);
			drag_area.addEventListener(MouseEvent.MOUSE_MOVE, moveDrag);
			drag_area.stage.addEventListener(MouseEvent.MOUSE_UP, upDrag);
			drag_area.addEventListener(MouseEvent.MOUSE_OUT, overDrag);
		}
		
		private function upDrag(e:MouseEvent):void 
		{
			mouseDown = false;
			drag_area.stopDrag();
		}
	
		/**
		 * 主地图背景拖动开始
		 */
		public function beginDrag(e:MouseEvent):void
		{
			mouseDown = true;
			//trace(e.target,e.target.name, e.currentTarget,e.currentTarget.name);
			var stage:Stage = drag_area.stage;
			var wAdd:Number = Math.abs(stage.stageWidth - WorldView.WORLD_WIDTH);
			var hAdd:Number = Math.abs(stage.stageHeight-WorldView.WORLD_HEIGHT);
			drag_area.startDrag(
				false, 
				new Rectangle(WorldView.WORLD_WIDTH / 2, WorldView.WORLD_HEIGHT / 2 - IsoUtil.TILE_SIZE / 2, -wAdd, -hAdd)
			);
		}
		
		/**
		 * 主地图背景拖动中
		 */
		public function moveDrag(e:MouseEvent):void
		{
			if (mouseDown) 
			{
				DataManager.getInstance().isDraging = true;
				//去除已有tips
				if (DisplayManager.doorTip) 
				{
					if (DisplayManager.doorTip.view.parent) 
					{
						DisplayManager.doorTip.view.parent.removeChild(DisplayManager.doorTip.view);
					}
				}
				if (DisplayManager.deskTip) 
				{
					if (DisplayManager.deskTip.view.parent) 
					{
						DisplayManager.deskTip.view.parent.removeChild(DisplayManager.deskTip.view);
					}
				}
			}
		}

		/**
		 * 主地图背景拖动结束
		 */
		public function overDrag(e:MouseEvent):void
		{
			if (!mouseDown) 
			{
				mouseDown = false;
				drag_area.stopDrag();
			}
			
		}
		
	}

}