package happyfish.scene.world 
{
	import com.friendsofed.isometric.IsoUtils;
	import com.friendsofed.isometric.Point3D;
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import happyfish.events.GameMouseEvent;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.iso.IsoView;
	import happyfish.scene.world.control.MapDrag;
	import happyfish.scene.world.control.MouseCursorAction;
	import happymagic.scene.world.bigScene.BigSceneBg;
	/**
	 * ...
	 * @author Beck
	 */
	public class WorldView extends Sprite
	{
		private var _isoView:IsoView;
		public static const LAYER_BOTTOM:int = 0; //最低层,不排序
		public static const LAYER_REALTIME_SORT:int = 1; //墙壁装饰,桌子,人,几乎所有需要排序的
		public static const LAYER_FLYING:int = 2; //不明飞行物
		public static const LAYER_MV:int = 3; //特效层
		
		public static const WORLD_WIDTH:int = 2000;
		public static const WORLD_HEIGHT:int = 1300;
		
		protected var _worldState:WorldState;
		protected var selectionContainer:Sprite;
        public var defaultContainer:Sprite;
        public var editContainer:Sprite;
        private var visibleLayer:Sprite;
		
		public var mouseAction:MouseCursorAction;
		
		public function WorldView($worldState:WorldState) 
		{
			this.selectionContainer = new Sprite();
			
            this._isoView = new IsoView();
            addChild(this._isoView);
			this.addChild(this.selectionContainer);
			
			this._worldState = $worldState;
			
			//点击背景监听
            addEventListener(MouseEvent.CLICK, this.preMouseClick, true);
            addEventListener(MouseEvent.CLICK, this.postMouseClick, false,-1);
		}
		
		private function resizeFun(e:Event):void 
		{
			_isoView.center();
		}
		
		/**
		 * 将IsoSprite对象放入容器
		 * @param	$isoSprite
		 */
        public function addIsoChild($isoSprite:IsoSprite) : void
        {
            this._isoView.addIsoChild($isoSprite);
            return;
        }
		
		public function get isoView():IsoView
		{
			return this._isoView;
		}
		
		/**
		 * 居中
		 */
		public function center():void
		{
			this._isoView.center();
		}
		
		/**
		 * 前置监听事件,取消冒泡
		 * @param	event
		 */
		private function preMouseClick(event:MouseEvent) : void
        {
            //event.stopPropagation();
            return;
        }
		
		/**
		 * 后置监听,鼠标点击背景,派发游戏背景点击事件,主要用在diy模式,放下物品的时候
		 * @param	event
		 */
        private function postMouseClick(event:MouseEvent) : void
        {
            dispatchEvent(new GameMouseEvent(GameMouseEvent.CLICK, null, "Background"));
            return;
        }
		
		public function onMouseMove(event:MouseEvent) : void
		{
			if (this._worldState.mouseAction != null)
			{
				this._worldState.mouseAction.onMouseMove(event);
			}
		}
		
		/**
		 * 显示编辑层
		 * @param	$layer
		 */
        public function showLayer($layer:Sprite) : void
        {
            if (this.visibleLayer != null)
            {
                selectionContainer.removeChild(this.visibleLayer);
            }
            this.visibleLayer = $layer;
            selectionContainer.addChild(this.visibleLayer);
            return;
        }// end function
		
		/**
		 * 将背景加入主sprite
		 * @param	param1
		 */
        public function setBackground($bdata:BitmapData, $x:int, $y:int) : void
        {
            var bitmap:Bitmap = new Bitmap($bdata, "auto", true);
            bitmap.x = $x;
            bitmap.y = $y;
			
            while (this._isoView.backgroundContainer.numChildren > 0)
            {
                this._isoView.backgroundContainer.removeChildAt(0);
            }
            this._isoView.backgroundContainer.addChild(bitmap);
        }
		
		public function setBigBg(bitmap:Bitmap, $x:int, $y:int):void {
            bitmap.x = $x;
            bitmap.y = $y;
			
            while (_isoView.big_backgroundContainer.numChildren > 0)
            {
                _isoView.big_backgroundContainer.removeChildAt(0);
            }
            _isoView.big_backgroundContainer.addChild(bitmap);
		}
		
		/**
		 * 将当前鼠标坐标转换为grid坐标
		 * @return	[Point3D]	鼠标所在格的格子坐标
		 */
		public function targetGrid():Point3D
		{
			var targetP:Point3D = IsoUtils.screenToIso(new Point(this.isoView.camera.mouseX-6 + IsoUtil.TILE_SIZE/2, 
							this.isoView.camera.mouseY + IsoUtil.TILE_SIZE/2-isoView.sceneY));
			return IsoUtil.isoToGrid(targetP);
		}
		
	}

}