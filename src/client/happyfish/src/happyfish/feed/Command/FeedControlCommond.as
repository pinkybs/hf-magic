package happyfish.feed.Command 
{
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.external.ExternalInterface;
	import flash.utils.setTimeout;
	import happyfish.feed.vo.FeedVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.display.view.ui.AwardResultView;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class FeedControlCommond 
	{

		private var feedvo:FeedVo;
		private var id:uint;
		public function FeedControlCommond() 
		{
			
		}
		
		//判断FEED的按钮是否存在
		public function isExist(_id:uint):Boolean
		{
			id = _id;
			
			if (DataManager.getInstance().getFeedClass(id))
			{
				return true;
			}			
			return false;
		}
		
		public function init(iview:DisplayObject):void
		{
			iview.addEventListener(MouseEvent.CLICK,clickrun);
		}
		
		public function clickrun(e:MouseEvent = null):void 
		{
			feedvo = DataManager.getInstance().getFeedClass(id);
			
			if (DataManager.getInstance().currentUser.feedNum < 5)
			{
				ExternalInterface.call("sendFeed", feedvo.value);
				
				setTimeout(feedRequest, 2000);
				
			}
            else
			{
				ExternalInterface.call("sendFeed", feedvo.value);
				
				EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("feedlanguage"));
			}

		}
		
		private function feedRequest():void 
		{
			var feedDataRequestCommond:FeedDataRequestCommond = new FeedDataRequestCommond();
		    feedDataRequestCommond.setData(feedvo.id);
			feedDataRequestCommond.addEventListener(Event.COMPLETE, feedDataRequestCommondComplete);			
		}
		
		private function feedDataRequestCommondComplete(e:Event):void 
		{			
			e.target.removeEventListener(Event.COMPLETE, feedDataRequestCommondComplete);
			
			if (e.target.data.result.status == -1)
			{
				return;
			}
			DataManager.getInstance().currentUser.feedNum += 1;
                //表现奖励窗口
				var awards:Array = new Array();
				var i:int = 0;				
				if (e.target.data.result)
				{
					if (e.target.data.result.coin)
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_COIN, num:e.target.data.result.coin } ));
					}
					if (e.target.data.result.exp)
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_EXP, num:e.target.data.result.exp } ));
					}
				}
			
				
				if (e.target.data.addItem)
				{
					for (i = 0; i < e.target.data.addItem.length; i++) 
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.ITEM, id:e.target.data.addItem[i].i_id, num:e.target.data.addItem[i].num } ));
					}
				}
				
				if (e.target.data.addDecorBag)
				{
					for (i = 0; i < e.target.data.addDecorBag.length; i++) 
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.DECOR, id:e.target.data.addDecorBag[i].d_id, num:e.target.data.addDecorBag[i].num } ));
					}
				}

				var awardwin:AwardResultView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_AWARD_RESULT, ModuleDict.MODULE_AWARD_RESULT_CLASS,true) as AwardResultView;
				awardwin.setData( { name:"分享奖励", awards:awards } );				
			
		}
	}

}