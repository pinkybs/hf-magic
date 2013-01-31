<?php defined('SYSPATH') or die('No direct script access.');
class Sns_Controller extends Controller {
	public $session;
	
	public function __construct()
	{
		//-------------------常量,全局变量---------------------------------------
		//初始化时间
		$timestamp = &PEAR::getStaticProperty('_APP', 'timestamp');
		$timestamp = time();
		
		parent::__construct();
		//Event::add_before('system.shutdown', array('Kohana', 'shutdown'), array('Role', 'updates'));
		Event::add_before('system.shutdown', array('Kohana', 'shutdown'), array('DbCache', 'updates'));
		Event::add_before('system.shutdown', array('Kohana', 'shutdown'), array('DbCache', 'sync'));
		@header('P3P: CP="CAO PSA OUR"');
		
//		/$prop = new Profiler;
	}
	
	public function test()
	{
		/**
		$b = new Building_Model('48');
		$b->updateBuildingById(array('bag_type' => 0), 81);
		
		$decor_model = new Decor_Model($role_id);
		//$decor_model->update(1, array('name' => '6哈哈'));
		//$decor_model->update(1, array('name' => '7哈哈'));
		$decor_list = $decor_model->getDecorByRoleId();
		
		//$decor_model->update(2, array('name' => '4哈哈'));
		foreach ($decor_list as $key => $vl) {
			//var_dump($key, $vl);
		}
		
		$decor_list = $decor_model->getDecorByRoleId();
		foreach ($decor_list as $key => $vl) {
			//var_dump($key, $vl);
		}
		*/
		/**
		$role = Role::create(515);
		var_dump($role->get('exp'));
		var_dump($role->get('red'));
		$role->increment('exp', 1);
		$role->increment('red', 1);
		
		var_dump($role->get('exp'));
		var_dump($role->get('red'));
		*/
		/*
		$student_model = Student_Task_Model::instance(515);
		
		$students = $student_model->getDataById(5407);
		var_dump($students['state']);
		$data = array('state' => $students['state']+1);
		
		$student_model->update(5407, $data);
		
		echo 'update'."\n";
		$students = $student_model->getDataByRoleId();
		foreach ($students as $key => $vl) {
			var_dump($vl[0]['id']);
			var_dump($vl[0]['state']);
		}
		$students = $student_model->getDataById(5407);
		var_dump($students['state']);
		**/
//			$basic_model = new Basic_Model();
//			
//			//新手引导表
//			$newbies = $basic_model->getNewbieList();
//			
//			foreach ($newbies as $vl) {
//				var_dump($vl);
//			}
//			
//				$building_data = array(
//					'role_id' => 1,
//					'building_id' => 1,
//					'building_type' => 1,
//					'effect_mp' => 1,
//					'x' => 0,
//					'y' => 0,
//					'z' => 0,
//					'mirror' => 0,
//					'bag_type' => 1,
//				);
//				$building_model = Building_Model::instance(1);
//				$insert_id = $building_model->insert($building_data);
	}
	
