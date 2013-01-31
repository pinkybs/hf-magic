package happymagic.display.view.door 
{
	import flash.events.Event;
	import happyfish.display.view.UISprite;
	import happyfish.utils.DateTools;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.DecorVo;
	import happymagic.scene.world.grid.item.Desk;
	import happymagic.scene.world.grid.item.Door;
	/**
	 * ...
	 * @author Beck
	 */
	public class DoorTip extends UISprite
	{
		private var _door:Door;
		public var _iview:ui_doortips;
		public function DoorTip() 
		{
			this._view = new ui_doortips();
			this._iview = this._view as ui_doortips;
			
			DisplayManager.doorTip = this;
			this._view.addEventListener(Event.ADDED_TO_STAGE, addToStage);
		}
		
		private function addToStage(e:Event):void
		{
			this._view.removeEventListener(Event.ADDED_TO_STAGE, addToStage);
		}
		
		public function setDoor(value:Door):void {
			
			if (_door) 
			{
				_door.hideToolTips();
			}
			
			_door = value;
		}
		
		public function set data($data:DecorVo):void
		{
			this._iview.door_name.text = $data.name;
			_iview.starBar.gotoAndStop($data.level);
			
			this._iview.magic_value.text = '+' + $data.max_magic as String;
			this.countdown = $data.door_left_time;
		}
		
		public function set countdown($time:int):void
		{
			if ($time<=0) 
			{
				_iview.door_countdown.visible = false; 
				_iview.waitIcon.visible = true;
			}else {
				var time_count:String = DateTools.getLostTime($time * 1000,true,":",":",":");
				this._iview.door_countdown.text = time_count;
				_iview.door_countdown.visible = true;
				_iview.waitIcon.visible = false;
			}
			
		}
	}

}