package happymagic.display.view.magic 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.cacher.CacheSprite;
	import happyfish.display.view.IconView;
	import happyfish.display.view.ItemRender;
	import happyfish.events.ModuleEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.mouse.MouseManager;
	import happyfish.utils.display.ScaleControl;
	import happymagic.display.control.MagicEnoughCheckCommand;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.events.ActionStepEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.PublicDomain;
	import happymagic.model.vo.MagicClassVo;
	import happymagic.model.vo.TransMagicVo;
	import happymagic.scene.world.control.MouseStudentAction;
	import happymagic.scene.world.control.MouseTransAction;
	
	/**
	 * ...
	 * @author Beck
	 */
	public class MagicItemRender extends ItemRender
	{
		private var _iview:ui_allmagic;
		protected var asset:CacheSprite;
		public var transmouseaction:MouseTransAction;
		public function MagicItemRender() 
		{
			this._view = new ui_allmagic;
			
			this._iview = this._view as ui_allmagic;
			
			this._iview.addEventListener(MouseEvent.CLICK, clickFun, false);
			
			ModuleManager.getInstance().addEventListener(ModuleEvent.MODULE_CLOSE, moduleClose);
		}
		
		override public function set data($data:Object):void
		{
			if (this.asset) {
				this._iview.icon.removeChild(this.asset);
			}
			
			this._data = $data;
			
			var icon:IconView = new IconView(45, 45, new Rectangle(12,10,50,50));
			_iview.addChild(icon);
			icon.setData(data.class_name);
			this._iview.num.text = $data.mp;
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			//如果mp不足,就飘屏
			if (!new MagicEnoughCheckCommand().check(_data.mp)) 
			{
				EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("magicUnenough"));
				return;
			}
             useTrans();
		}

		private function useTrans():void
		{
			//记录当前选中的魔法
			DataManager.getInstance().setVar("selectedTransClass",data);
			
			//创建设置流程鼠标手型
			var asset:IconView = new IconView(32, 32, new Rectangle(0,14,30,30));
			asset.setData(data.class_name);
			var paopao:ui_paopaoshouxing = new ui_paopaoshouxing();
			paopao.addChild(asset);
			
			
			
			
			//开始魔法学习鼠标事件侦听
			transmouseaction = new MouseTransAction(DataManager.getInstance().worldState, data as TransMagicVo);
			transmouseaction.setMagic(paopao);
			EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_CHANGEART));
		}
	
		public function bubbleComplete():void
		{
			ScaleControl.size(asset, 30, 30);
			asset = null;
		}
		
		private function moduleClose(e:ModuleEvent):void
		{
			switch(e.moduleName)
			{
			   case ModuleDict.MODULE_USEMAGIC_LIST:
		              if (transmouseaction)
					  {
						  transmouseaction.remove();
					  }
		       break;
			}

			
		}
		
	}

}