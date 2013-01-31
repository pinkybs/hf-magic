package happymagic.scene.world.control 
{
	import flash.geom.Point;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.WorldState;
	import happymagic.display.view.decorMirrro.MirroMenu;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.display.view.SysMenuView;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.scene.world.grid.item.Decor;
	import happymagic.scene.world.grid.item.Desk;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.item.WallDecor;
	/**
	 * ...
	 * @author Beck
	 */
	public class MouseEditAction extends MouseMagicAction
	{
		private var item:IsoItem;
		private var mirroMenu:MirroMenu;
		public function MouseEditAction($state:WorldState) 
		{
			super($state, true);
			mirroMenu = new MirroMenu();
			DataManager.getInstance().setVar("mirroMenu", mirroMenu);
		}
		
        override public function onDecorOver(event:GameMouseEvent) : void
        {
            Decor(event.item).showGlow();
			mirroMenu.setTarget(event.item);
			event.item.view.parent.addChild(mirroMenu);
            return;
        }
		
		override public function onDecorClick(event:GameMouseEvent):void
		{
			Decor(event.item).hideGlow();
			setItem(event.item);
		}
		
		override public function onDecorOut(event:GameMouseEvent) : void
        {
            Decor(event.item).hideGlow();
			mirroMenu.clearTarget();
            return;
        }
		
		override public function onDoorClick(event:GameMouseEvent):void
		{
			Door(event.item).hideGlow();
			setItem(event.item);
		}
		
		public function setItem(value:IsoItem):void {
			this.item = value;
		}
		
        
		
        override public function onWallDecorOver(event:GameMouseEvent) : void
        {
            WallDecor(event.item).showGlow();
            return;
        }
		
		override public function onWallDecorClick(event:GameMouseEvent):void
		{
			WallDecor(event.item).hideGlow();
			setItem(event.item);
		}
		
        override public function onWallDecorOut(event:GameMouseEvent) : void
        {
			WallDecor(event.item).hideGlow();
            return;
        }
		
		override public function onBackgroundClick(event:GameMouseEvent) : void
		{
			if (this.item != null) {
				new MouseCarryIsoAction(this.item, this.state);
				//显示物件的占格表现
				this.item.addIsoTile();
			}
			this.item = null;
		}
		
        override public function onDeskOver(event:GameMouseEvent) : void
        {
            Decor(event.item).showGlow();
			mirroMenu.setTarget(event.item);
			event.item.view.parent.addChild(mirroMenu);
            return;
        }
		
		override public function onDeskClick(event:GameMouseEvent):void
		{
			Decor(event.item).hideGlow();
			Desk(event.item).hideCantWalkIcon();
			//if (Desk(event.item).student) {
				//漂屏
				//var msgs:Array = [[PiaoMsgType.TYPE_BAD_STRING, "学生在课桌旁，不要打扰他"]];
				//
				//var point:Point = Desk(event.item).view.container.parent.localToGlobal(new Point(Desk(event.item).view.container.screenX, Desk(event.item).view.container.screenY));
				//var event_msg:PiaoMsgEvent = new PiaoMsgEvent(PiaoMsgEvent.SHOW_PIAO_MSG, msgs, 
							//point.x, point.y);
				//EventManager.getInstance().dispatchEvent(event_msg);
				//
				//return;
			//}
			setItem(event.item);
		}
		
        override public function onDeskOut(event:GameMouseEvent) : void
        {
            Decor(event.item).hideGlow();
			mirroMenu.clearTarget();
            return;
        }
		
	}

}