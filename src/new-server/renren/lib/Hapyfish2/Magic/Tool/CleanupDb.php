<?php

class Hapyfish2_Magic_Tool_CleanupDb
{
    public static $logFile = 'crontask_clearDb';

	public static function cleanGift($tmExpire)
	{

	    try {
	        info_log('cleanup-Gift:', self::$logFile);
    	    for ($dbid=0; $dbid<DATABASE_NODE_NUM; $dbid++) {
    	        $db = Hapyfish2_Db_FactoryTool::getDB($dbid);
    	        $wdb = $db['w'];
    	        $strMsg = '';
    	        for ($i=0; $i<10; $i++) {
    	            $tbName = 'magic_user_gift_bag_'.$i;
                    $sql = "DELETE FROM $tbName WHERE create_time<:tm ";
                    $rst = $wdb->query($sql, array('tm' => $tmExpire));
                    $strMsg .= $rst->rowCount() . "\t";
                    //$strMsg .= $rst . "\t";
    	        }

    	        info_log('db'.$dbid.': '.$strMsg, self::$logFile);
    	    }
	    } catch (Exception $e) {
			info_log($e->getMessage(), self::$logFile);
			return false;
		}

		return true;
	}

}