package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.manager.local.LocaleWords;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.ui.AwardResultView;
	import happymagic.events.StudentEvent;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.scene.world.grid.person.Student;
	/**
	 * ...
	 * @author ZC
	 */
	public class StudentAwardCommand extends BaseDataCommand 
	{
		private var _arr:Array;
		public var sid:uint;
		public function StudentAwardCommand(callback:Function=null) 
		{
			super(callback);
	    }
		
		public function change(_sid:uint):void {
			sid = _sid;
			
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("studentAward"), { sid:sid} );
			
			loader.load(request);   
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			if (data.result.isSuccess) 
			{
				var awards:Array = new Array();
				var i:int = 0;
				
				if (data.result)
				{
					if (data.result.exp)
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_EXP, num:data.result.exp } ));
					}
					
					if (data.result.coin)
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_COIN, num:data.result.coin } ));
					}					
				}
				
				if (data.addItem)
				{
					for (i = 0; i < data.addItem.length; i++) 
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.ITEM, id:data.addItem[i].i_id, num:data.addItem[i].num } ));
					}
				}
				
				if (data.addDecorBag)
				{
					for (i = 0; i < data.addDecorBag.length; i++) 
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.DECOR, id:data.addDecorBag[i].d_id, num:data.addDecorBag[i].num } ));
					}
				}
				
				var awardwin:AwardResultView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_AWARD_RESULT, ModuleDict.MODULE_AWARD_RESULT_CLASS,true) as AwardResultView;
					awardwin.setData( { name:LocaleWords.getInstance().getWord("studentAwardTile"), awards:awards } );
					
				//通知场景中学生更新表现
				//EventManager.getInstance().dispatchEvent(new StudentEvent(StudentEvent.REFRESH_INSCENE_STUDENT, sid);
			}
			
			commandComplete();
		}
	}

}