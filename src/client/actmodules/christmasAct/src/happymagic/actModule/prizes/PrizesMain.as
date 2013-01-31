package happymagic.actModule.prizes
{
	import flash.display.Sprite;
	import flash.events.Event;
	import happyfish.events.ModuleEvent;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happymagic.actModule.prizes.model.PrizesAwardVo;
	import happymagic.manager.DisplayManager;

	/**
	 * ...
	 * @author 
	 */
	public class PrizesMain extends ActModuleBase
	{

		public function PrizesMain():void 
		{
			
		}
		
		private function module_close(e:ModuleEvent):void 
		{
			if (e.moduleName!="prizesActView") 
			{
				return;
			}
			e.target.removeEventListener(ModuleEvent.MODULE_CLOSE, module_close);
			
			close();
		}

		override public function init(actVo:ActVo, _type:uint = 1):void 
		{
			super.init(actVo, _type);
			
			//侦听主窗口被关闭
			ModuleManager.getInstance().addEventListener(ModuleEvent.MODULE_CLOSE,module_close);
			
			if (actVo.moduleData.awards is Array) 
			{
				var awards:Vector.<PrizesAwardVo> = new Vector.<PrizesAwardVo>();
				var arr:Array = actVo.moduleData.awards;
				for (var i:int = 0; i < arr.length; i++) 
				{
					awards.push(new PrizesAwardVo().setData(arr[i]) as PrizesAwardVo);
				}
				actVo.moduleData.awards = awards;
			}
			
			
			var moduleVo:ModuleVo = new ModuleVo();
			moduleVo.setValue( { name:"prizesActView", className:"happymagic.actModule.prizes.PrizesActView",single:true
									});

		    var view:PrizesActView = DisplayManager.uiSprite.addModuleByVo(moduleVo) as PrizesActView;
		    view.setData(actVo);
			DisplayManager.uiSprite.setBg(view);
		}

	}

}