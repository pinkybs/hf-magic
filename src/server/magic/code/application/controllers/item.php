<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class Item_Controller extends Role_Controller {
	
	public function useitem()
	{
		$post = new Validation($_POST);
		$post->add_rules('role_item_id', 'required', 'numeric');
		$post->add_rules('num', 'required', 'numeric');
		
		if(!$post->validate())
		{
		   Network::buffer_error(Kohana::lang('base.valid_error'));
		}
		
		$role_item_id = $this->input->post('role_item_id');
		$num = 1;
		
		//根据id取出item信息
		$role_id = Role::getOwnRoleId();
		$role_item = RoleItem::instance($role_id);
		
		//检查是否有此种物品
		$role_item_data = $role_item->getItemDataById($role_item_id);
		
		if (empty($role_item_data)) {
			Network::buffer_error(Kohana::lang('base.no_this_item'));
		}
		
		//检查商品类型
		//$role_item_model = new Role_Item_Model($role_id);
		//--------检查使用次数---------------------------------------------------
		//先根据item表取出item数据
		$item_id = $role_item_data['item_id'];
		$item_data = Item::getItemData($item_id);
		if (!empty($item_data['role_property'])) {
			$role_prop = arr::_serial_to_array($item_data['role_property']);
			
			foreach ($role_prop as $key => $vl) {
				if ($key == 'hp' || $key == 'mina') {
					$role_extend_model = new Role_Extend_Model($role_id);
					$role_extend_data = $role_extend_model->getData();
					$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
					$cur_time = date("Ymd", $timestamp);
					if ($role_extend_data['use_item_day'] == $cur_time && $role_extend_data['use_item_times'] + $num > Kohana::config('base.item_over_nums_hp_mina_limit')) {
						Network::buffer_error(Kohana::lang('base.item_over_nums_hp_mina_limit'));
					}
				} elseif ($key == 'gmoney') {
					$num = $this->input->post('num');
				}
			}
		}
		//----------------------------------------------------------------------
		
		//检查数量是否足够
		if ($role_item_data['num'] < $num) {
			Network::buffer_error(Kohana::lang('base.not_enough_num'));
		}
		
		//=================检查buff是否重复使用===================================
		if (!empty($item_data['buff_effect'])) {
			$buff_effect = arr::_serial_to_array($item_data['buff_effect']);
			$buff_datas = $role_item->getBuff();
			foreach ($buff_effect as $key => $vl) {
				foreach ($buff_datas as $value) {
					if ($value['type'] == $key) {
						Network::buffer_error(Kohana::lang('base.same_buff_effect_item'));
					}
				}
			}
		}
		//======================================================================
		
		//消耗道具
		$result = $role_item->consume($role_item_id, $num, $role_item_data['num']);
		if (empty($result)) {
			Network::buffer_error(Kohana::lang('base.server_error'));
		}
		
		//使用
		$role = Role::create($role_id);
		//使用后的属性改变
		if (!empty($item_data['role_property'])) {
			if (!isset($role_prop)) {
				$role_prop = arr::_serial_to_array($item_data['role_property']);
			}
			foreach ($role_prop as $key => $vl) {
				if ($key == 'gmoney') {
					$vl *= $num;
				}
				$role->increment($key, $vl);
			}
		}
		
		//使用效果use_effect
		
		//使用buff效果buff_effcet
		if (!empty($item_data['buff_effect'])) {
			$buff_effect = arr::_serial_to_array($item_data['buff_effect']);
			$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
			//插入buff
			$role_buff_model = new Role_Buff_Model($role_id);
			
			//取出未到期技能效果
			$buff_datas = (array)$role_item->getBuff();

			foreach ($buff_effect as $key => $vl) {
				$insert_buff_data = array(
					'effect' => $item_data['buff_effect'],
					'role_id' => $role_id,
					'start_time' => $timestamp,
					'dur_time' => $item_data['buff_time'],
					'endtime' => $timestamp + $item_data['buff_time'],
					'type' => $key,
					'intro' => $item_data['eintro'],
				);
				
				array_push($buff_datas, $insert_buff_data);
				$role_buff_model->insertData($insert_buff_data);
			}
			Network::buffer('buff_data', $buff_datas);
		}
		
		//奖励物品
		if (!empty($item_data['prize_items'])) {
			$prize_items = arr::_serial_to_array($item_data['prize_items']);
			
			$prize_item_id = common::random_prize($prize_items);
			$role_item->putItemToPackage($prize_item_id);
			
			common::prize('item', array('key' => $prize_item_id, 'num' => 1));
			//拼接奖励字符串
			$prize_msg = common::retPrize();
			$prize_data = common::getVar('prize');
			Network::buffer('prize', array('prize_data' => $prize_data, 'prize_msg' => $prize_msg));
		}
		
		//记录使用次数
		if (isset($role_extend_data)) {
//			$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
//			$cur_time = date("Ymd", $timestamp);
			if ($role_extend_data['use_item_day'] != $cur_time) {
				$use_item_day = $cur_time;
				$use_item_times = $num;
			} else {
				$use_item_day = $cur_time;
				$use_item_times =  $role_extend_data['use_item_times'] + $num;
			}
			$role_extend_model->updateDatas(array(
				'use_item_times' => $use_item_times,
				'use_item_day' => $use_item_day,
			));
		}
		
		if ($item_data['rmoney'] != 0) {
			//插入使用日志
			log::useLog($item_data['id'], $item_data['name']);
		}

		Network::buffer('msg', Kohana::lang('base.use_ok'));
		Network::buffer('role_data', $role->getData());
		
		$bag_data = $role_item->getBagDataByType(RoleItem::ITEM_BAG);
		Network::buffer('item_bag', $bag_data);
	}
}