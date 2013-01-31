package happymagic.scene.world.control 
{
	import com.greensock.OverwriteManager;
	import happyfish.events.GameMouseEvent;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.control.MouseCursorAction;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.WorldState;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.DecorType;
	import happymagic.model.vo.DecorVo;
	
	/**
	 * ...
	 * @author Beck
	 */
	public class MouseMagicAction extends MouseCursorAction
	{
		protected var skipBackgroundClick:Boolean;
		
		public function MouseMagicAction($state:WorldState, $stack_flg:Boolean = false) 
		{
			super($state, $stack_flg);
		}
		
		public function onAwardItemOver(event:GameMouseEvent):void {
			return;
		}
		
		public function onAwardItemOut(event:GameMouseEvent):void {
			return;
		}
		
		public function onAwardItemClick(event:GameMouseEvent):void {
			return;
		}
		
		public function onRoomUpItemClick(event:GameMouseEvent):void {
			
		}
		
		public function onRoomUpItemOver(event:GameMouseEvent):void {
			return;
		}
		
		public function onRoomUpItemOut(event:GameMouseEvent):void {
			return;
		}
		
		public function onEnemyClick(event:GameMouseEvent):void {
			return;
		}
		
		public function onEnemyOver(event:GameMouseEvent):void {
			return;
		}
		
		public function onEnemyOut(event:GameMouseEvent):void {
			return;
		}

        public function onBackgroundClick(event:GameMouseEvent) : void
        {
			return;
        }

        public function onDecorOver(event:GameMouseEvent) : void
        {
            return;
        }

        public function onDecorClick(event:GameMouseEvent) : void
        {
            return;
        }

        public function onDecorOut(event:GameMouseEvent) : void
        {
            return;
        }
		
        public function onStudentOver(event:GameMouseEvent) : void
        {
            return;
        }

        public function onStudentClick(event:GameMouseEvent) : void
        {
            return;
        }

        public function onStudentOut(event:GameMouseEvent) : void
        {
            return;
        }
		
        public function onDeskOver(event:GameMouseEvent) : void
        {
            return;
        }

        public function onDeskClick(event:GameMouseEvent) : void
        {
            return;
        }

        public function onDeskOut(event:GameMouseEvent) : void
        {
            return;
        }
		
        public function onDoorClick(event:GameMouseEvent) : void
        {
            return;
        }
		
        public function onWallDecorOver(event:GameMouseEvent) : void
        {
            return;
        }

        public function onWallDecorClick(event:GameMouseEvent) : void
        {
            return;
        }

        public function onWallDecorOut(event:GameMouseEvent) : void
        {
            return;
        }
		
		public function onPlayerClick(event:GameMouseEvent):void {
			return ;
		}
		
		public function onPlayerOver(event:GameMouseEvent):void {
			return;
		}
		
		public function onPlayerOut(event:GameMouseEvent):void {
			return;
		}
		
		public function onNpcOver(event:GameMouseEvent):void {
			return;
		}
		public function onNpcOut(event:GameMouseEvent):void {
			return;
		}
		public function onNpcClick(event:GameMouseEvent):void {
			return;
		}
		
		public function onMassesClick(event:GameMouseEvent):void {
			return;
		}
		
		
		/**
		 * 记录DIY的内容,放入要提交保存的列表
		 * @param	$isoItem
		 */
		protected function recordChangeData($isoItem:IsoItem):void
		{
			var $data:Object = new Object();
			$data.x = $isoItem.x-IsoUtil.roomStart;
			$data.y = $isoItem.y;
			$data.z = $isoItem.z-IsoUtil.roomStart;
			$data.id = $isoItem.data.id;
			$data.d_id = $isoItem.data.d_id;
			$data.mirror = $isoItem.mirror;
			$data.bag_type = 0;
			
			var change_list:Object;
			if ($isoItem.type == DecorType.FLOOR) {
				change_list = DataManager.getInstance().floorChangeList;
			} else if ($isoItem.type == DecorType.WALL_PAPER) {
				change_list = DataManager.getInstance().wallChangeList;
			} else {
				
				if (DataManager.getInstance().decorChangeBagList[$data.id+"&"+$data.d_id.toString()]) 
				{
					if (($isoItem.data as DecorVo).x!=$data.x && ($isoItem.data as DecorVo).z!=$data.z) 
					{
						change_list = DataManager.getInstance().decorChangeList;
				
						//更改数据值
						change_list[$data.id+"&"+$data.d_id.toString()] = $data;
					}
					delete DataManager.getInstance().decorChangeBagList[$data.id+"&"+$data.d_id.toString()];
				}else {
					change_list = DataManager.getInstance().decorChangeList;
				
					//更改数据值
					change_list[$data.id + "&" + $data.d_id.toString()] = $data;
				}
				return;
			}
			change_list[$data.x + '_' + $data.z] = $data;
		}
		
		/**
		 * 记录有DIY道具变更要放入背包
		 * @param	$$data	要放入背包的装饰物
		 * @param	$is_add	是否增加道具到场景上
		 */
		protected function recordBagChangeData($$data:Object, $is_add:Boolean = true):void
		{
			//change数据对象
			var $data:Object = new Object();
			$data.id = $$data.id;
			$data.d_id = $$data.d_id;
			$data.mirror = $$data.mirror;
			$data.bag_type = 1;
			$data.num = $$data.num;
			
			var change_list:Object;
			if ($$data.type == DecorType.FLOOR) {
				change_list = DataManager.getInstance().floorChangeBagList;
			} else if ($$data.type == DecorType.WALL_PAPER) {
				change_list = DataManager.getInstance().wallChangeBagList;
			} else {
				//装饰物
				
				if ($data.num == 0) {
					return;
				}
				//防止拖上去然后拖下来的bug
				//如果不是本来就是背包里的
				//if ($$data.bag_type != 1) {
					//change_list = DataManager.getInstance().decorChangeBagList;
					//更改数据值
					//change_list[$data.id] = $data;
				//}else {
					//delete  DataManager.getInstance().decorChangeList[$data.id];
				//}
				
				//如果修改记录中有这个道具,就先从修改记录中移除
				if (DataManager.getInstance().decorChangeList[$data.id + "&" + $data.d_id.toString()]) {
					
					delete  DataManager.getInstance().decorChangeList[$data.id + "&" + $data.d_id.toString()];
				}
				change_list = DataManager.getInstance().decorChangeBagList;
				//更改数据值
				change_list[$data.id + "&" + $data.d_id.toString()] = $data;
				
				return;
			}
			
			//地板与墙纸的记录
			if (change_list[$data.d_id]) {
				if ($is_add) {
					change_list[$data.d_id].num++;
				} else {
					change_list[$data.d_id].num--;
				}
			} else {
				if ($is_add) {
					$data.num = 1;
				} else {
					$data.num = -1;
				}
				change_list[$data.d_id] = $data;
			}
		}

	}

}