package happymagic.display.view.friends 
{
	import com.greensock.TweenLite;
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import happyfish.display.ui.FaceView;
	import happyfish.display.ui.GridItem;
	import happyfish.manager.EventManager;
	import happyfish.utils.display.ItemOverControl;
	import happymagic.events.SceneEvent;
	import happymagic.model.vo.UserVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class FriendsItemView extends GridItem
	{
		public var data:UserVo;
		private var iview:friendItemUi;
		private var _index:uint;
		private var face:FaceView;
		
		public function FriendsItemView(uiview:MovieClip) 
		{
			super(uiview);
			iview = uiview as friendItemUi;
			
			iview.mouseChildren = false;
			iview.buttonMode = true;
			iview.addEventListener(MouseEvent.CLICK, clickFun);
			ItemOverControl.getInstance().addOverItem(iview);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			var tmpe:SceneEvent = new SceneEvent(SceneEvent.CHANGE_SCENE);
			tmpe.uid = data.uid;
			EventManager.getInstance().dispatchEvent(tmpe);
		}
		
		override public function setData(value:Object):void 
		{
			data = value as UserVo;
			
			iview.nameTxt.text = data.name;
			iview.levelTxt.text = "LV " + data.level.toString();
			iview.indexTxt.text = (data.index + 1).toString() + "st";
			
			face = new FaceView();
			face.loadFace(data.face);
			face.x = 13;
			face.y = 35;
			iview.addChild(face);
			iview.addChild(iview.levelTxt);
		}
	}

}