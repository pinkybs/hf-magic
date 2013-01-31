package com.friendsofed.isometric
{
	public class Point3D
	{
		public var x:Number;
		public var y:Number;
		public var z:Number;
		
		public function Point3D(x:Number = 0, y:Number = 0, z:Number = 0)
		{
			this.x = x;
			this.y = y;
			this.z = z;
		}
		
		public function clone():Point3D {
			return new Point3D(x, y, z);
		}
		
		public function toString():String {
			return x + "," + y + "," + z;
		}
	}
}