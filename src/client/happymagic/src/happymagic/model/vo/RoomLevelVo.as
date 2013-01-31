package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author jj
	 */
	public class RoomLevelVo extends BasicVo
	{
		public var level:uint;
		public var needMaxMp:uint;
		public var student_limit:uint;
		public var desk_limit:uint;
		public var coin:uint;
		public var gem:uint;
		public var items:Array;
		public var decors:Array;
		public function RoomLevelVo() 
		{
			
		}
		
		public function setValue(value:Object):RoomLevelVo {
			
			var i:int;
			for (var name:String in value) 
			{
				if ( this.hasOwnProperty(name)) 
				{
					if (name=="items") 
					{
						items = new Array();
						for (i = 0; i < value.items.length; i++) 
						{
							items.push(new ItemVo().setValue({i_id:value.items[i][0],num:value.items[i][1]}));
						}
					}else if(name=="decors"){
						decors = new Array();
						for (i = 0; i < value.decors.length; i++) 
						{
							items.push(new DecorVo().setValue({d_id:value.decors[i][0],num:value.decors[i][1]}));
						}
					}else {
						this[name] = value[name];
					}
					
				}
			}
			
			return this;
			
		}
		
	}

}