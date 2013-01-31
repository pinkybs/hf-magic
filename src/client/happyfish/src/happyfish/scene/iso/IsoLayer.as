package happyfish.scene.iso 
{
	import com.friendsofed.isometric.IsoObject;
	import flash.display.DisplayObjectContainer;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import happyfish.cacher.bitmapMc.display.BitmapMovieMc;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.grid.Wall;
	import happymagic.scene.world.grid.item.WallDecor;
	
	/**
	 * 主要负责各个层的排序
	 * @author Beck
	 */
	public class IsoLayer extends Sprite
	{
		private var sortCount:uint;
		private var sortDelay:uint=10;
		private var needSort:Boolean;
		private var curOverItem:IsoItem;
		private var _sortAble:Boolean;
		protected var _objects:Array;
		public var parentView:IsoView;
		
		
		public function IsoLayer($iso_view:IsoView,sortAble:Boolean=false)
		{
			_sortAble = sortAble;
			_objects = new Array();
			this.parentView = $iso_view;
			
			if (_sortAble) 
			{
				addEventListener(Event.ENTER_FRAME, sortFun);
			}
			
		}
		
		public function getClickTargetPoint(e:MouseEvent):Boolean {
			var tmparr:Array = new Array();
			var list:Array = stage.getObjectsUnderPoint(new Point(stage.mouseX, stage.mouseY));
			var tmpitem:IsoObject;
			
			var i:int;
			for (i = 0; i < list.length; i++) 
			{
				if (list[i].parent) 
				{
					if (list[i].parent.parent) 
					{
						if (list[i].parent.parent is IsoObject) 
						{
							tmpitem = list[i].parent.parent as IsoObject;
							if (tmpitem.sprite.isoItem.mouseEnabled) 
							{
								tmparr.push(tmpitem);
							}
						}
					}
				}
			}
			var tmpe:MouseEvent;
			var getted:Boolean;
			for (i = 0; i < tmparr.length; i++) 
			{
				tmpitem = tmparr[i];
				if (tmpitem.sprite.isoItem.checkPoint()) 
				{
					tmpe = new MouseEvent(e.type);
					tmpitem.dispatchEvent(tmpe);
					getted = true;
				}else {
					tmpe = new MouseEvent(MouseEvent.MOUSE_OUT);
					tmpitem.dispatchEvent(tmpe);
				}
			}
			return getted;
		}
		
		public function inItem(item:IsoItem):void {
			curOverItem = item;
		}
		
		public function outItem():void {
			if (curOverItem) 
			{
				curOverItem.onMouseOut();
				curOverItem = null;
			}
			
		}
		
		public function nextItemMouseEvent(item:IsoSprite, e:MouseEvent):Boolean {
			var list:Array = stage.getObjectsUnderPoint(new Point(stage.mouseX, stage.mouseY));
			list = filterArrNotBitmapMc(list);
			for (var i:int = 0; i < list.length; i++) 
			{
				
				var tmp:IsoObject = checkParentIsIsoObject(list[i]);
				if (tmp) 
				{
					if (tmp===item.container) 
					{
						if (list[i-1]) 
						{
							var tmpe:MouseEvent = new MouseEvent(e.type);
							list[i-1].parent.parent.dispatchEvent(tmpe);
							return true;
						}
						
						return false;
					}
					
				}
			}
			return false;
		}
		
		private function filterArrNotBitmapMc(list:Array):Array 
		{
			var tmparr:Array = new Array();
			for (var i:int = 0; i < list.length; i++) 
			{
				if ((list[i] is BitmapMovieMc)) 
				{
					tmparr.push(list[i]);
				}
			}
			return tmparr;
		}
		
		private function checkParentIsIsoObject(target:*):IsoObject {
			if (target.parent) 
			{
				if (target.parent.parent) 
				{
					if (target.parent.parent is IsoObject) 
					{
						return target.parent.parent;
					}
				}
			}
			return null;
		}
		
		public function addIsoChild(child:IsoSprite):void
		{
			this.addChild(child.container);
			_objects.push(child);
			
			//设置精灵所在层
			child.parent = this;
			sort();
		}
		
		public function sort():void
		{
			needSort = true;
		}
		
		private function sortFun(e:Event):void {
			//return;
			sortCount++;
			if (sortCount>sortDelay) 
			{
				_objects.sortOn("name");
				
				//_objects.sortOn(["depth","sortPriority","moreDepth"], [Array.NUMERIC,Array.NUMERIC,Array.NUMERIC]);
				//_objects.sortOn(["depth","sortPriority"], [Array.NUMERIC,Array.NUMERIC]);
				//_objects.sortOn("sortPriority");
				_objects.sortOn("depth", Array.NUMERIC);
				//_objects.sortOn("height",Array.DESCENDING);
				
				
				//_objects.sort(isoDepthSort);
				
				for(var i:int = 0; i < _objects.length; i++)
				{
					if (getChildIndex(_objects[i].container)!=i) 
					{
						setChildIndex(_objects[i].container, i);
					}
					//setChildIndex(_objects[i].container, i);
				}
				sortCount = 0;
				needSort = false;
			}
		}
		
		private function isoDepthSort (childA:Object, childB:Object):int
		{
			var boundsA:IsoSprite = childA as IsoSprite;
			var boundsB:IsoSprite = childB as IsoSprite;
			
			//if (!boundsA.isoItem || !boundsB.isoItem) 
			//{
				//return 1;
			//}
			
			//if (boundsA.isoItem is WallDecor) 
			//{
				//trace(boundsA.left);
			//}
			//
			//if (boundsB.isoItem is WallDecor) 
			//{
				//trace(boundsB.left);
			//}
			
			if (boundsA.right <= boundsB.left)
				return -1;
			
			else if (boundsA.left >= boundsB.right)
				return 1;
			
			else if (boundsA.front <= boundsB.back)
				return -1;
				
			else if (boundsA.back >= boundsB.front)
				return 1;
				
			//else if (boundsA.grid_size_z <= boundsB.grid_size_z)
				//return -1;
				//
			//else if (boundsA.grid_size_z >= boundsB.grid_size_z)
				//return 1;
				
			//else if (boundsA.top <= boundsB.bottom)
				//return -1;
				//
			//else if (boundsA.bottom >= boundsB.top)
				//return 1;
			
			else if (boundsA.sortPriority <= boundsB.sortPriority)
				return -1;
			else if (boundsA.sortPriority >= boundsB.sortPriority)
				return 1;
				
				
			else
				return 0;
		}
		
		public function removeIsoChild($isoSprite:IsoSprite):void
		{
			//$isoSprite.container.alpha = 0;
			if ($isoSprite.container.parent) 
			{
				this.removeChild($isoSprite.container);
			}
			
			
			var index:int = _objects.indexOf($isoSprite);
			_objects.splice(index, 1);
			
			//this.sort();
			
			$isoSprite = null;
		}
		
		public function get objects():Array 
		{
			return _objects;
		}
		
	}

}