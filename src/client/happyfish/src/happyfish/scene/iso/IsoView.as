package happyfish.scene.iso 
{
	import flash.display.Sprite;
	import flash.geom.Point;
	import happyfish.scene.world.WorldView;
	
	/**
	 * 负责显示,这个类最终将被实际加入主显示对象
	 * @author Beck
	 */
	public class IsoView extends Sprite
	{
		public var backgroundContainer:Sprite;
		public var big_backgroundContainer:Sprite;
		public var layers:Array = [];
		public var camera:Sprite;
		public static var NUM_LAYERS:int = 4;
		public var sceneY:int = 0;
		public function IsoView() 
		{
            this.camera = new Sprite();
			backgroundContainer = new Sprite();
			big_backgroundContainer = new Sprite();
            addChild(camera);
			initialize();
		}
		
		public function getLayer(index:uint):Sprite {
			return layers[index];
		}
		
		/**
		 * 将IsoSprite放入相应的layer里
		 * @param	isoSprite
		 */
        public function addIsoChild(isoSprite:IsoSprite) : void
        {
            this.layers[isoSprite.layer].addIsoChild(isoSprite);
            return;
        }
		
		public function removeIsoChild(isoSprite:IsoSprite) : void
		{
            this.layers[isoSprite.layer].removeIsoChild(isoSprite);
            return;
		}
		
        private function initialize() : void
        {
			sceneY = -IsoUtil.TILE_SIZE*60/2;
			
			camera.addChild(big_backgroundContainer);
			camera.addChild(backgroundContainer);
			backgroundContainer.y = sceneY;
			
			
			var layer:IsoLayer = null;
			
            this.layers = [];
            var layerCount:int = 0;
            while (layerCount < NUM_LAYERS)
            {
                if (layerCount==WorldView.LAYER_REALTIME_SORT) 
				{
					 layer = new IsoLayer(this,true);
				}else {
					 layer = new IsoLayer(this,false);
				}
               
				layer.name = "isoLayer_" + layerCount.toString();
				layer.y = sceneY;
                camera.addChild(layer);
                layers.push(layer);
                layerCount++;
            }
            return;
        }
		
		public function center():void
		{
			//this.camera.x = 0;
			//this.camera.y = 0;
			
			camera.x = (stage.stageWidth) / 2;
			camera.y = (WorldView.WORLD_HEIGHT - stage.stageHeight)/2;
		}
		
		public function fullCenter():void
		{
			center();
		}
		
	}

}