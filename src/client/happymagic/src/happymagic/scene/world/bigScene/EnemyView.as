package happymagic.scene.world.bigScene 
{
	import com.friendsofed.isometric.Point3D;
	import com.greensock.TweenLite;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import happyfish.display.view.PerBarView;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.WorldState;
	import happyfish.utils.CustomTools;
	import happyfish.utils.display.McShower;
	import happymagic.model.autoTake.TakeResultCommand;
	import happymagic.model.command.KillEnemyCommand;
	import happymagic.model.control.TakeResultVoControl;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.EnemyVo;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.ResultVo;
	import happymagic.scene.world.award.AwardItemManager;
	import happymagic.scene.world.award.AwardType;
	import happymagic.scene.world.bigScene.events.BigSceneEvent;
	import happymagic.scene.world.control.AvatarCommand;
	/**
	 * ...
	 * @author jj
	 */
	public class EnemyView extends BigSceneNpcView
	{
		public var enemyVo:EnemyVo;
		private var hpBar:PerBarView;
		private var randomActId:uint;
		private var smookMvMc:McShower;
		private var killRequestReady:Boolean;
		private var killEnemyMvReady:Boolean;
		private var killRequestResultEvent:Event;
		public var killed:Boolean;
		
		public function EnemyView($data:EnemyVo, $worldState:WorldState,__callBack:Function=null) 
		{
			enemyVo = $data;
			//_drawFrame = 0;
			super($data as Object, $worldState, __callBack);
			typeName = "Enemy";
			view.container.addEventListener(MouseEvent.ROLL_OVER, this.onMouseOver);
            view.container.addEventListener(MouseEvent.ROLL_OUT, this.onMouseOut);
            view.container.addEventListener(MouseEvent.MOUSE_MOVE, this.onMouseOverMove);
			_speed = 1;
			
		}
		
		override protected function makeView():IsoSprite 
		{
			super.makeView();
			
			//鼠标事件
			view.container.addEventListener(MouseEvent.CLICK, onClick);
			
			return view;
		}
		
		override protected function fiddleWaitFun():void 
		{
			if (CustomTools.customInt(0,2)==1) 
			{
				//停步表现随机动作
				randomAction();
			}else {
				fiddleId = setTimeout(fiddle, 5000);
			}
		}
		
		override protected function view_complete():void 
		{
			super.view_complete();
			asset.bitmap_movie_mc.drawFrame = 2;
			asset.buttonMode = true;
			//显示血槽
			initHp();
			
			//开始随机行走
			fiddle();
		}
		
		private function randomAction():void
		{
			asset.bitmap_movie_mc.gotoAndPlayLabels("waitplay");
			randomActId = setTimeout(fiddle, 3000);
		}
		
		public function showHitMv():void {
			//先停止行走,
			stopMove();
			
			var hitMvShower:McShower = new McShower(enemyHitMv, this.view.container, null, null, hitMvEnd);
			hitMvShower.x = Math.floor(Math.random() * 20)-10;
			hitMvShower.y = Math.floor(Math.random() * 30)-15;
			hitMvShower.mouseEnabled = false;
			
			TweenLite.to(asset, .2, { x: 6, y: -6, onComplete:onHit1Complete } );
			
			var tmp:int = Math.floor(Math.random() * 2) + 1;
			
			asset.bitmap_movie_mc.gotoAndPlayLabels("hit"+tmp.toString());
		}
		
		private function onHit1Complete():void
		{
			asset.bitmap_movie_mc.gotoAndPlayLabels("wait");
			TweenLite.to(asset, .1, { x: 0, y: 0,onComplete:onHit2Complete} );
		}
		
		private function onHit2Complete():void
		{
			
		}
		
		private function hitMvEnd():void
		{
			//mouseEnabled = true;
		}
		
		private function initHp():void
		{
			if (!hpBar) 
			{
				hpBar = new PerBarView(new enemyHpBarUi(), 58, enemyVo.hp);
				hpBar._view.visible = false;
				hpBar._view.x = -50 / 2;
				hpBar._view.y = -asset.height;
				
				view.container.addChild(hpBar._view);
			}
			
			hpBar.setData(enemyVo.curHp);
			
		}
		
		public function showHp():void {
			if(hpBar){
				hpBar._view.visible = true;
			}
		}
		
		public function hideHp():void {
			if(hpBar){
				hpBar._view.visible = false;
			}
		}
		
		public function changeHp(value:int):void {
			enemyVo.curHp += value;
			enemyVo.curHp = Math.max(0, enemyVo.curHp);
			enemyVo.curHp = Math.min(enemyVo.hp, enemyVo.curHp);
			
			if (enemyVo.curHp<=0) 
			{
				view.container.mouseChildren = mouseEnabled = false;
				//通知灭怪
				startFlashMv();
				//停止怪的行走
				killed = true;
				//调用杀怪接口
				killEnemy();
			}
			
			
			if(hpBar) hpBar.setData(enemyVo.curHp);
			
		}
		
		//播放闪电动画
		private function startFlashMv():void
		{	
			var killMvShower:McShower = new McShower(killEnemyMv, view.container, null, null, flashMvEnd);
			killMvShower.mouseEnabled = false;
		}
		
		private function flashMvEnd():void
		{
			startSmook();
		}
		
		//表现循环的烟雾动画
		private function startSmook():void
		{
			smookMvMc = new McShower(smookMv, view.container,null,null,smookMvEnd,null,false);
			smookMvMc.mouseEnabled = false;
		}
		
		public function smookMvEnd():void {
			killEnemyMvReady = true;
			killEnemyCheck();
		}
		
		/**
		 * 检查是否动画与请求都完成,如果完成,就表现结果
		 * @param	e
		 */
		private function killEnemyCheck():void
		{
			if (killEnemyMvReady && killRequestReady) 
			{
				smookMvMc.removeMe();
				smookMvMc = null;
				
				asset.visible = false;
				
				//表现烟雾散开动画
				smookMvMc = new McShower(smookMv, view.container,null,null,showAward);
				smookMvMc.mouseEnabled = false;
				
				//清除请求与动画完成状态
				killRequestReady=
				killEnemyMvReady = false;
			}
		}
		
		private function showAward():void
		{
			var e:Event = killRequestResultEvent;
			smookMvMc = null;
			if (e.target.data.result.isSuccess) 
			{
				//如果成功
				
				var result:ResultVo = e.target.data.result;
				
				//显示掉落物品
				var tmpP:Point3D = IsoUtil.isoToGrid(IsoUtil.screenToIso(new Point(view.container.screenX,view.container.screenY)));
			
				var awards:Array = new Array();
				if (e.target.data.addItem) 
				{
					var tmpitem:ItemVo;
					for (var i:int = 0; i < e.target.data.addItem.length; i++) 
					{
						tmpitem = e.target.data.addItem[i] as ItemVo;
						awards.push({ type:AwardType.ITEM, num:tmpitem.num, point:tmpP,id:tmpitem.i_id });
					}
				}
				
				AwardItemManager.getInstance().addAwardsByResultVo(result, awards, tmpP);
				
				//其他返回结果飘屏显示
				var point:Point = view.container.parent.localToGlobal(
					new Point(view.container.screenX, view.container.screenY));
				result.coin = result.gem = result.exp = 0;
				TakeResultVoControl.getInstance().take(result,true, point);
				
				removeMe();
			}
			killRequestResultEvent = null;
			//还原鼠标事件
			view.container.mouseChildren = view.container.mouseEnabled = true;
		}
		
		private function killEnemy():void {
			var command:KillEnemyCommand = new KillEnemyCommand();
			command.addEventListener(Event.COMPLETE, killEnemy_complete);
			command.kill(enemyVo.enemyId);
		}
		
		private function killEnemy_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, killEnemy_complete);
			
			if ((e.target.data.result as ResultVo).isSuccess) 
			{
				killRequestReady = true;
				killRequestResultEvent=e;
				killEnemyCheck();
			}else {
				
				if(smookMvMc){
					smookMvMc.removeMe();
					smookMvMc = null;
				}
				
				//表现烟雾散开动画
				//smookMvMc = new McShower(smookMv, view.container,null,null,null,null,true);
				//smookMvMc.mouseEnabled = false;
				
				//清除请求与动画完成状态
				killRequestReady=
				killEnemyMvReady = false;
				
				asset.buttonMode = true;
				//显示血槽
				initHp();
				
				//开始随机行走
				fiddle();
			}
			
			
		}
		
		override public function clear():void 
		{
			if(randomActId) clearTimeout(randomActId);
			super.clear();
		}
		
		private function removeMe():void
		{
			remove();
		}
		
	}

}