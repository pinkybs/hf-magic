package happyfish.display.ui 
{
	import com.greensock.easing.Cubic;
	import com.greensock.TweenLite;
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.MouseEvent;
	import happyfish.display.ui.events.GridPageEvent;
	import happyfish.display.ui.GridView;
	import happyfish.utils.display.BtnStateControl;
	import happymagic.manager.DataManager;
	/**
	 * 表格列表管理类
	 * @author jj
	 */
	public class GridPage extends EventDispatcher
	{
		public var iview:Sprite;
		public var data:Array;
		private var totalData:Array;
		
		
		public var currentPage:uint;
		public var pageLength:uint;
		
		private var filterKey:String;
		private var filterValue:*;
		public var blankItemUi:Class;
		protected var grid:GridView;
		protected var container:DisplayObjectContainer;
		protected var prevBtn:DisplayObject;
		protected var nextBtn:DisplayObject;
		
		//gridItem内使用的代表数量的keyName
		public var numKey:String;
		//代表id的keyName,用来判断是否同一物件时使用
		public var cidKey:String;
		//增加每项内容内数量属性的方法
		public var addNumFunc:String;
		//减少每项内容内数量属性的方法
		public var delNumFunc:String;
		
		public var selectCallBack:Function;
		//是否在不需要时隐藏按钮
		private var _hideButtonFlag:Boolean;
		public var tweenTime:Number=.4;
		public var tweenDelay:Number=.2;
		
		/**
		 * 列表组件基类
		 * @param	ui		列表UI
		 * @param	_container		列表所在容器
		 * @param	_hideButton		是否在不需要时隐藏按钮
		 */
		public function GridPage(ui:Sprite,_container:DisplayObjectContainer,_hideButton:Boolean=true ) 
		{
			container = _container;
			iview = ui;
			
			hideButtonFlag = _hideButton;
			
			prevBtn = iview.getChildByName("prevBtn");
			nextBtn = iview.getChildByName("nextBtn");
			
			setBtnVisible(prevBtn, false);
			setBtnVisible(nextBtn, false);
			container.addChild(iview);
			
			iview.addEventListener(MouseEvent.CLICK, clickFun);
			
			iview.addEventListener(GridPageEvent.ITEM_SELECT, itemSelectFun,true);
			
		}
		
		protected function itemSelectFun(e:GridPageEvent):void 
		{
			if (selectCallBack!=null) 
			{
				selectCallBack.apply(null,[e]);
			}
		}
		
		public function init(gridWidth:Number, gridHeight:Number, tileWidth:Number, tileHeight:Number,gridX:Number=0,gridY:Number=0, tileAlgin:String="TL", algin:String="TL"):void
		{
			grid = new GridView(gridWidth, gridHeight, tileWidth, tileHeight, tileAlgin, algin);
			
			grid.x = gridX;
			grid.y = gridY;
			iview.addChild(grid);
		}
		
		/**
		 * 设置数据,并依key保留数据
		 * @param	value			数据组
		 * @param	key				数据过滤依据字段
		 * @param	keyV			key为该值的数据保留
		 * @param	saveCurrentPage	是否还停在当前页页
		 */
		public function setData(value:Array, key:String = "", keyV:*= null, saveCurrentPage:Boolean = false):void {
			if (!value) 
			{
				return;
			}
			totalData = value;
			filterKey = key;
			filterValue = keyV;
			
			data = filterData();
			
			if (!saveCurrentPage) 
			{
				currentPage = 1;
			}
			
			initPage();
		}
		
		private function filterData():Array {
			if (!filterKey || !filterValue) 
			{
				return totalData;
			}
			var tmp:Array = new Array();
			for (var i:int = 0; i < totalData.length; i++) 
			{
				if (totalData[i][filterKey]==filterValue) 
				{
					tmp.push(totalData[i]);
				}
			}
			return tmp;
		}
		
		public function initPage():void {
			
			if(grid) grid.clearAll();
			
			if (iview["pageNumTxt"]) iview["pageNumTxt"].text = currentPage+"/" + Math.ceil(data.length / pageLength);
			
			var addNum:uint = (currentPage-1) * pageLength;
			var tmp:GridItem;
			for (var i:int = 0; i < pageLength; i++) 
			{
				if (data[i+addNum]) 
				{
					tmp = createItem(data[i + addNum]);
					tmp.index = i + addNum;
					grid.add(tmp);
				}else {
					tmp = createBlankItem();
					if (tmp) 
					{
						grid.add(tmp);
					}
				}
				if(tmp) tmp.view.visible = false;
			}
			
			//显示出现动画
			for (var j:int = 0; j < grid.data.length; j++) 
			{
				var item:GridItem = grid.data[j];
				item.view.visible = true;
				item.view.mouseChildrens = 
				item.view.mouseEnabled = false;
				
				if (tweenTime) 
				{
					TweenLite.from(item.view, tweenTime, { delay:j*tweenDelay, y:"+20",autoAlpha:0,onComplete:outComplete,onCompleteParams:[item,j==grid.data.length-1],ease:Cubic.easeOut } );
				}else {
					outComplete(item, j == grid.data.length - 1);
				}
				
			}
			
			if (currentPage>1) 
			{
				setBtnVisible(prevBtn, true);
			}else {
				setBtnVisible(prevBtn, false);
			}
			
			if (currentPage+1<=Math.ceil(data.length/pageLength)) 
			{
				setBtnVisible(nextBtn, true);
			}else {
				setBtnVisible(nextBtn, false);
			}
			
		}
		
		private function outComplete(value:GridItem,finish:Boolean=false):void 
		{
			value.view.mouseChildrens = 
			value.view.mouseEnabled = true;
			if (finish) 
			{
				dispatchInitPageComplete();
			}
		}
		
		private function dispatchInitPageComplete():void {
			dispatchEvent(new Event(Event.COMPLETE));
		}
		
		protected function createBlankItem():GridItem
		{
			return null;
		}
		
		/**
		 * 获取指定index的项
		 * @param	index
		 * @return
		 */
		public function getItemByIndex(index:uint):GridItem {
			var tmp:GridItem;
			for (var i:int = 0; i < grid.data.length; i++) 
			{
				tmp = grid.data[i] as GridItem;
				if (tmp.index==index) 
				{
					return tmp;
				}
			}
			return null;
		}
		
		/**
		 * 增加item
		 * @param	value
		 * @param	reDraw
		 */
		public function addItem(value:Object, reDraw:Boolean = true ):void { 
			if (numKey && cidKey) 
			{
				for (var i:int = 0; i < data.length; i++) 
				{
					if (data[i][cidKey]==value[cidKey]) 
					{
						
						if (addNumFunc) {
							data[i][addNumFunc](value);
						}else{
							data[i][numKey] += value[numKey];
						}
					}
						
					if(reDraw) initPage();
					return;
				}
			}	
			//如果没有相同的
			data.push(value);
			if(reDraw) initPage();
		}
		
		public function jumpToItem(item:Object):void {
			
		}
		
		
		/**
		 * 删除item
		 * @param	cid
		 * @param	num
		 * @param	reDraw
		 */
		public function removeItem(cid:uint,num:uint, reDraw:Boolean = true):void { 
			
			for (var i:int = 0; i < data.length; i++) 
			{
				if (data[i][cidKey]==cid) 
				{
					if (numKey && cidKey) 
					{
						if (delNumFunc) 
						{
							data[i][delNumFunc](num);
						}else {
							data[i][numKey] -= num;
						}
						
						if (data[i][numKey]<=0) 
						{
							data.splice(i, 1);
						}
						
						if(reDraw) initPage();
						return;
					}
					
					data.splice(i, 1);
					if(reDraw) initPage();
				}	
			}
		}
		
		
		private function setBtnVisible(target:DisplayObject,value:Boolean):void {
			if (target) 
			{
				if (hideButtonFlag) 
				{
					target.visible = value;
					BtnStateControl.setBtnState(target, true);
				}else {
					target.visible = true;
					BtnStateControl.setBtnState(target, value);
				}
				
			}
			
		}
		
		protected function createItem(value:Object):GridItem {
			//TODO item的具体实现,更换不同的GridItem子对象
			
			return null;
		}
		
		public function nextPage():void {
			if (currentPage+1<=Math.ceil(data.length/pageLength)) 
			{
				currentPage++;
				initPage();
			}
		}
		
		public function prevPage():void {
			if (currentPage-1>0) 
			{
				currentPage--;
				initPage();
			}
		}
		
		public function clear():void {
			if (grid) 
			{
				grid.clearAll();
			}
			
			totalData = null;
			data = null;
		}
		
		protected function clickFun(e:MouseEvent):void 
		{
			switch (e.target.name) 
			{
				case "prevBtn":
				prevPage();
				break;
				
				case "nextBtn":
				nextPage();
				break;
			}
		}
		
		public function get x():Number { return iview.x; }
		
		public function set x(value:Number):void 
		{
			iview.x = value;
		}
		
		public function get y():Number { return iview.y; }
		
		public function set y(value:Number):void 
		{
			iview.y = value;
		}
		
		public function getItemByKey(key:String, value:*):GridItem {
			var tmp:GridItem;
			for (var i:int = 0; i < grid.data.length; i++) 
			{
				tmp = grid.data[i] as GridItem;
				if (tmp) 
				{
					if (tmp["data"][key]) 
					{
						if (tmp["data"][key]==value) 
						{
							return tmp;
						}
					}
				}
			}
			return null;
		}
		
		public function set useBounds(value:Boolean):void {
			grid.list.useBounds = value;
			grid.list.layout();
		}
		
		public function get hideButtonFlag():Boolean 
		{
			return _hideButtonFlag;
		}
		
		public function set hideButtonFlag(value:Boolean):void 
		{
			_hideButtonFlag = value;
			if (prevBtn) 
			{
				setBtnVisible(prevBtn, false);
			}
			if (nextBtn) 
			{
				setBtnVisible(nextBtn, false);
			}
			
		}
	}

}