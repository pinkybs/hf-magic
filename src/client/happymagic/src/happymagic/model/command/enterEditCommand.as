package happymagic.model.command 
{
	import adobe.utils.CustomActions;
	import com.adobe.serialization.json.JSON;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.model.MagicUrlLoader;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.DecorVo;
	/**
	 * ...
	 * @author Beck
	 */
	public class enterEditCommand extends BaseDataCommand
	{
		
		public function enterEditCommand() 
		{
			
		}
		
		public function load():void {
			
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl('enteredit'), {buildingList:1,buildingBagList:1,FloorList:1,FloorBagList:1 } );
			
			loader.load(request);
		}
		
		
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			//设置vo
			DataManager.getInstance().decorBagList = new Array();
			var decor_array:Array = new Array();
			for ( var i:int= 0; i < objdata.decorList.length; i++ ) 
			{
				var decor_vo:DecorVo =  new DecorVo();
				decor_vo.setValue(objdata.decorList[i]);
				decor_array.push(decor_vo);
			}
			DataManager.getInstance().addDecor(decor_array);
			
			commandComplete();
		}
	}

}