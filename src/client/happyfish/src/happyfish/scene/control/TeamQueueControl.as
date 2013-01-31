package happyfish.scene.control 
{
	import happyfish.scene.astar.Node;
	import happyfish.scene.world.grid.Person;
	/**
	 * ...
	 * @author ...
	 */
	public class TeamQueueControl 
	{
		private var persons:Vector.<Person>;
		private var path:Vector.<Node>;
		public function TeamQueueControl() 
		{
			
		}
		
		public function addPerson(person:Person):void {
			persons.push(person);
		}
		
//		public function 
		
	}

}