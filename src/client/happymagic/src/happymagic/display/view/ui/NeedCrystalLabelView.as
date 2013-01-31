package happymagic.display.view.ui 
{
	import flash.text.TextFieldAutoSize;
	import happyfish.utils.HtmlTextTools;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.MoneyType;
	/**
	 * ...
	 * @author jj
	 */
	public class NeedCrystalLabelView extends needCrystalLabel
	{
		private var type:uint;
		private var num:int;
		public var enough:Boolean;
		
		public function NeedCrystalLabelView(_type:uint,_num:int,showEnough:Boolean=true) 
		{
			type = _type;
			num = _num;
			
			needCrystalIcon.gotoAndStop(type);
			needCrystalTxt.autoSize = TextFieldAutoSize.LEFT;
			needCrystalTxt.wordWrap  = false;
			enough = DataManager.getInstance().getEnouthCrystalType(type, num);
			if (!showEnough || enough ) 
			{
				HtmlTextTools.setTxtSaveFormat(needCrystalTxt,num.toString(),0xffffff);
				//needCrystalTxt.text = num.toString();
			}else {
				HtmlTextTools.setTxtSaveFormat(needCrystalTxt,num.toString(),0xFF0000);
				//needCrystalTxt.htmlText = HtmlTextTools.fontWord(num.toString(),"#FF0000",13,"Arial");
			}
			
		}
		
	}

}