package happymagic.display.view.diary 
{
	import flash.display.MovieClip;
	import happyfish.display.ui.GridItem;
	import happyfish.utils.DateTools;
	import happymagic.model.vo.DiaryVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class DiaryListItemView extends GridItem
	{
		private var iview:DailyRecordListItem;
		private var data:DiaryVo;
		
		public function DiaryListItemView(uiview:MovieClip) 
		{
			super(uiview);
			iview = uiview as DailyRecordListItem;
		}
		
		override public function setData(value:Object):void 
		{
			data = value as DiaryVo;
			
			var now:Date = new Date();
			if (now.getFullYear()== data.createTime.getFullYear() && now.getMonth()== data.createTime.getMonth() && now.getDate() == data.createTime.getDate()) 
			{
				iview.timeTxt.text = DateTools.getTimeString(data.createTime);
			}else {
				iview.timeTxt.text = DateTools.getDateString(data.createTime, ".") + " " + DateTools.getTimeString(data.createTime);
			}
			
			iview.contentTxt.htmlText = data.content;
			iview.happyface.gotoAndStop(data.icon);

		}
	}

}