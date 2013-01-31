<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
/**
 * 
 * @author beck917
 * 后台跑的脚本
 */
class Cron_Controller extends Controller {
	private $db;
	
	public function __construct() {
		//判断传入的正确参数,防止前端非法调用
		//if (PHP_SAPI !== 'cli') {
		//	die('oh,shit,hack erorr....wuhahhah....');
		//}
		

		$this->db = Database::instance ( 'default' );
		
		parent::__construct ();
	}
	
	public function flush() {
		$cache = new Cache ();
		$cache->delete_all ();
		
		$cache_config = Kohana::config('cache');
		
		foreach ($cache_config as $key => $vl) {
			echo $key."<br>";
			$cache = Cache::instance($key);
			$cache->delete_all ();
		}
		
		echo '清空配置数据缓存成功  --' . date ( "Y-m-d H:i:s", time () ) . "\n";
	}
	
	public function invitecode()
	{
		$str = '<?php '."\n";
		for ($i = 1; $i <= 100; $i++) {
			$code = sha1(md5($i+'cuiidasknsyff'));
			$str.= '$config["'.$code.'"] = 1;'."\n";
		}
		
		file_put_contents ( 'application/config/invitecode.php', $str );
		
		echo '邀请码生成成功';
	}
	
	public function getdb() {
		$dbName = $this->input->get ( 'db' );
		
		$basic = Basic::instance ( '' );
		$fieldsList = $basic->getDbByName ( $dbName );
		
		echo $fieldsList;
	}
	
	public function initstatic() {
		$basic_model = new Basic_Model ();
		$building_list = $basic_model->getBuildingList ();
		$decor_class = common::transform ( 'decorClass', $building_list );
		
		$level_list = $basic_model->getLevelList ();
		$level_infos = common::transform ( 'levelInfos', $level_list );
		
		$magic_study_list = $basic_model->getStudyMagicList ();
		$magic_class = common::transform ( 'magicClass', $magic_study_list );
		
		$magic_mix_list = $basic_model->getMixMagicList ();
		$magic_mix_list_new = array ();
		/**
		foreach ($magic_mix_list as $key => $vl) {
			fore$vl[0];
			if ($key == 'need_building' || $key == 'need_item') {
				var_dump($vl[0]);
				$magic_mix_list_new[$key] = json_decode($vl[0]);
			}
		}
		 */
		$trans_magic_class = common::transform ( 'mixMagicClass', $magic_mix_list );
		
		$magic_trans_list = $basic_model->getTransMagicList ();
		$mix_magic_class = common::transform ( 'transMagicClass', $magic_trans_list );
		
		$item_list = $basic_model->getItemList ();
		$item_class = common::transform ( 'itemClass', $item_list );
		
		//怪物
		$monster_list = $basic_model->getMonsterList ();
		$enemy_class = common::transform ( 'enemyClass', $monster_list );
		
		//=========================获取任务==========================================================
		$event_trunk_list = $basic_model->getEventTrunkList ();
		$event_trunk_class = common::transform ( 'taskClass', $event_trunk_list );
		
		//主线
		$awards = $this->_event_awards ( $event_trunk_list, $event_trunk_class );
		$this->_icon_condition ( $event_trunk_class );
		
		$event_branch_list = $basic_model->getEventBranchList ();
		$event_branch_class = common::transform ( 'taskClass', $event_branch_list );
		
		//支线
		$awards = $this->_event_awards ( $event_branch_list, $event_branch_class );
		$this->_icon_condition ( $event_branch_class );
		
		$event_daily_list = $basic_model->getEventDailyList ();
		$event_daily_class = common::transform ( 'taskClass', $event_daily_list );
		
		//日常
		$awards = $this->_event_awards ( $event_daily_list, $event_daily_class );
		$this->_icon_condition ( $event_daily_class );
		
		$event_newbie_list = $basic_model->getEventNewbieList ();
		$event_newbie_class = common::transform ( 'taskClass', $event_newbie_list );
		
		//新手
		$awards = $this->_event_awards ( $event_newbie_list, $event_newbie_class );
		$this->_icon_condition ( $event_newbie_class );
		
		$event_class = array ('taskClass' => array_merge ( $event_trunk_class ['taskClass'], $event_branch_class ['taskClass'], $event_daily_class ['taskClass'], $event_newbie_class ['taskClass'] ) );
		//============================================================================================
		

		//水晶交换
		$switch_list = $basic_model->getDealLevel ();
		$switch_class = common::transform ( 'switchLevel', $switch_list );
		
		//avatar
		$avatar_list = $basic_model->getAvatarList ();
		$avatar_class = common::transform ( 'avatarClass', $avatar_list );
		
		//npc
		$npc_list = $basic_model->getNpcList ();
		$npc_class = common::transform ( 'npcClass', $npc_list );
		
		$newbie_list = $basic_model->getNewbieList ();
		$newbie_class = common::transform ( 'guideClass', $newbie_list );
		
		//scene
		$size_scene_list = $basic_model->getSizeSceneList();
		$size_scene_class = common::transform('roomSizeClass', $size_scene_list);
		
		//scene
		$scene_list = $basic_model->getSceneList ();
		$scene_class = common::transform ( 'sceneClass', $scene_list );
		
		$house_level_list = $basic_model->getHouseLevelList();
		$house_level_class = common::transform('roomLevelClass', $house_level_list);
		
		$student_level_list = $basic_model->getStudentLevelList();
		$student_level_class = common::transform('studentLevelClass', $student_level_list);
		
		$student_list = $basic_model->getStudentList();
		$student_class = common::transform('studentClass', $student_list);
		
		//前端格式化
		foreach ( $scene_class ['sceneClass'] as $key => $vl ) {
			$tmp = arr::_multi_serial_to_array ( $vl ['needs1'] );
			foreach ( $tmp as $k => $v ) {
				$tmp [$k] ['type'] = $v ['cat'];
				unset ( $tmp [$k] ['cat'] );
			}
			$scene_class ['sceneClass'] [$key] ['needs1'] = $tmp;
			
			$tmp = arr::_multi_serial_to_array ( $vl ['needs2'] );
			foreach ( $tmp as $k => $v ) {
				$tmp [$k] ['type'] = $v ['cat'];
				unset ( $tmp [$k] ['cat'] );
			}
			$scene_class ['sceneClass'] [$key] ['needs2'] = $tmp;
		}
		
		$output_array = array_merge ( $decor_class, $level_infos, $magic_class, $newbie_class, $enemy_class, 
		$trans_magic_class, $mix_magic_class, $item_class, $event_class, $switch_class, 
		$avatar_class, $npc_class, $scene_class, $size_scene_class, $house_level_class, $student_level_class, $student_class);
		
		//echo json_encode($output_array);
		

		file_put_contents ( '../media/static/file/config.json', json_encode ( $output_array ) );
		echo 'make static file ok!!';
	}
	
