package happyfish.actModule.giftGetAct.commond 
{
	import flash.events.Event;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happyfish.actModule.giftGetAct.model.vo.GiftVo;
	import happymagic.model.command.BaseDataCommand;
	/**
	 * ...
	 * @author ZC
	 */
	public class GiftGetActInitStaticCommond extends BaseDataCommand
	{
		
		public function GiftGetActInitStaticCommond() 
		{
			
		}
		
		public function setData():void 
		{
			createLoad();
			createRequest(GiftDomain.getInstance().getInterfaceUrl("GiftGetActInitStatic"));
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
            super.load_complete(e);			
			var temp:Array = new Array();
			
			if (objdata.gifts)
			{
				//礼物数据
				for (var i:int = 0; i < objdata.gifts.length; i++) 
				{
					temp.push(new GiftVo().setData(objdata.gifts[i]));
				}						
			}
			
			for (i = 0; i < temp.length; i++)
			{
				temp[i].name = GiftDomain.getInstance().getGiftDiaryVoName(temp[i].type,temp[i].id);
				temp[i].className = GiftDomain.getInstance().getGiftDiaryVoClassName(temp[i].type, temp[i].id);
			}			
			
			
			GiftDomain.getInstance().setVar("gifts", temp);
			
			commandComplete();
		}
	}

}