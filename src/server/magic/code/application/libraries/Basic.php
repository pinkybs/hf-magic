<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class Basic {
	//单例
	private static $instance;
	private $uid;
	
	public function __construct($uid)
	{
		$this->uid = $uid;
	}
	
	/**
	 * Singleton instance of Basic
	 */
	public static function instance($uid)
	{
		if (!isset(self::$instance[$uid]))
		{
			// Create a new instance
			self::$instance[$uid] = new Basic($uid);
		}

		return self::$instance[$uid];
	}
	
	public function getRoleId()
	{
		$map_model = new Uid_Map_Model($this->uid);
		$data = $map_model->getRoleIdByUid($this->uid);

		$role_id = 0;
		if ($data = current($data)) {
			$role_id = $data['role_id'];
		}
		
		return $role_id;
	}
	
	public function getDbByName($dbName)
	{
		$basic_model = new Basic_Model();
		$tableList = $basic_model->getDbByName($dbName);
		
		$result = array();
		foreach ( $tableList AS $table ) {
			$fieldsList = self::getTableByName($table);
			$result[$table] = $fieldsList;
		}
		
		return $result;
	}
	
	public function getLevelData()
	{
		$basic_model = new Basic_Model();
		$basicLevelInfo = $basic_model->getLevelList();
		$basicLevelArray = array();
		foreach ( $basicLevelInfo as $basicLevel ) {
			$basicLevelArray[$basicLevel[0]['level']] = $basicLevel[0];
		}
		
		return $basicLevelArray;
	}
	
	public static function getCurLevelData($level)
	{
		$basic_model = new Basic_Model();
		$basicLevelInfo = $basic_model->getLevelList();
		$basicLevelArray = array();
		foreach ( $basicLevelInfo as $basicLevel ) {
			$basicLevelArray[$basicLevel[0]['level']] = $basicLevel[0];
		}
		
		return $basicLevelArray[$level];
	}
	
	public static function getHouseLevelData($level)
	{
		$basic_model = new Basic_Model();
		$basic_level_data = $basic_model->getHouseLevelData($level);
		
		return $basic_level_data;
	}
	
	public function getMagicData()
	{
		return $this->getFormatData('getStudyMagicList');
	}
	
	public function getFormatData($func)
	{
		$basic_model = new Basic_Model();
		$basicFormatInfo = $basic_model->$func();
		$basicFormatArray = array();
		foreach ( $basicFormatInfo as $basicFormat ) {
			$basicFormatArray[$basicFormat[0]['id']] = $basicFormat[0];
		}
		
		return $basicFormatArray;
	}
	
	public function getTableByName($tableName)
	{
		$basic_model = new Basic_Model();
		$fieldsList = $basic_model->getTableByName($tableName);
		
		return $fieldsList;
	}
}