package happymagic.scene.world.bigScene 
{
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	/**
	 * ...
	 * @author slamjj
	 */
	public class StoryPersonView extends Person 
	{
		
		public function StoryPersonView($data:Object, $worldState:WorldState,__callBack:Function=null) 
		{
			super($data, $worldState, __callBack);
			typeName = "StoryPerson";
		}
		
		override protected function makeView():IsoSprite 
		{
			return super.makeView();
		}
		
	}

}