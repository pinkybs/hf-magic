package happymagic.display.view.task 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.GridPage;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.ui.events.GridPageEvent;
	
	/**
	 * ...
	 * @author jj
	 */
	public class TaskGridPageView extends DefaultListView
	{
		
		
		public function TaskGridPageView(uiview:MovieClip,_container:DisplayObjectContainer) 
		{
			super(uiview as MovieClip, _container);
			
			pageLength = 4;
			
			init(120, 300, 120, 50);
			
			useBounds = true;
		}
		
		override protected function itemSelectFun(e:GridPageEvent):void 
		{
			if (selectCallBack!=null) 
			{
				selectCallBack(e.item);
			}
		}
		
		override protected function createItem(value:Object):GridItem 
		{
			var tmp:GridItem;
			
			tmp = new TaskListItemView(new MovieClip());
			tmp.setData(value);
			
			return tmp;
		}
	}

}