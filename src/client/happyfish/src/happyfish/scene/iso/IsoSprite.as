package happyfish.scene.iso 
{
	import com.friendsofed.isometric.IsoObject;
	import com.friendsofed.isometric.Point3D;
	import flash.display.Sprite;
	import flash.geom.Point;
	import happyfish.scene.world.grid.IsoItem;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.item.WallDecor;
	/**
	 * 负责设置精灵的位置,同样是一个管理类
	 * @author Beck
	 */
	public class IsoSprite
	{
        public var container:IsoObject;
        //public var depth:Number = 0;
		//精灵所在层
        public var parent:IsoLayer;
        private var point:Point;
        private var _layer:int;
        public var prev:IsoSprite;
        public var next:IsoSprite;
		
		public var isoItem:IsoItem;
		
		public function IsoSprite($layer:int = 1) 
		{
			this._layer = $layer;
			this.container = new IsoObject(IsoUtil.TILE_SIZE,this);
		}
		
		public function get layer():int
		{
			return _layer;
		}
		
		public function set position(value:Point3D):void
		{
			this.container.position = value;
		}
		
		public function get position():Point3D
		{
			return this.container.position;
		}
		
        public function setPos($pos:Point3D) : void
        {
			this.position = IsoUtil.gridToIso($pos);
        }
		
		public function get grid_size_x():int {
			return isoItem.grid_size_x;
		}
		
		public function get grid_size_z():int {
			return isoItem.grid_size_z;
		}
		
		//*************************************
		public function get left():Number {
			//if (isoItem is WallDecor) 
			//{
				//if (isoItem.mirror) 
				//{
					//return container.x-1;
				//}
			//}
			return (container.x);
		}
		
		public function get right():Number {
			
			return container.x+((grid_size_x)+28);
		}
		
		public function get back ():Number
		{
			return (container.z);
		}
		
		public function get front ():Number
		{
			return container.z +(grid_size_z+28);
		}
		
		public function get bottom ():Number
		{
			return container.y;
		}
		
		public function get top ():Number
		{
			return container.y;
		}
		
		public function get name():String {
			return container.name;
		}
		
		public function get sortSize():Number {
			return container.width+container.height;
		}
		
		public function get sortPriority():Number {
			return container.sortPriority;
		}
		
		//public function set sortPriority(value:Number):void {
			//container.sortPriority=value;
		//}
		
		public function get depth():Number {
			//return container.depth + ((isoHeight*.3) +(isoWidth)*.5)*.1+sortPriority;
			return container.depth +sortPriority;
			//return container.depth;
		}
		
		public function get isoWidth():Number
		{
			return isoItem.grid_size_x;
		}
		
		public function get isoHeight():Number
		{
			return isoItem.grid_size_z;
		}
	}

}