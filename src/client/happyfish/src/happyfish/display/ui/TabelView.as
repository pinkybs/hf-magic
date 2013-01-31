package happyfish.display.ui
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.filters.ColorMatrixFilter;
	import flash.geom.Rectangle;
	import happyfish.utils.display.FiltersDomain;
	
	[Event(name = "select", type = "flash.events.Event")]
	
	/**
	 * 标签组件
	 * @author slamjj
	 */
	public class TabelView extends Sprite
	{
		private var tabelsArr:Array;
		private var tabelsValueArr:Array;
		public var selectIndex:uint;
		public var btwX:Number=-1;
		public var btwY:Number=-1;
		
		public function TabelView() 
		{
			tabelsArr = new Array();
			tabelsValueArr = new Array();
			
			addEventListener(MouseEvent.CLICK, clickFun, true);
			
			init();
		}
		
		/**
		 * 设置指定index的标签的状态
		 * @param	index
		 * @param	flag
		 */
		public function setAbleItem(index:uint,flag:Boolean):void {
			var tmp:MovieClip = getItem(index);
			if (flag) 
			{
				tmp.filter = [];
				tmp.mouseEnabled = 
				tmp.mouseChildren = true;
			}else {
				tmp.filter = [FiltersDomain.grayFilter];
				tmp.mouseChildren = 
				tmp.mouseEnabled = false;
			}
		}
		
		/**
		 * 设置标签组的内容
		 * @param	... args  例:[mc,value],[tab_desk,"课桌"]
		 * @example	[mc,value],[tab_desk,"课桌"]
		 */
		public function setTabs(... args):void {
			tabelsArr = new Array();
			
			//TODO 可以直接调addTabs方法
			var tmp:MovieClip;
			for (var i:int = 0; i < args.length; i++) 
			{
				tmp = args[i][0] as MovieClip;
				tabelsArr.push(tmp);
				if (args[i][1]) 
				{
					tabelsValueArr.push(args[i][1]);
				}else {
					tabelsValueArr.push("");
				}
				tmp.index = i;
			}
			
			sortBtns();
			
			
		}
		
		/**
		 * 增加标签
		 * @param	... args
		 */
		public function addTabs(... args):void {
			var tmp:MovieClip;
			for (var i:int = 0; i < args.length; i++) 
			{
				tmp = args[i][0] as MovieClip;
				tabelsArr.push(tmp);
				if (args[i][1]) 
				{
					tabelsValueArr.push(args[i][1]);
				}else {
					tabelsValueArr.push("");
				}
				tmp.index = tabelsArr.length-1;
			}
			
			sortBtns();
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			var target:MovieClip ;
			if (e.target.parent is TabelView) 
			{
				target = e.target as MovieClip;
			}else {
				target = e.target.parent;
			}
			//var target:MovieClip = e.target.parent;
			select(target.index);
			
		}
		
		/**
		 * 指到标签
		 * @param	index	从0开始的标签index
		 * @param	dispatch
		 */
		public function select(index:uint,dispatch:Boolean=true):void {
			selectIndex = index;
			for (var i:int = 0; i < tabelsArr.length; i++) 
			{
				if (i==index) 
				{
					setItemSelect(i, true);
				}else {
					setItemSelect(i, false);
				}
			}
			if (dispatch) 
			{
				dispatchEvent(new Event(Event.SELECT));
			}
			
			mouseChildren = true;
		}
		
		/**
		 * 只是选中,但不会派发选中事件
		 * @param	index
		 * @param	value
		 */
		public function setItemSelect(index:uint,value:Boolean):void {
			var tmp:MovieClip = getItem(index);
			if (tmp) 
			{
				tmp.select.visible = value;
				tmp.unselect.visible = !value;
			}
			
		}
		
		private function getItem(index:uint):MovieClip {
			return tabelsArr[index];
		}
		
		public function get selectValue():* {
			return tabelsValueArr[selectIndex];
		}
		
		private function sortBtns():void {
			var tmpRect:Rectangle;
			var tmpRect2:Rectangle;
			var tmp:MovieClip;
			for (var i:int = 0; i < tabelsArr.length; i++) 
			{
				tmp = tabelsArr[i] as MovieClip;
				addChild(tmp);
				tmp.visible = true;
				tmp.x = 0;
				tmp.y = 0;
				if (i==0) 
				{
					tmp.x = 0;
					tmp.y = 0;
				}else {
					tmpRect = (tabelsArr[i - 1] as MovieClip).getBounds(this);
					tmpRect2 = tmp.getBounds(this);
					if(btwX!=-1) tmp.x = tmpRect.right-tmpRect2.x + btwX;
					if(btwY!=-1) tmp.y = tmpRect.bottom-tmpRect2.y + btwY;
				}
			}
		}
		
		private function init():void
		{
			selectIndex = 0;
			setItemSelect(0, true);
		}
		
	}

}