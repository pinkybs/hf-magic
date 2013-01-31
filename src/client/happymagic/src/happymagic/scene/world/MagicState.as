package happymagic.scene.world
{
	import happyfish.scene.world.control.MouseCursorAction;
	import happyfish.scene.world.WorldState;
	import happymagic.scene.world.control.MouseDefaultAction;
	
	/**
	 * ...
	 * @author Beck
	 */
	public class MagicState extends WorldState
	{
		public static var instances:Array;
		public function MagicState() 
		{
			super();
		}
		
		public static function getState($uid:int, $scene:int):WorldState
		{
			if (!instances[$uid][$scene]) {
				instances[$uid][$scene] = new MagicState();
			}
			return instances[$uid][$scene];
		}
		
	}

}