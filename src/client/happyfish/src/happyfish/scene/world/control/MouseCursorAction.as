package happyfish.scene.world.control 
{
	import com.friendsofed.isometric.Point3D;
	import flash.events.MouseEvent;
	import happyfish.scene.world.WorldState;
	/**
	 * ...
	 * @author Beck
	 */
	public class MouseCursorAction
	{
		public var state:WorldState;
        public static var actionStack:Array = new Array();
        public static var defaultAction:MouseCursorAction = null;
		
		public function MouseCursorAction($state:WorldState, $stack_flg:Boolean = false) 
		{
            this.state = $state;
            if ($stack_flg)
            {
                actionStack.push(this);
            }
            else
            {
                this.clearActionStack();
            }
            if ($state.mouseAction != null)
            {
                $state.mouseAction.remove(!$stack_flg);
            }
            $state.mouseAction = this;
            return;
		}
		
        public function remove($stack_flg:Boolean = true) : void
        {
			if ($stack_flg) {
                if (actionStack.length > 0)
                {
                    actionStack.pop();
                    if (actionStack.length > 0)
                    {
                        this.state.mouseAction = actionStack[(actionStack.length - 1)];
                    }
                    else
                    {
                        this.gotoDefaultAction();
                    }
                }
                else
                {
                    this.gotoDefaultAction();
                }
			}
			
			return;
		}
		
		/**
		 * 设置默认action
		 */
		public function gotoDefaultAction():void
		{
			this.state.mouseAction = MouseCursorAction.defaultAction;
			return;
		}
		
		public function clearActionStack():void
		{
			actionStack = [];
			return;
		}
		
		/**
		 * 鼠标移动,待重载
		 * @param	event
		 */
        public function onMouseMove(event:MouseEvent) : void
        {
            return;
        }
		
		/**
		 * 返回grid坐标
		 * @return
		 */
		public function worldPosition():Point3D
		{
			return this.state.view.targetGrid();
		}
		
	}

}