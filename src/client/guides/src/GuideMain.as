package 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import happyfish.guides.control.GuidesControl;
	import happyfish.guides.view.GuidesView;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.ModuleMvType;
	import happyfish.model.vo.GuidesVo;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.task.TaskListView;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;

	/**
	 * ...
	 * @author jj
	 */
	public class GuideMain extends ActModuleBase 
	{
		private var control:GuidesControl;

		public function GuideMain():void 
		{
			//if (stage) init();
			//else addEventListener(Event.ADDED_TO_STAGE, init);
			
			//init();
		}

		override public function init(actVo:ActVo, _type:uint = 1):void 
		{
            super.init(DataManager.getInstance().getActByName("guideact"), 1);
			
			var tmp:GuidesView = DisplayManager.uiSprite.addModule("guidesView", "happyfish.guides.view.GuidesView",false, 
				AlginType.Cl, 0, -65, -100, 0, ModuleMvType.FROM_LEFT) as GuidesView;
			
			var arr:Array = DataManager.getInstance().guides;
			
			var tastlistview:TaskListView = ModuleManager.getInstance().getModule(ModuleDict.MODULE_TASKLIST) as TaskListView;
			tastlistview.closeView();
			
			tmp.setData(arr);
			control = new GuidesControl(tmp);
			control.start();
			
			
		}
		

	}

}