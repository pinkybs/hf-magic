package happyfish.scene.personAction 
{
	import flash.geom.Rectangle;
	import happyfish.model.vo.BasicVo;
	import happyfish.scene.astar.Node;
	import happyfish.scene.world.grid.IsoItem;
	/**
	 * ...
	 * @author jj
	 */
	public class PersonActionVo extends BasicVo
	{
		public var type:String;
		public var isoItem:IsoItem;
		public var targetNodeArr:Array;
		public var targetNode:Node;
		public var rect:Rectangle;
		public var rectArr:Array;
		public var times:uint;
		public var showTime:uint;
		public var iconClass:String;
		public var chat:String;
		public var num:int;
		public var triggerName:String;
		public var towardsNode:Node;
		public var towardsNodeArr:Array;
		
		public function PersonActionVo() 
		{
			
		}
		
		override public function setData(obj:Object):BasicVo 
		{
			super.setData(obj);
			if (targetNodeArr) 
			{
				targetNode = new Node(targetNodeArr[0], targetNodeArr[1]);
			}
			if (towardsNodeArr) 
			{
				towardsNode = new Node(towardsNodeArr[0], towardsNodeArr[1]);
			}
			if (rectArr) 
			{
				rect = new Rectangle(rectArr[0], rectArr[1], rectArr[2], rectArr[3]);
			}
			return this;
		}
		
		public function clone():PersonActionVo {
			var tmp:PersonActionVo = new PersonActionVo();
			tmp.type = type;
			tmp.isoItem = isoItem;
			tmp.targetNode = targetNode;
			tmp.rect = rect;
			tmp.times = times;
			tmp.showTime = showTime;
			tmp.iconClass = iconClass;
			tmp.chat = chat;
			tmp.num = num;
			tmp.triggerName = triggerName;
			
			return tmp;
		}
		
	}

}