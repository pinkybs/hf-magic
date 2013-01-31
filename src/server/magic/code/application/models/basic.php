<?php
class Basic_Model
{
	protected $db;
	private $seq_id_table = 'seq_id';
	private $building_list_table = 'basic_building';
	private $level_list_table = 'basic_level';
	private $study_magic_list_table = 'basic_magic_study';
	private $trans_magic_list_table = 'basic_magic_trans';
	private $mix_magic_list_table = 'basic_magic_mix';
	private $item_list_table = 'basic_item';
	private $feed_list_table = 'basic_feed';
	private $event_trunk_table = 'basic_event_trunk';
	private $event_branch_table = 'basic_event_branch';
	private $event_daily_table = 'basic_event_daily';
	private $event_newbie_table = 'basic_event_newbie';
	private $deal_table = 'basic_deal';
	private $init_table = 'basic_init';
	public $npc_table = 'basic_npc';
	public $scene_table = 'basic_scene';
	public $avatar_table = 'basic_avatar';
	public $basic_table = 'basic_basic';
	public $newbie_table = 'basic_newbie';
	public $monster_table = 'basic_monster';
	public $size_scene_table = 'basic_size_scene';
	public $house_level_table = "basic_house_level";
	public $student_level_table = "basic_student_level";
	public $student_awards_table = "basic_student_awards";
	public $student_table = 'basic_student';
	public $story_table = 'basic_story';
	public $story_talk_table = 'basic_story_talk';
	
	/**
	 * Loads the database instance, if the database is not already loaded.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		if ( ! is_object($this->db))
		{
			// Load the default database
			$this->db = Database::instance('basic');
			
			$this->db_cache = DbCache::instance('basic');
		}
	}
	
	/**
	 * 获取静态装饰数组
	 */
	public function getBuildingList()
	{
		$result = $this->db_cache->select($this->building_list_table);
		
		return $result;
	}
	
	public function getStoryTalkListByStoryId($story_id)
	{
		$result = $this->db_cache->select($this->story_talk_table, array('story_id' => $story_id));
		
		return $result;
	}
	
	public function getStoryByExp($story_exp, $role_exp)
	{
		$result = $this->db_cache->select($this->story_table);
		
		$data = array();
		foreach ($result as $vl) {
			if ($vl[0]['exp'] <= $role_exp && $vl[0]['exp'] > $story_exp) {
				$data = $vl[0];
			}
		}
		
		if (empty($data)) {
			return false;
		}
		
		return $data;
	}
	
	public function getHouseLevelList()
	{
		$result = $this->db_cache->select($this->house_level_table);
		
		return $result;
	}
	
