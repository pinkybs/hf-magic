package happymagic.events 
{
	import adobe.utils.CustomActions;
	import flash.events.Event;
	
	/**
	 * ...
	 * @author jj
	 */
	public class ActionStepEvent extends Event 
	{
		//新手引导的事件
		public static const ON_DOOR_READY:String = "onDoorReady";
		public static const ON_DOOR_CLICK:String = "onDoorClick";
		public static const ON_STUDENT_TODESK:String = "onStudentToDesk";
		public static const ON_STUDENT_NEED_CLICK:String = "onStudentNeedClick";
		public static const ON_STUDENT_TEACHCLICK:String = "onStudentTeachClick";
		
		public static const ON_STUDENT_EVENT_HAPPEN:String = "onStudentEventHappen";
		public static const ON_STUDENT_EVENT_CLICK:String = "onStudentEventClick";
		public static const ON_TEACH_COMPLETE:String = "onTeachComplete";
		public static const ON_DESKCRYSTAL_CLICK:String = "onDeskCrystalClick";
		public static const ON_TRANSTAB_CLICK:String = "onTransTabClick";
		public static const ON_USETRANSBTN_CLICK:String = "onUseTransBtnClick";
		public static const ON_USETRANS_COMPLETE:String = "onUseTransComplete";
		public static const ON_MIX_COMPLETE:String = "onMixComplete";
		public static const ON_FINISH_GUIDE:String = "onFinishGuide";
		
		public static const ACTION_HAPPEN:String = "actionHappen";
		public static const ON_TEACHMAGIC:String = "onTeachMagic";//点击教魔法按钮
		public static const ON_MENUCONJURE:String = "onMenuConjure"; //点击施法菜单按钮
		public static const ON_CHANGEART:String = "onChangeArt";//点击变化术按钮
		public static const ON_MENUMIXCLICK:String = "onMenuMixClick";//点击合成术菜单按钮
	    public static const ON_MIXBUTTON:String = "onMixButton";//点击合成术里的合成按钮
		
		public static const ON_TEACHMAGICCONTACTEVENT :String = "onTeachMagicContactevent"; //教学生面板里的关闭发出的事件
		public static const ON_CHANGEARTCONTACTEVENT:String = "onChangeArtContactevent";    //变化术使用面板关闭的时候发出的事件
		public static const ON_USETRANSCOMPLETECONTACTEVENT:String = "onUseTransCompleteContactevent"; //同上
		
		public static const ON_MIXBUTTONCONTACTEVENT:String =  "onMixButtonContactevent";//合成术面板关闭的时候发出的事件
		public static const ON_MIXCOMPLETECONTACTEVENT:String = "onMixCompleteContactevent";//同上
		public var actType:String;
		
		public function ActionStepEvent(type:String,_actType:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			actType = _actType;
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new ActionStepEvent(type, actType,bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("ActionStepEvent", "actType","type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}