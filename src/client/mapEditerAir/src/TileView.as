package  
{
	import com.friendsofed.isometric.IsoObject;
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.MovieClip;
	import flash.geom.Matrix;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoUtil;
	
	/**
	 * ...
	 * @author jj
	 */
	public class TileView extends IsoObject
	{
		public static var uiMap:BitmapData;
		public var data:Node;
		public function TileView(_ui:BitmapData=null) 
		{
			super(IsoUtil.TILE_SIZE);
			if (!uiMap) {
				var tmpsize:Number = IsoUtil.TILE_SIZE;
				var uiMap:BitmapData = new BitmapData(tmpsize * 2, tmpsize, true, 0xffffff);
				if (_ui) 
				{
					uiMap = _ui;
				}else {
					var tmpui:gridUi = new gridUi();
					var tmpmatrix:Matrix = new Matrix();
					tmpmatrix.translate(tmpsize,tmpsize/2);
					uiMap.draw(tmpui,tmpmatrix);
				}
				
			}
			
			addChild(new Bitmap(uiMap));
			
			mouseChildren = false;
			
			//var tmp:gridUi = new gridUi();
			//tmp.cacheAsBitmap = true;
			//addChild(tmp);
			
			alpha = .1;
		}
		
		public function setNode(value:Node):void
		{
			data = value;
			
			if (data.walkable) {
				alpha = .1;
			}else {
				alpha = .5;
			}
		}
		
	}

}