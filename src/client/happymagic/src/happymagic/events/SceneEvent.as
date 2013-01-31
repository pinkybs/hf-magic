package happymagic.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author jj
	 */
	public class SceneEvent extends Event 
	{
		//场景渲染完成
		public static const SCENE_COMPLETE:String = "SceneComplete"; 
		//地板和墙完成
		public static const WALL_COMPLETE:String = "WallComplete"; 
		//diy开始
		public static const START_DIY:String = "diyStart";
		//diy结束
		public static const DIY_FINISHED:String = "diyFinished";
		public static const DIY_CANCELDIY:String = "diyCancel";
		//要求切换
		public static const CHANGE_SCENE:String = "changeScene";
		//场景清空事件
		public static const SCENE_CLEARED:String = "SceneCleared";
		
		//场景数据完成
		public static const SCENE_DATA_COMPLETE:String = "sceneDataComplete";
		
		public var uid:String;
		public function SceneEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new SceneEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("SceneEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}