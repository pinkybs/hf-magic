package happyfish.manager.module.vo 
{
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.ModuleMvType;
	/**
	 * ...
	 * @author jj
	 */
	public class ModuleVo
	{
		public var name:String;
		public var className:String;
		public var algin:String=AlginType.CENTER;
		//最终出现位置
		public var x:int;
		public var y:int;
		//出现动画方式
		public var mvType:String=ModuleMvType.CNETER;
		public var mvTime:Number = .5;
		public var fx:int;
		public var fy:int;
		//是否单独显示的窗口
		public var single:Boolean=false;
		public var layer:uint=1;
		public function ModuleVo() 
		{
			
		}
		
		public function setValue(value:Object):ModuleVo {
			for (var name:String in value) 
			{
				this[name] = value[name];
			}
			return this;
		}
		
	}

}