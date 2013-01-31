package 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.utils.setTimeout;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.scene.astar.Grid;
	import happyfish.scene.astar.Node;
	import happyfish.scene.world.grid.BaseItem;
	import happyfish.scene.world.grid.SolidObject;
	import happyfish.scene.world.grid.Wall;
	import happymagic.actModule.overlapFix.model.ClearOverlapItemsCommand;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.scene.world.grid.item.Decor;
	import happymagic.scene.world.grid.item.Desk;
	import happymagic.scene.world.grid.item.WallDecor;

	/**
	 * ...
	 * @author slamjj
	 */
	public class OverlapFixMain extends ActModuleBase 
	{
		private var overlapItems:Object;

		public function OverlapFixMain():void 
		{
			
		}
		
		override public function close():void 
		{
			init_complete();
			super.close();
		}
		
		override public function init(actVo:ActVo, _type:uint = 1):void 
		{
			super.init(actVo, _type);
			
			if (DataManager.getInstance().isSelfScene  
				//&& DataManager.getInstance().getSceneClassById(DataManager.getInstance().curSceneUser.currentSceneId).type == SceneClassVo.HOME
				) 
			{
				overlapItems = new Object();
				initNodeItems();
			}else {
				close();
			}
			
		}
		
		private function initNodeItems():void 
		{
			var items:Array = DataManager.getInstance().worldState.world.items;
			var nodeItems:Object = new Object();
			//把所有物件放入node列表
			for (var i:int = 0; i < items.length; i++) 
			{
				if (items[i] is SolidObject) 
				{
					addItemToNodes(nodeItems,items[i]);
				}
			}
			
			//检查所有node
			requestDiyOverlapItems();
			
		}
		
		private function requestDiyOverlapItems():void 
		{
			var command:ClearOverlapItemsCommand = new ClearOverlapItemsCommand();
			command.addEventListener(Event.COMPLETE, requestDiyOverlapItems_complete);
			command.load(overlapItems);
		}
		
		private function requestDiyOverlapItems_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, requestDiyOverlapItems_complete);
			
			if ((e.target as ClearOverlapItemsCommand).needRefresh) 
			{
				EventManager.getInstance().showSysMsg("您的装饰错误已自动修正,场景将刷新,请稍候.",0,2000);
			
				setTimeout(refreshScene,2000);
			}else {
				close();
			}
			
			
		}
		
		private function refreshScene():void 
		{
			//通知刷新场景
			var tmpe:SceneEvent = new SceneEvent(SceneEvent.CHANGE_SCENE);
			tmpe.uid = DataManager.getInstance().currentUser.uid;
			EventManager.getInstance().dispatchEvent(tmpe);
			
			close();
		}
		
		private function addItemToNodes(nodeItems:Object,$item:BaseItem):void {
			var solid_object:SolidObject = SolidObject($item);
			
			var tmpx:int;
			var tmpz:int;
			var skip:Boolean;
			var $data:Object;
			
			for (var i:int = 0; i < solid_object.grid_size_x; i++) {
				
				for (var j:int = 0; j < solid_object.grid_size_z; j++) {
					tmpx = solid_object.x + i;
					tmpz = solid_object.z + j;
					
					
					if (!nodeItems[tmpx]) 
					{
						nodeItems[tmpx] = new Object();
					}
					if (!nodeItems[tmpx][tmpz] || (tmpx==tmpz && tmpx==0 && !(solid_object is Wall))) 
					{
						if (!(solid_object is Wall)) 
						{
							nodeItems[tmpx][tmpz] = solid_object;
						}
					}else {
						if ((solid_object is Decor) || (solid_object is WallDecor) || (solid_object is Desk) ) 
						{
							$data = new Object();
							$data.id = solid_object.data.id;
							$data.d_id = solid_object.data.d_id;
							$data.mirror = solid_object.data.mirror;
							$data.bag_type = 1;
							//$data.num = solid_object.data.num;
						}
						
						
						overlapItems[solid_object.data.id+"&"+solid_object.data.d_id.toString()] = $data;
					}
					
				}
			}
		}

	}

}