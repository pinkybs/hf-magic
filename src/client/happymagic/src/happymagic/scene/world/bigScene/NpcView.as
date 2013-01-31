package happymagic.scene.world.bigScene 
{
	import com.friendsofed.isometric.Point3D;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import happyfish.cacher.CacheSprite;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.world.WorldState;
	import happyfish.scene.world.WorldView;
	import happyfish.task.vo.TaskState;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.NpcVo;
	import happymagic.model.vo.TaskVo;
	import happymagic.scene.world.bigScene.events.BigSceneEvent;
	
	/**
	 * ...
	 * @author jj
	 */
	public class NpcView extends BigSceneNpcView
	{
		
		public var tasks:Array;
		private var paoIcon:MovieClip;
		public static const STATE_TASK:uint = 1;
		public static const STATE_CHAT:uint = 2;
		public static const STATE_SHOP:uint = 3;
		public var state:uint;
		public function NpcView($data:Object, $worldState:WorldState,__callBack:Function=null) 
		{
			
			//tasks = DataManager.getInstance().getTasksByNpcId($data.npcId);
			super($data, $worldState, __callBack);
			typeName = "Npc";
			
			view.container.buttonMode = true;
		}
		
		override protected function makeView():IsoSprite 
		{
			super.makeView();
			
			//鼠标事件
			view.container.addEventListener(MouseEvent.MOUSE_OVER, onMouseOver);
			view.container.addEventListener(MouseEvent.MOUSE_OUT, onMouseOut);
			view.container.addEventListener(MouseEvent.MOUSE_MOVE, onMouseOverMove);
			view.container.addEventListener(MouseEvent.CLICK, onClick);
			
			return view;
		}
		
		override protected function view_complete():void 
		{
			super.view_complete();
			
			if (data.faceX || data.faceY) 
			{
				faceTowardsSpace(new Point3D(data.faceX, 0, data.faceY));
			}
			
			
			//渲染任务状态
			initPaoIcon();
		}
		
		
		public function initPaoIcon():void
		{
			tasks = DataManager.getInstance().getTasksByNpcId(data.npcId);
			tasks.sortOn("state", Array.NUMERIC | Array.DESCENDING);
			if (paoIcon) 
			{
				paoIcon.parent.removeChild(paoIcon);
				paoIcon = null;
			}
			if (tasks.length>0) 
			{
				state = STATE_TASK;
				//有任务
				//判断是否有已完成任务
				if (hasFinishedTask()) 
				{
					paoIcon = new taskStateBg_1();
				}else {
					paoIcon = new taskStateBg_0();
				}
				paoIcon.y = -asset.height+paoIcon.getBounds(paoIcon).y+20;
				
			}else if (data.shop) {
				state = STATE_SHOP;
				paoIcon = new npcPao_shop();
				paoIcon.y = -asset.height;
			}else {
				//无任务
				//创建闲话泡
				state = STATE_CHAT;
				paoIcon = new npcPao_chat();
				paoIcon.y = -asset.height;
			}
			
			if (paoIcon) 
			{
				view.container.addChild(paoIcon);
			}
		}
		
		private function hasFinishedTask():Boolean {
			for (var i:int = 0; i < tasks.length; i++) 
			{
				if ((tasks[i] as TaskVo).state==TaskState.CAN_FINISH) 
				{
					return true;
				}
			}
			return false;
		}
		
	}

}