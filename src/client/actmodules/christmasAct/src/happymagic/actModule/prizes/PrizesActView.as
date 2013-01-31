package happymagic.actModule.prizes 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.display.FiltersDomain;
	import happymagic.actModule.prizes.model.PrizesAwardVo;
	import happymagic.actModule.prizes.model.PrizesGetCommand;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.display.view.ui.AwardResultView;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.ResultVo;
	
	/**
	 * ...
	 * @author 
	 */
	public class PrizesActView extends UISprite 
	{
		private var iview:christmasUi;
		private var data:ActVo;
		private var itemCid:uint;
		private var curNum:uint;
		public function PrizesActView() 
		{
			super();
			_view = new christmasUi();
			iview = _view as christmasUi;
			
			iview.hasGetBtn_0.visible = false;
			iview.hasGetBtn_1.visible = false;
			iview.hasGetBtn_2.visible = false;
			
			iview.hasIcon_0.visible = false;
			iview.hasIcon_1.visible = false;
			iview.hasIcon_2.visible = false;
			
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			
		}
		
		public function setData(act:ActVo):void 
		{
			data = act;
			
			InterfaceURLManager.getInstance().setUrl("prizesAwardGet", data.moduleData.request["prizesAwardGet"]);
			
			var awards:Vector.<PrizesAwardVo> = data.moduleData.awards;
			for (var i:int = 0; i < awards.length; i++) 
			{
				initAward(i,awards[i]);
			}
			
			itemCid = data.moduleData.priceItemCid;
			initNum();
			
		}
		
		private function initNum():void {
			curNum = DataManager.getInstance().getItemNum(itemCid);
			iview.curNumTxt.text = "x"+curNum.toString();
		}
		
		/**
		 * 设置奖品兑换状态
		 * @param	index
		 * @param	awardVo
		 */
		private function initAward(index:uint,awardVo:PrizesAwardVo):void 
		{
			awardVo.index = index;
			iview["awardPrice_" + index.toString()].text = "x" + awardVo.price.toString();
			if (awardVo.state) 
			{
				iview["hasIcon_" + index.toString()].visible = true;
				iview["getBtn_" + index.toString()].visible = false;
				iview["hasGetBtn_" + index.toString()].visible = true;	
			}else {
				iview["hasIcon_" + index.toString()].visible = false;
				iview["getBtn_" + index.toString()].visible = true;
				iview["hasGetBtn_" + index.toString()].visible = false;
			}
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case iview.closeBtn:
					closeMe(true);
				break;
				
				case iview.getBtn_0:
				case iview.getBtn_1:
				case iview.getBtn_2:
				case iview.hasGetBtn_0:
				case iview.hasGetBtn_1:
				case iview.hasGetBtn_2:
					getAward(uint(e.target.name.split("_")[1]));
				break;
			}
		}
		
		private function getAward(index:Number):void 
		{
			var award:PrizesAwardVo = data.moduleData.awards[index] as PrizesAwardVo;
			if (award.state) 
			{
				//已领过
				EventManager.getInstance().showSysMsg("您已经兑换过该奖品了~", PiaoMsgType.TYPE_BAD_STRING);
				return;
			}
			
			if (curNum<award.price) 
			{
				EventManager.getInstance().showSysMsg("您的袜子不够哦~", PiaoMsgType.TYPE_BAD_STRING);
				return;
			}
			
			iview.mouseChildren = false;
			EventManager.getInstance().showLoading();
			
			var loader:PrizesGetCommand = new PrizesGetCommand();
			loader.addEventListener(Event.COMPLETE, getAward_complete);
			loader.getAward(InterfaceURLManager.getInstance().getUrl("prizesAwardGet"), award.id);
		}
		
		public function getAwardVoById(id:uint):PrizesAwardVo {
			var tmp:PrizesAwardVo;
			for (var i:int = 0; i < data.moduleData.awards.length; i++) 
			{
				tmp = data.moduleData.awards[i] as PrizesAwardVo;
				if (tmp) 
				{
					if (tmp.id==id) 
					{
						return tmp;
					}
				}
			}
			return null;
		}
		
		private function setAwardVo(award:PrizesAwardVo):void {
			var tmp:PrizesAwardVo;
			for (var i:int = 0; i < data.moduleData.awards.length; i++) 
			{
				tmp = data.moduleData.awards[i] as PrizesAwardVo;
				if (tmp) 
				{
					if (tmp.id==award.id) 
					{
						data.moduleData.awards[i] = award;
					}
				}
			}
		}
		
		private function getAward_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, getAward_complete);
			
			iview.mouseChildren = true;
			
			EventManager.getInstance().hideLoading();
			
			var tmpdata:Object = (e.target as PrizesGetCommand).data;
			var result:ResultVo = tmpdata.result as ResultVo;
			if (result.isSuccess) 
			{
				var i:int;
				//表现领到的礼物
				var awards:Array = new Array();
				if (tmpdata.addItem)
				{
					for (i = 0; i < tmpdata.addItem.length; i++) 
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.ITEM, id:tmpdata.addItem[i].i_id, num:tmpdata.addItem[i].num } ));
					}
				}
				
				if (tmpdata.addDecorBag)
				{
					for (i = 0; i < tmpdata.addDecorBag.length; i++) 
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.DECOR, id:tmpdata.addDecorBag[i].d_id, num:tmpdata.addDecorBag[i].num } ));
					}
				}
				
				var awardwin:AwardResultView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_AWARD_RESULT, ModuleDict.MODULE_AWARD_RESULT_CLASS) as AwardResultView;
				awardwin.setData( { name:"圣诞礼物", awards:awards } );
				
				var award:PrizesAwardVo = getAwardVoById((e.target as PrizesGetCommand).id);
				award.state = 1;
				initAward(award.index, award);
				
				initNum();
			}
		}
		
		
		
	}

}