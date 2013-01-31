package happymagic.manager 
{
	import flash.display.Sprite;
	import flash.geom.Point;
	import happymagic.display.view.desk.DeskTip;
	import happymagic.display.view.door.DoorTip;
	import happymagic.display.view.edit.BuildingItemList;
	import happymagic.display.view.MenuView;
	/**
	 * ...
	 * @author Beck
	 */
	public class DisplayManager
	{
		public static var uiDiyMenu:ui_diymenu;
		//ui容器
		public static var uiSprite:UiManager;
		//场景容器
		public static var sceneSprite:Sprite;
		//幕布容器
		public static var storyUiSprite:Sprite;
		//手型容器
		public static var mouseIconSprite:Sprite;
		
		public static var doorTip:DoorTip;
		public static var deskTip:DeskTip;
		public static var menuView:MenuView;
		//道具显示列表
		public static var buildingItemList:BuildingItemList;
		public function DisplayManager() 
		{
			
		}
		
		public static function getCurrentMousePosition():Point {
			return new Point(uiSprite.mouseX, uiSprite.mouseY);
		}
		
		public static function getPlayerPosition():Point {
			return DataManager.getInstance().worldState.world.player.view.container.parent.localToGlobal(new Point(DataManager.getInstance().worldState.world.player.view.container.screenX, DataManager.getInstance().worldState.world.player.view.container.screenY));
		}
		
	}

}