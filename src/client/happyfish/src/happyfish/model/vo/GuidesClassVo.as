package happyfish.model.vo 
{
	
	/**
	 * ...
	 * @author jj
	 */
	public class GuidesClassVo extends BasicVo
	{
		public var gid:uint;
		public var index:uint;
		public var name:String;
		public var icon:String;
		public var eventType:Array;
		public var chats:Array;
		public var actTips:Array;
	    public var contact:Array;
		public var contactevent:Array;
		public function GuidesClassVo() 
		{
			
		}
		
		override public function setData(obj:Object):BasicVo 
		{
			for (var name:String in obj) 
			{
				if ( this.hasOwnProperty(name)) 
				{
					if ((name=="eventType" || name=="chats" || name=="actTips" ||name == "contact"||name =="contactevent") && obj[name] is String) 
					{
						this[name] = (obj[name] as String).split("##");
					}else {
						this[name] = obj[name];
					}
					
				}
			}
			return this;
		}
		
		public function get endStepEvent():String {
			for (var i:int = eventType.length-1; i >=0 ; i--) 
			{
				if (eventType[i]!="blank") 
				{
					return eventType[i];
				}
			}
			return null;
		}
		
		public function set endStepEvent(value:String):void {
			
		}
		
	}

}