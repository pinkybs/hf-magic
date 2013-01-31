package happymagic.scene.world.grid.item 
{
	import happyfish.scene.world.WorldState;
	/**
	 * ...
	 * @author jj
	 */
	public class RoomUpItem extends Decor
	{
		
		public function RoomUpItem($data:Object, $worldState:WorldState,__callBack:Function=null) 
		{
			$data.class_name = "roomUpIsoItemUi";
			super($data, $worldState, __callBack);
			typeName = "RoomUpItem";
		}
		
		override public function set diyState(value:Boolean):void 
		{
			super.diyState = value;
			
			if (value) 
			{
				visible = false;
				
			}else {
				visible = true;
			}
		}
	}

}