package  
{
	import com.adobe.serialization.json.JSON;
	import fl.data.DataProvider;
	import flash.events.MouseEvent;
	import flash.filesystem.File;
	import flash.filesystem.FileMode;
	import flash.filesystem.FileStream;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoUtil;
	/**
	 * ...
	 * @author jj
	 */
	public class EditerView extends editerUi
	{
		private var main:Main;
		public var data:Object;
		public var mapSize:Number;
		
		public function EditerView(_main:Main) 
		{
			main = _main;
			addEventListener(MouseEvent.CLICK, clickFun, true);
			
			tileSizeInput.text = IsoUtil.TILE_SIZE.toString();
			tileLengthInput.text = "60";
			
			mapListCombox.labelField = "name";
			
		}
		
		public function setMapList(value:Object):void {
			data = value;
			mapListCombox.dataProvider = new DataProvider(value.bgClass);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case mapBtn:
					loadMap();
				break;
				
				case outBtn:
					saveData();
				break;
				
				case copyBtn:
					
				break;
			}
		}
		
		private function saveData():void
		{
			var curMapData:Object = main.gridView.outData();
			for (var i:int = 0; i < data.bgClass.length; i++) 
			{
				if (data.bgClass[i].name==curMapData.name) 
				{
					data.bgClass[i] = curMapData;
				}
			}
			
			var file:File = File.documentsDirectory;
			file = new File(File.applicationDirectory.resolvePath("data.txt").nativePath);
			var fileStream:FileStream = new FileStream();
			fileStream.open(file, FileMode.WRITE);
			fileStream.writeUTFBytes(JSON.encode(data));
			fileStream.close();
			//trace(JSON.encode(data));
		}
		
		private function loadMap():void
		{
			main.mapview.load(mapListCombox.selectedItem);
			
			main.gridView.setData(mapListCombox.selectedItem, uint(tileSizeInput.text),uint(tileLengthInput.text));
		}
		
		public function addStr(str:String):void
		{
			txt.appendText(str + "\n");
		}
		
		public function setStr(str:String):void
		{
			txt.text=str;
		}
		
	}

}