	public function getHouseLevelData($level)
	{
		$result = $this->db_cache->select($this->house_level_table, array("id" => $level));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getStudentAwardsData($sid, $level)
	{
		$result = $this->db_cache->select($this->student_awards_table, array("student_id" => $sid, 'level' => $level));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getStudentAwardsList()
	{
		$result = $this->db_cache->select($this->student_awards_table);
		
		return $result;
	}
	
	public function getStudentLevelList()
	{
		$result = $this->db_cache->select($this->student_level_table);
		
		return $result;
	}
	
	public function getStudentLevelDataByLevel($level)
	{
		$result = $this->db_cache->select($this->student_level_table, array('id' => $level));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getStudentList()
	{
		$result = $this->db_cache->select($this->student_table);
		
		return $result;
	}
	
	public function getStudentListByLevel($level = 1)
	{
		$result = $this->db_cache->select($this->student_table, array('unlock_level' => $level));
		
		return $result;
	}
	
	public function getBasicList($table)
	{
		$result = $this->db_cache->select($table);
		
		return $result;
	}
	
	public function getAvatarList()
	{
		$result = $this->db_cache->select($this->avatar_table);
		
		return $result;
	}
	
	public function getAvatarListByType($type = 2)
	{
		$result = $this->db_cache->select($this->avatar_table, array('type' => $type));
		
		$data = array();
		foreach ($result as $key => $vl) {
			$data[$key] = $vl[0];
		}
		
		return $data;
	}
	
	public function getNpcList()
	{
		$result = $this->db_cache->select($this->npc_table);
		
		return $result;
	}
	
	public function getMonsterList()
	{
		$result = $this->db_cache->select($this->monster_table);
		
		return $result;
	}
	
	public function getSizeSceneList()
	{
		$result = $this->db_cache->select($this->size_scene_table);
		
		return $result;
	}
	
	public function getSizeSceneById($id)
	{
		$result = $this->db_cache->select($this->size_scene_table, array('id' => $id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getMonsterListBySceneId($scene_id)
	{
		$result = $this->db_cache->select($this->monster_table, array('scene_id' => $scene_id));
		
		$data = array();
		foreach ($result as $vl) {
			$data[$vl[0]['id']] = $vl[0];
		}
		
		return $data;
	}
	
	public function getMonsterDataById($id)
	{
		$result = $this->db_cache->select($this->monster_table, array('id' => $id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getBasicBaseList()
	{
		$result = $this->db_cache->select($this->basic_table);
		
		return $result;
	}
	
	public function getSceneList()
	{
		$result = $this->db_cache->select($this->scene_table);
		
		return $result;
	}
	
	public function getNewbieList()
	{
		$result = $this->db_cache->select($this->newbie_table);
		
		return $result;
	}
	
	public function getNewbieDataById($id)
	{
		$result = $this->db_cache->select($this->newbie_table, array('id' => $id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getSceneListByState($state = 1)
	{
		$result = $this->db_cache->select($this->scene_table, array('state' => $state));
		
		return $result;
	}
	
	public function getSceneDataById($id)
	{
		$result = $this->db_cache->select($this->scene_table, array('id' => $id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	/**
	 * 获取某一建筑的基础数据
	 */
	public function getBuildingDataById($id)
	{
		$result = $this->db_cache->select($this->building_list_table, array('id' => $id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getMagicStudyDataById($id)
	{
		$result = $this->db_cache->select($this->study_magic_list_table, array('id' => $id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getMagicTransDataById($id)
	{
		$result = $this->db_cache->select($this->trans_magic_list_table, array('id' => $id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getInitDataById($id = 1)
	{
		$result = $this->db_cache->select($this->init_table, array('id' => $id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getMagicMixDataById($id)
	{
		$result = $this->db_cache->select($this->mix_magic_list_table, array('id' => $id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getLevelList()
	{
		$result = $this->db_cache->select($this->level_list_table);
		
		return $result;
	}
	
	/**
	 * 主线任务表
	 */
	public function getEventTrunkList()
	{
		$result = $this->db_cache->select($this->event_trunk_table);
		
		return $result;
	}
	
	/**
	 * 支线任务表
	 */
	public function getEventBranchList()
	{
		$result = $this->db_cache->select($this->event_branch_table);
		
		return $result;
	}
	
	/**
	 * 支线任务表根据经验
	 */
	public function getEventBranchExpList($last_exp)
	{
		$result = $this->db_cache->select($this->event_branch_table);
		
		$data = array();
		foreach ($result as $vl) {
			if ($vl[0]['exp'] > $last_exp) {
				array_push($data, $vl);
			}
		}
		
		return $data;
	}
	
	/**
	 * 日常任务表
	 */
	public function getEventDailyList()
	{
		$result = $this->db_cache->select($this->event_daily_table);
		
		return $result;
	}
	
	/**
	 * 日常任务表根据等级
	 */
	public function getEventDailyLevelList($level)
	{
		$result = $this->db_cache->select($this->event_daily_table, array('level' => $level));
		
		return $result;
	}
	
	/**
	 * 新手任务表
	 */
	public function getEventNewbieList()
	{
		$result = $this->db_cache->select($this->event_newbie_table);
		
		return $result;
	}
	
	public function getEventTrunkById($id)
	{
		$result = $this->db_cache->select($this->event_trunk_table, array('id' => $id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getEventBranchById($id)
	{
		$result = $this->db_cache->select($this->event_branch_table, array('id' => $id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getEventDailyById($id)
	{
		$result = $this->db_cache->select($this->event_daily_table, array('id' => $id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getEventNewbieById($id)
	{
		$result = $this->db_cache->select($this->event_newbie_table, array('id' => $id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getStudyMagicList()
	{
		$result = $this->db_cache->select($this->study_magic_list_table);
		
		return $result;
	}
	
	
	public function getMixMagicList()
	{
		$result = $this->db_cache->select($this->mix_magic_list_table, array(), array('id' => 'ASC'));
		
		return $result;
	}
	
	
	public function getTransMagicList()
	{
		$result = $this->db_cache->select($this->trans_magic_list_table);
		
		return $result;
	}
	
	public function getItemList()
	{
		$result = $this->db_cache->select($this->item_list_table);
		
		return $result;
	}

	public function getItemById($id)
	{
		$result = $this->db_cache->select($this->item_list_table, array('id' => $id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	/**
	 * 更新自增长数据表
	 *
	 */
	public function updateSeqId()
	{
		$result = $this->db->query(
						"UPDATE ".
							"$this->seq_id_table ".
						"SET ".
							"id = LAST_INSERT_ID(id + 1) ".
						"WHERE ".
							"name = 'uid'"
				);
		
		$data = $this->db->query(
						"SELECT ".
							"LAST_INSERT_ID(id) ".
						"FROM ".
							"$this->seq_id_table ".
						"WHERE ".
							"name = 'uid'"
				)->current();

		return $data["LAST_INSERT_ID(id)"];
	}
	
	public function getTableByName($tableName)
	{
		$data = $this->db->query(
						"SHOW ".
							"FIELDS ".
						"FROM ".
							"$tableName "
				);
		$fieldList = array();
		foreach ( $data as $val ) {
			$fieldList[] = $val['Field'];
		}
		return $fieldList;
	}

	public function getDbByName($dbName)
	{
		$data = $this->db->query(
						"SHOW ".
							"TABLES ".
						"FROM ".
							"$dbName "
				);

        $tableList = array();
        $i = 'Tables_in_'.$dbName;
        foreach ( $data as $val ) {
        	$tableList[] = $val[$i];
        }
		return $tableList;
	}
	
/**************************************************/
	
	public function getFeedTemplate()
	{
		$result = $this->db_cache->select($this->feed_list_table);
		
		return $result;
	}
	
	public function getDealLevel()
	{
		$result = $this->db_cache->select($this->deal_table);
		
		return $result;
	}
	
}