	private function _event_awards($event_trunk_list, &$event_class) {
		//符合前端格式化
		$i = 0;
		foreach ( $event_trunk_list as $vl ) {
			$awards = array ();
			$award = array ();
			if (! empty ( $vl [0] ['award_items'] )) {
				//var_dump($vl[0]['award_items']);
				foreach ( json_decode ( $vl [0] ['award_items'] ) as $value ) {
					$award ['type'] = 1;
					$award ['id'] = $value [0];
					$award ['num'] = $value [1];
					array_push ( $awards, $award );
				}
			}
			
			if (! empty ( $vl [0] ['award_prop'] )) {
				foreach ( arr::_serial_to_array ( $vl [0] ['award_prop'] ) as $key => $value ) {
					$award ['type'] = 3;
					$award ['id'] = $key;
					$award ['num'] = $value;
					
					array_push ( $awards, $award );
				}
			}
			
			if (! empty ( $vl [0] ['award_decors'] )) {
				foreach ( json_decode ( $vl [0] ['award_decors'] ) as $value ) {
					$award ['type'] = 2;
					$award ['id'] = $value [0];
					$award ['num'] = $value [1];
					array_push ( $awards, $award );
				}
			}
			$event_class ['taskClass'] [$i] ['awards'] = $awards;
			$i ++;
		}
		
		return $awards;
	}
	
	private function _icon_condition(&$task_class) {
		foreach ( $task_class ['taskClass'] as $key => $vl ) {
			$tmp = arr::_multi_serial_to_array ( $vl ['finish_condition'] );
			foreach ( $tmp as $k => $v ) {
				$tmp [$k] ['type'] = $v ['cat'];
				unset ( $tmp [$k] ['cat'] );
			}
			$task_class ['taskClass'] [$key] ['finish_condition'] = $tmp;
		}
	}
}