package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author jj
	 */
	public class LevelInfoVo extends BasicVo
	{
		public var level:uint;
		public var door_limit:uint;
		public var desk_limit:uint;
		public var student_limit:uint;
		public var magic_limit:uint;
		public var decors:Array;
		public var items:Array;
		public var tile_x_length:uint;
		public var tile_z_length:uint;
		public var gem:uint;
		public var coin:uint;
		public var tile:String;
		
		public var max_exp:uint;
		public function LevelInfoVo() 
		{
			
		}
		
	}

}