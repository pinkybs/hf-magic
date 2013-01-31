package  
{
	import flash.events.MouseEvent;
	/**
	 * ...
	 * @author jj
	 */
	public class EditerControl
	{
		private var main:Main;
		
		public function EditerControl(_main:Main) 
		{
			main = _main;
			
			//main.gridView.addEventListener(MouseEvent.CLICK, mapClickFun,true);
			main.addEventListener(MouseEvent.MOUSE_DOWN, beginDrag,true);
			main.addEventListener(MouseEvent.MOUSE_MOVE, mouseMoveFun,true);
			main.stage.addEventListener(MouseEvent.MOUSE_UP, upDrag);
		}
		
		private function mouseMoveFun(e:MouseEvent):void 
		{
			var tmp:TileView = e.target as TileView;
			if (!tmp) 
			{
				return;
			}else {
				main.editer.setStr(tmp.data.x+","+tmp.data.y);
			}
			
			if (!e.buttonDown) 
			{
				return;
			}
			
				if (main.editer.editTypeCombox.selectedItem.label=="可行") 
				{
					tmp.data.walkable = true;
					tmp.setNode(tmp.data);
					main.gridView.setNode(tmp.data);
				}else if (main.editer.editTypeCombox.selectedItem.label=="不可行") {
					tmp.data.walkable = false;
					tmp.setNode(tmp.data);
					main.gridView.setNode(tmp.data);
				}
		}
		
		private function upDrag(e:MouseEvent):void 
		{
			//if (main.editer.editTypeCombox.selectedItem.label=="移动地图") 
			//{
				main.mapSprite.stopDrag();
			//}
			
		}
		
		private function beginDrag(e:MouseEvent):void 
		{
			if (main.editer.editTypeCombox.selectedItem.label=="移动地图") 
			{
				main.mapSprite.startDrag();
			}else {
				var tmp:TileView = e.target as TileView;
				if (!tmp) 
				{
					return;
				}
				if (main.editer.editTypeCombox.selectedItem.label=="可行") 
				{
					tmp.data.walkable = true;
					tmp.setNode(tmp.data);
					main.gridView.setNode(tmp.data);
				}else if (main.editer.editTypeCombox.selectedItem.label=="不可行") {
					tmp.data.walkable = false;
					tmp.setNode(tmp.data);
					main.gridView.setNode(tmp.data);
				}
			}
		}
		
		private function mapClickFun(e:MouseEvent):void 
		{
			
			var tmp:TileView = e.target as TileView;
			if (!tmp) 
			{
				return;
			}
			if (main.editer.editTypeCombox.selectedItem.label=="可行") 
			{
				tmp.data.walkable = true;
				tmp.setNode(tmp.data);
				main.gridView.setNode(tmp.data);
			}else if (main.editer.editTypeCombox.selectedItem.label=="不可行") {
				tmp.data.walkable = false;
				tmp.setNode(tmp.data);
				main.gridView.setNode(tmp.data);
			}
		}
		
	}

}