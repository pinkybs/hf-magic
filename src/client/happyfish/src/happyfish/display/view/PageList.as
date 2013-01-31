package happyfish.display.view 
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.utils.getQualifiedClassName;
	import happyfish.display.view.ItemRender;
	import happymagic.display.view.magic.MagicLearnRender;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.DecorType;
	import happymagic.model.vo.DecorVo;
	/**
	 * 通用的滚动翻页列表类
	 * @author Beck
	 */
	public class PageList extends Sprite
	{
		private var itemData:Array;
		private var listClass:Class;
		private var itemListRender:ItemRender;
		private var class_name:String;
		//间隔距离,横向
		public var horizontalGap:int = 12;
		//默认起始位置
		public var startX:int = 80;
		public var _width:int = 80;
		
		//一页的项目数量
		public var pageItemNum:int = 6;
		public var items:Array=new Array();
		//当前数量
		private var curItemNum:int = 0;
		//当前页数
		private var curPage:int = 0;
		private var tabIndexName:int;
		
		public static const FALSEDATA:String = "falsedata"; 
		public function PageList( ) 
		{
			
		}
		
		public function create($item_data:Array, $list_class:Class, $tab_index:int = 1):void
		{
			this.class_name = getQualifiedClassName($list_class);
			this.listClass = $list_class;
			this.itemData = $item_data;
			this.tabIndexName = $tab_index;
			
			this.createPage();
		}
		
		private function createPage():void
		{
			var x:int = this.startX;
			this.curItemNum = this.curPage * this.pageItemNum;
			
			if (this.curItemNum < 0) {
				this.curPage++;
				return;
			}
			
			if (this.curItemNum >= this.itemData.length) {
				this.curPage--;
				return;
			}
			
			this.removeAllChild();

			while (curItemNum < this.itemData.length) {
				if (curItemNum >= this.pageItemNum * (this.curPage + 1)) {
					break;
				}
				
				if (this.itemData[curItemNum].class_name !=FALSEDATA)
				{
				   this.itemListRender = new this.listClass() as ItemRender;
				   this.itemListRender.data = this.itemData[curItemNum];
				}
				else
				{
			       this.itemListRender = new MagicLearnRender() as ItemRender;
				   this.itemListRender.data = this.itemData[curItemNum];
				}

	
				this.itemListRender.view.x = x;
				this.addChild(this.itemListRender.view);
				items.push(itemListRender);
				
				x = x + this.horizontalGap + this._width;
				curItemNum++;
			}
		}
		
		public function removeAllChild():void
		{
			items = new Array();
			removeAllChildFun(this);
		}
		
		public static function removeAllChildFun(con:Sprite):void
		{		
			for(var i:int=con.numChildren-1; i>=0; i--) {
				var tempObj:Object = con.getChildAt(0);
				con.removeChildAt(0);
				tempObj = null;
			}
		}
		
		public function nextPage():void
		{
			this.curPage++;
	
			this.createPage();
		}
		
		public function prevPage():void
		{	
			this.curPage--;
			
			this.createPage();
		}
		
		/**
		 * 根据新项重新渲染列表(新加项)
		 */
		public function addRenderList($item_data:DecorVo):void
		{
			$item_data.x = 0;
			$item_data.z = 0;
			/**
			 * 没有索引,效率低
			 */
			for (var i:int = 0; i < this.itemData.length; i++ ) {
				if (this.itemData[i].d_id == $item_data.d_id) {
					//TODO 数量叠加了,但道具ID没有合并记录,后面需要修改
					this.itemData[i].num++;
					this.reRenderList();
					return;
				}
			}
			
			$item_data.num = 1;
			DataManager.getInstance().addDecor([$item_data]);
			this.reRenderList();
		}
		
		public function reRenderList():void
		{
			this.removeAllChild();

			this.createPage();
		}
		
		/**
		 * 将物品拖到界面中,如果此物品<0,删除背包中的物项
		 */
		public function removeRenderList($item_data:DecorVo, $vl:int = -1):void
		{
			//var index:int = this.itemData.indexOf($item_data);
			
			//this.itemData[index].num += $vl;
			if ($item_data.num <= 0) {
				//删除此项
				var index:int = this.itemData.indexOf($item_data);
				//delete this.itemData[index];
				this.itemData.splice(index, 1);
				
				this.reRenderList();
			}
		}
		
	}

}