package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.manager.local.LocaleWords;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.ui.AwardResultView;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happyfish.feed.vo.FeedVo;
	/**
	 * ...
	 * @author jj
	 */
	public class ShareFeedCommand extends BaseDataCommand
	{
		private var feed:FeedVo;
		
		public function ShareFeedCommand() 
		{
			
		}
		
		public function share(_feed:FeedVo):void {
			feed = _feed;
			
			createLoad();
			
			createRequest(InterfaceURLManager.getInstance().getUrl("feedAward"), { id:feed.id, value:feed.value } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			
			if (data.result.isSuccess) 
			{
				
				if (feed.awards.length>0) 
				{
					if (DataManager.getInstance().currentUser.feedNum>0) 
					{
						//表现奖励窗口
						var awardwin:AwardResultView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_AWARD_RESULT, ModuleDict.MODULE_AWARD_RESULT_CLASS,true) as AwardResultView;
						awardwin.setData( { name:feed.content, awards:feed.awards } );
						DataManager.getInstance().currentUser.feedNum--;
					}else {
						//提示次数已用完
						EventManager.getInstance().showSysMsg(LocaleWords.getInstance().getWord("feedAwardOut"));
					}
					
				}
			}
			
			commandComplete();
		}
		
	}

}