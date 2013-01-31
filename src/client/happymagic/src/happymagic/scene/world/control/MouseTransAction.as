package happymagic.scene.world.control 
{
	import com.friendsofed.isometric.IsoUtils;
	import com.friendsofed.isometric.Point3D;
	import flash.display.DisplayObjectContainer;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.geom.Point;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.mouse.MouseManager;
	import happyfish.manager.SoundEffectManager;
	import happyfish.scene.world.WorldState;
	import happyfish.scene.world.WorldView;
	import happyfish.utils.display.CameraSharkControl;
	import happyfish.utils.display.McShower;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.events.ActionStepEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.UseTransCommand;
	import happymagic.model.control.TakeResultVoControl;
	import happymagic.model.vo.ResultVo;
	import happymagic.model.vo.TransMagicVo;
	import happymagic.scene.world.award.AwardItemManager;
	import happymagic.scene.world.award.AwardType;
	import happymagic.scene.world.grid.person.Player;
	import happymagic.scene.world.grid.person.Student;
	/**
	 * ...
	 * @author jj
	 */
	public class MouseTransAction extends MouseMagicAction
	{
		private var player:Player;
		private var transVo:TransMagicVo;
		private var remove_flg:Boolean = false;
		private var playPoint:Point3D;
		
		public function MouseTransAction($state:WorldState, $transVo:TransMagicVo, $stack_flg:Boolean = false) 
		{
			super($state, $stack_flg);
			transVo = $transVo;
		}
		
		override public function onPlayerOver(event:GameMouseEvent):void 
		{
			event.item.showGlow();
		}
		
		override public function onPlayerOut(event:GameMouseEvent):void 
		{
			event.item.hideGlow();
		}
		
		/**
		 * 点击学生
		 * @param	event
		 */
        override public function onPlayerClick(event:GameMouseEvent) : void
        {
			player = event.item as Player;
			player.hideGlow();
			if (player.userVo.trans_time) 
			{
				//如果已变化
				EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("hasBeTurned"));
				return;
			}
			
			playPoint = state.view.targetGrid();
			startTransMv();
			//remove_flg = true;
        }
		
		private function startTransMv():void {
			//引导事件
			EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_USETRANS_COMPLETE));
			
			//禁止主角移动
			player.moveAble = false;
			var mvContainer:Sprite = state.view.isoView.getLayer(WorldView.LAYER_MV);
			var learnMv:McShower = new McShower(learnTransMv, mvContainer, null, null, learnMvEnd);
			//learnMv.changeRate(10, 71, 83);
			
			var p:Point = new Point(player.view.container.screenX, player.view.container.screenY);
			p = player.view.container.parent.localToGlobal(p);
			p = mvContainer.globalToLocal(p);
			learnMv.x = p.x;
			learnMv.y = p.y;
			
			CameraSharkControl.shark(DisplayManager.sceneSprite, 2, 500, null, 1500);
			
			//音效
			SoundEffectManager.getInstance().playSound(new sound_magichange());
		}
		
		private function learnMvEnd():void
		{
			trans();
		}
		
		private function trans():void
		{
			
			var uid:String;
			//判断是否是主角
			if (player===state.world.player) 
			{
				//主角
				uid=DataManager.getInstance().currentUser.uid;
			}else {
				//好友
				uid=DataManager.getInstance().curSceneUser.uid;
			}
			
			var command:UseTransCommand = new UseTransCommand();
			command.piaoMsg = false;
			command.addEventListener(Event.COMPLETE, trans_complete);
			command.useTrans(transVo.trans_mid,uid);
		}
		
		private function trans_complete(e:Event):void 
		{
			//人物可以行走了
			player.moveAble = true;
			
			e.target.removeEventListener(Event.COMPLETE, trans_complete);
			
			if (e.target.data.result.isSuccess) 
			{
				
				player.userVo.trans_className =DataManager.getInstance().getAvatarVo(transVo.trans_mid).className;
				player.userVo.trans_time =transVo.time==0 ? 1 : transVo.time;
				player.userVo.trans_mid =transVo.trans_mid;
				player.resetView(player.userVo.trans_className);
				player.initTransTimer();
				
				if (DataManager.getInstance().currentUser.uid==player.userVo.uid) 
				{
					DataManager.getInstance().currentUser.trans_className = player.userVo.trans_className;
					DataManager.getInstance().currentUser.trans_time = player.userVo.trans_time;
					DataManager.getInstance().currentUser.trans_mid = player.userVo.trans_mid;
				}
				
				if (DataManager.getInstance().curSceneUser.uid==player.userVo.uid) 
				{
					DataManager.getInstance().curSceneUser.trans_className = player.userVo.trans_className;
					DataManager.getInstance().curSceneUser.trans_time = player.userVo.trans_time;
					DataManager.getInstance().curSceneUser.trans_mid = player.userVo.trans_mid;
				}
				
				//掉落表现
				var awards:Array = new Array();
				//道具
				for (var i:int = 0; i < transVo.itemId.length; i++) 
				{
					awards.push({ type:AwardType.ITEM, num:transVo.itemId[i][1], point:playPoint,id:transVo.itemId[i][0] });
				}
				AwardItemManager.getInstance().addAwardsByResultVo(e.target.data.result, awards, playPoint);
				
				//复制一个去掉了经验\钱之类变化的resultVo,然后处理,因为这些都在前面的掉落表现中处理了
				var tmpresult:ResultVo = e.target.data.result.clone();
				tmpresult.coin = 0;
				tmpresult.exp = 0;
				TakeResultVoControl.getInstance().take(tmpresult,true);
			}
		}
		
		override public function onStudentOut(event:GameMouseEvent) : void
        {
			remove();
            return;
        }
		
		override public function onBackgroundClick(event:GameMouseEvent):void 
		{
			
			if(!player) EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING,LocaleWords.getInstance().getWord("transErrorTarget") );
			if(!player) EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_USETRANSCOMPLETECONTACTEVENT));
			remove_flg = true;
			remove();
			
		}
		
		override public function remove($stack_flg:Boolean = true):void
		{
			if (remove_flg) {
				super.remove();
			}
		}
		
		public function setMagic(paopao:Sprite):void
		{
			MouseManager.getInstance().setLiuChenIcon(paopao,transMouseComplete);
			
		}
		
		private function transMouseComplete():void
		{
			if (state.world.sceneLoading)
			{
				EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_USETRANSCOMPLETECONTACTEVENT));
			}
			remove_flg = true;
		}
		
	}

}