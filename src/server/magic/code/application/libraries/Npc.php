<?php defined('SYSPATH') OR die('No direct access allowed.');
/*
 * Created on 2009-2-21
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * 观察者
 */
class Npc {
    protected static $instace = null;
    
    public function __construct()
    {
    	
    }

    /**
     * 单件模式获取对象,防止重复读取数据库
     */
    public static function instance()
    {
        if (self::$instace === null) {
            self::$instace = new npc();
        }

        return self::$instance;
    }
    
    /**
     * 可以防止多个npc数据冲突
     * @param unknown_type $npc_id
     */
    public static function getNpcData($npc_id)
    {
    	static $npc_data = array();
		//这里可能也会有多个npc的问题
		if (!isset($npc_data[$npc_id])) {
    		$npc_model = new npc_Model();
			$npc_data[$npc_id] = $npc_model->getDataByID($npc_id);
    	}
    	
    	return $npc_data[$npc_id];
    }
}
?>
