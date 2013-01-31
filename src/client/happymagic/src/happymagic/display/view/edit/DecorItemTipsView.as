package happymagic.display.view.edit 
{
	import happymagic.model.vo.DecorVo;
	/**
	 * ...
	 * @author jj
	 */
	public class DecorItemTipsView extends diyItem_tipsUI
	{
		
		public function DecorItemTipsView() 
		{
			starBar.gotoAndStop(1);
		}
		
		public function setData(data:DecorVo):void {
			item_name.text = data.name;
			
			starBar.gotoAndStop(data.level);
			affect_num.text = "+"+data.max_magic.toString();
			
		}
		
	}

}