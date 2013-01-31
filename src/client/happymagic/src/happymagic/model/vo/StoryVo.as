package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author slamjj
	 */
	public class StoryVo extends BasicVo 
	{
		public var actions:Array;
		public function StoryVo() 
		{
			
		}
		
		override public function setData(obj:Object):BasicVo 
		{
			actions = new Array();
			for (var i:int = 0; i < obj.actions.length; i++) 
			{
				var item:Object = obj.actions[i];
				actions.push(new StoryActionVo().setData(item));
			}
			
			return this;
		}
		
	}

}