	public function index()
	{
//		//获得平台uid
//		$platform_uid = $this->input->get('uid');
//		$platform_name = $this->input->get('name');
//		$platform_name = mb_convert_encoding($platform_name, "utf-8", "gbk");//iconv("gb2312","UTF-8",$platform_name); 
//		if (empty($platform_uid)) {
//			$platform_uid = 1030;
//		}
//		
//		if (empty($platform_name)) {
//			$platform_name = "公共账号";
//		}

		$base_conf = Kohana::config('base');
		$api_key = $base_conf['api_key'];
		$secret = $base_conf['secret_key'];
		$app_name = $base_conf['app_name'];
		
		$this->session = Session::instance();
		//XXX以后可以替换为post
		$platform_uid = $this->input->get('xn_sig_user');
		$xn_sig_added = $this->input->get('xn_sig_added');
		
		$sns_session = $this->session->get('sns');
		if (!empty($sns_session) && empty($platform_uid)) {
			$platform_uid = $sns_session['xn_sig_user'];
		}
		
		//根据平台uid获取游戏role_id
		$basic = Basic::instance($platform_uid);
		$role_id = $basic->getRoleId();

		$sns = Sns::factory('XiaoNei', $api_key, $secret);
		$in_sns = $sns->usersIsAppUser();
		if ($in_sns) {
			$session_conf = Kohana::config('session');
			$session_key = $session_conf['session_key'];
			
			$this->session->set('session_key', $session_key);
			$this->session->set('uid', $platform_uid);
		} else {
			echo "<script>top.location.href='http://app.renren.com/apps/tos.do?v=1.0&api_key=$api_key&next=http://apps.renren.com/$app_name/'</script>";
			die();
		}
		
		if (empty($platform_uid)) {
			Network::buffer_error('valid_error');
		}
		
		if (empty($_GET['invite_code'])) {
			$this->session->set('sns', $this->input->get());
		}

		$first_in = true;
		if (empty($role_id)) {
			//=================邀请码操作==============================================
			$code = $this->input->get('invite_code');
			if (empty($code)) {
				//显示邀请码
				$this->template = new View('magic/invitecode');
				$this->template->render(TRUE);
				die();
			} else {
				$invite_code_config = Kohana::config('invitecode');
				if (isset($invite_code_config[$code])) {
					//判断是否使用过
					$cache = Cache::instance('invite_code');
					$invite_code_flg = $cache->get('invite_code_save');
					
					if (empty($invite_code_flg) || !isset($invite_code_flg[$code])) {
						//写入缓存
						if (empty($invite_code_flg)) {
							$invite_code_flg = array($code => 1);
						} else {
							$invite_code_flg = array_merge($invite_code_flg, array($code => 1));
						}
						$cache->set('invite_code_save', $invite_code_flg);
					} else {
						die('邀请码无效');
					}
				} else {
					die('邀请码无效');
				}
			}
			//===========================================================================

			//第一次进入游戏,初始化
			$user = $sns->getUserInfo();
			$platform_name = $user['name'];
			
			//更新basic表,并取出此id
			$basic_model = new Basic_Model();
			$role_id = $basic_model->updateSeqId();
			
			//插入map表
			$map_model = new Uid_Map_Model($platform_uid);
			$map_model->insertData($platform_uid, $role_id);
			
			//初始化数据
			$basic_model = new Basic_Model();
			$init_data = $basic_model->getInitDataById();
			
			//取出初级的数据
			//$levels = $basic_model->getLevelList();
			$level_data = usual::getLevelConfig(1);
			
			//新手引导表
			$newbies = $basic_model->getNewbieList();
			$newbie_data = array();

			foreach ($newbies as $vl) {
				$v = $vl[0];
				if (!isset($newbie_data[0])) {
					$newbie_data[0] = array(array($v['id'], 1));
				} else {
					array_push($newbie_data[0], array($v['id'], 1));
				}
			}
			//1是学生学习时间缩短,有中断,0是正常
			$newbie_data[1] = array(1);
			
			$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
			//插入role表
			$role_data = array(
				'id' => $role_id,
				'role_id' => $role_id,
				'uid' => $platform_uid,
				'name' => $platform_name,
				'tiny_url' => $user['tinyurl'],
				'exp' => 0,
				'max_exp' => $level_data['exp'],
				'max_mp_add' => 0,
				'mp' => $level_data['max_mp'],
				'max_mp' => $level_data['max_mp'],
				'mp_set_time' => $timestamp,
				'mp_recovery_rate_plus' => 0,
				'coin' => $init_data['coin'],
				'gmoney' => $init_data['gmoney'],
				'major_magic' => 1,
				'deal_level' => 1,
				'level' => 1,
				'house_level' => 1,
				'newbie' => json_encode($newbie_data),
				'trans_type' => 0,
				'trans_start_time' => 0,
				'fiddle_students' => 0,
				'tile_x_length' => $level_data['tile_size'],
				'tile_z_length' => $level_data['tile_size'],
				'avatar_id' => 0,
				'cur_scene_id' => 1000001,
				'created_time' => date("Y-m-d H:i:s", $timestamp),
			);
			$role_model = Role_Model::instance($role_id);
			$role_model->insert($role_data);
			
			$role = Role::create($role_id);
			
			//插入floor表
			$floor_format = array();
			$basic_building_data = $basic_model->getBuildingDataById($init_data['floor']);
			for ($i = 0; $i < $level_data['tile_size'] - 1; $i++) {
				for ($j = 0; $j < $level_data['tile_size'] - 1; $j++) {
					$floor_format[$i][$j] = $init_data['floor'];
				}
				$role->increment('max_mp_add', $basic_building_data['effect_mp']);
				$role->increment('mp', $basic_building_data['effect_mp']);
			}
			$floor_data = array(
				'id' => $role_id,
				'role_id' => $role_id,
				'data' => json_encode($floor_format),
			);
			$floor_model = Floor_Model::instance($role_id);
			$floor_model->insert($floor_data);
			
			//插入墙壁表
			$wall_format = array();
			$basic_building_data = $basic_model->getBuildingDataById($init_data['wall']);
			for ($i = 0; $i <= 1; $i++) {
				for ($j = 0; $j < $level_data['tile_size'] - 1; $j++) {
					$wall_format[$i][$j] = $init_data['wall'];
				}
				$role->increment('max_mp_add', $basic_building_data['effect_mp']);
				$role->increment('mp', $basic_building_data['effect_mp']);
			}
			$wall_data = array(
				'id' => $role_id,
				'role_id' => $role_id,
				'data' => json_encode($wall_format),
			);
			$wall_model = Wall_Model::instance($role_id);
			$wall_model->insert($wall_data);
			
			//初始化building表(包含door_task,暂时初始化,一个桌子,一个门
			$building_list_init = arr::_multi_serial_to_array($init_data['decors']);
			foreach ($building_list_init as $vl) {
				//取出基础信息,进行玩家mp加成
				$basic_building_data = $basic_model->getBuildingDataById($vl['id']);
				$role->increment('max_mp_add', $basic_building_data['effect_mp']);
				$role->increment('mp', $basic_building_data['effect_mp']);
				
				$building_data = array(
					'role_id' => $role_id,
					'building_id' => $vl['id'],
					'building_type' => $basic_building_data['type'],
					'effect_mp' => $basic_building_data['effect_mp'],
					'x' => $vl['x'],
					'y' => 0,
					'z' => $vl['z'],
					'mirror' => 0,
					'bag_type' => 0,
				);
				$building_model = Building_Model::instance($role_id);
				$insert_id = $building_model->insert($building_data);
				
				if ($vl['type'] == DecorType::DOOR) {
					$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
					$door_data = array(
						'id' => $insert_id,
						'role_id' => $role_id,
						'left_students_num' => 1,
						'start_time' => $timestamp,
						'end_time' => $timestamp,
					);
					//插入door_task
					$door_task_model = Door_Task_Model::instance($role_id);
					$door_task_model->insert($door_data);
				}
			}
			
			//初始化背包
			$building_bag_list = json_decode($init_data['bag_decors']);
			foreach ($building_bag_list as $building_id) {
				//取出数据
				$basic_building_data = $basic_model->getBuildingDataById($building_id);
				$building_data = array(
					'role_id' => $role_id,
					'building_id' => $building_id,
					'building_type' => $basic_building_data['type'],
					'effect_mp' => $basic_building_data['effect_mp'],
					'x' => 0,
					'y' => 0,
					'z' => 0,
					'mirror' => 0,
					'bag_type' => 1,
				);
				$building_model = Building_Model::instance($role_id);
				$insert_id = $building_model->insert($building_data);
			}
			
			$floor_bag_list = json_decode($init_data['bag_floor']);
			foreach ($floor_bag_list as $vl) {
				$floor_bag_data = array(
					'role_id' => $role_id,
					'floor_id' => $vl[0],
					'quantity' => $vl[1],
				);
				$floor_in_bag_model = Floor_Inbag_Model::instance($role_id);
			 	$floor_in_bag_model->insert($floor_bag_data);
			}
			
			$wall_bag_list = json_decode($init_data['bag_wall']);
			foreach ($wall_bag_list as $vl) {
				$wall_bag_data = array(
					'role_id' => $role_id,
					'wall_id' => $vl[0],
					'quantity' => $vl[1],
				);
				$wall_in_bag_model = Wall_Inbag_Model::instance($role_id);
			 	$wall_in_bag_model->insert($wall_bag_data);
			}
			
			//插入item
			$items_list = json_decode($init_data['items']);
			foreach ($items_list as $vl) {
				$item_data = array(
					'role_id' => $role_id,
					'item_id' => $vl[0],
					'count' => $vl[1],
					'today_use_count'=> 0,	
					'last_use_time' => 0,
				);
				
				$item_model = Item_Model::instance($role_id);
				$item_model->insert($item_data);
			}
			
			//插入初始场景
			$init_scene = $basic_model->getSceneListByState();
			$scene_array = array();
			foreach ($init_scene as $vl) {
				array_push($scene_array, $vl[0]['id']);
			}
			
			$scene_model = Scene_Model::instance($role_id);
			$scene_model->insert(array('id' => $role_id, 'role_id' => $role_id, 'scenes' => json_encode($scene_array)));
			
			//初始化用户任务
			//新手任务
			$newbie_event = $basic_model->getEventNewbieList();
			$newbie_event_array = array();
			foreach ($newbie_event as $vl) {
				array_push($newbie_event_array, $vl[0]['id']);
				
				//插入条件
			    $event_condition = arr::_multi_serial_to_array($vl[0]['condition']);
		    	$event_con_model = Event_Condition_Model::instance($role_id);
		    	foreach ($event_condition as $v) {
		    		$data = array(
		    			'type' => $v['type'],
		    			'num' => 0,
		    			'target_num' => $v['num'],
		    			'type_id' => $v['id'],
		    			'event_id' => $vl[0]['id'],
		    			'role_id' => $role_id,
		    		);
    		
    				$event_con_model->insert($data);
    			}
			}
			//日常任务
			$daily_event = $basic_model->getEventDailyList();
			$daily_event_array = array();
			foreach ($daily_event as $vl) {
				if ($vl[0]['level'] > 1) {
					break;
				}
				array_push($daily_event_array, $vl[0]['id']);
				
				//插入条件
			    $event_condition = arr::_multi_serial_to_array($vl[0]['condition']);
		    	$event_con_model = Event_Condition_Model::instance($role_id);
		    	foreach ($event_condition as $v) {
		    		$data = array(
		    			'type' => $v['type'],
		    			'num' => 0,
		    			'target_num' => $v['num'],
		    			'type_id' => $v['id'],
		    			'event_id' => $vl[0]['id'],
		    			'role_id' => $role_id,
		    		);
    		
    				$event_con_model->insert($data);
    			}
			}
			
			//插入任务
			$event_model = Event_Model::instance($role_id);
			$event_model->insert(
				//role_id 	newbie 	trunk 	branch 	daily 	branch_exp 	daily_level 
				array(
					'id' => $role_id,
					'role_id' => $role_id,
					'newbie' => json_encode($newbie_event_array),
					'trunk' => '[]',
					'branch' => '[]',
					'daily' => json_encode($daily_event_array),
					'branch_exp' => 0,
					'daily_level' => 1,
				)
			);
			
			//根据等级取出学生数据
			$student_level_conf = $basic_model->getStudentListByLevel(1);
			
			$student_model = Student_Model::instance($role_id);
			foreach ($student_level_conf as $vl) {
				//插入
				$student_model->insert(
					array(
						'role_id' => $role_id,
						'student_id' => $vl[0]['id'],
						'exp' => 0,
						'level' => 1,
						'award_flg' => 0,
						'student_state' => 0,
					)
				);
			}
		} else {
			$role = Role::create($role_id);
			$avatar_id = $role->get('avatar_id');
			if ($avatar_id != 0) {
				$first_in = false;
			}
		}
		
		//role_id存入session
		$this->session->set('role_id', $role_id);
		$role = Role::create($role_id);
		
		//显示页面
		$this->template = new View('magic/index');
		$this->template->role_id = $role_id;
		$this->template->name = $role->get('name');
		if ($first_in) {
			$this->template->api = url::base(true).'init/createrole';
			$this->template->module = media::flash_url().'createPlayer.swf';
			$this->template->piantou = media::flash_url().'piantou.swf';
		} else {
			$this->template->api = '';
			$this->template->module = '';
			$this->template->piantou = '';
		}
		$this->template->initUi = media::flash_url().'loading1.swf';
		$this->template->local_words  = media::server_static_url().'flash/data/localeWord.txt';
		$this->template->init_interface  = media::server_static_url().'flash/data/initData.txt';
		$this->template->render(TRUE);
	}
	
	/**
	 * 通过点击feed获得游戏奖励
	 */
	public function feedpresent()
	{
		
	}
}