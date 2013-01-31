package happymagic.display.view.friends 
{
	import flash.events.MouseEvent;
	import gs.TweenLite;
	import happyfish.display.view.ItemRender;
	import happyfish.manager.EventManager;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.events.SceneEvent;
	import happymagic.model.vo.UserVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class FriendsItemRender extends ItemRender
	{
		private var _index:uint;
		protected var _iview:friendItemUi;
		public function FriendsItemRender() 
		{
			this._view = new friendItemUi();
			
			this._iview = this._view as friendItemUi;
			_iview.mouseChildren = false;
			_iview.buttonMode = true;
			this._iview.addEventListener(MouseEvent.CLICK, clickFun);
			_iview.addEventListener(MouseEvent.MOUSE_OVER, overFun);
			_iview.addEventListener(MouseEvent.MOUSE_OUT, outFun);
		}
		
		private function outFun(e:MouseEvent):void 
		{
			TweenLite.to(_iview, .2, { y: 0 } );
		}
		
		private function overFun(e:MouseEvent):void 
		{
			TweenLite.to(_iview, .2, { y: -6 } );
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			var tmpe:SceneEvent = new SceneEvent(SceneEvent.CHANGE_SCENE);
			tmpe.uid = _data.uid;
			EventManager.getInstance().dispatchEvent(tmpe);
			
			
			//var msgs:Array = [[PiaoMsgType.TYPE_RED, 111],
				//[PiaoMsgType.TYPE_BLUE, 111],
				//[PiaoMsgType.TYPE_GREEN, 111],
				//[PiaoMsgType.TYPE_GEM, 111],
				//[PiaoMsgType.TYPE_EXP, 111],
				//[PiaoMsgType.TYPE_MAGIC, 111]
				//];
				//var px:Number;
				//var py:Number;
				//px = _view.stage.mouseX;
				//py = _view.stage.mouseY;
				//var event:PiaoMsgEvent = new PiaoMsgEvent(PiaoMsgEvent.SHOW_PIAO_MSG, msgs,px,py);
				//EventManager.getInstance().dispatchEvent(event);
		}
		
		public function set index(value:uint):void {
			_index = value;
			_iview.indexTxt.text = _index.toString() + "st";
		}
		
		public function get index():uint {
			return _index;
		}
		
		override public function set data(value:Object):void 
		{
			_data = value;
			
			//indexTxt.text = index.toString() + "st";
			_iview.nameTxt.text = data.name;
			_iview.levelTxt.text = "LV " + data.level.toString();
			_iview.typeIcon.gotoAndStop(data.magic_type);
		}
		
	}

}