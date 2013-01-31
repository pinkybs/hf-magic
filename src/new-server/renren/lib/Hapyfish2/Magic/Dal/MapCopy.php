<?php


class Hapyfish2_Magic_Dal_MapCopy
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Dal_MapCopy
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getTableName($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
    	return 'magic_user_map_copy_info_' . $id;
    }

    public function getInfo($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT * FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

    public function update($uid, $info)
    {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
    	$where = $wdb->quoteinto('uid = ?', $uid);
        $wdb->update($tbname, $info, $where);
    }

    public function add($uid, $info)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "INSERT INTO $tbname(uid,map_parent,map_ids,fids,enter_time) VALUES(:uid,:map_parent,:map_ids,:fids,:enter_time)
    			ON DUPLICATE KEY UPDATE map_parent=:map_parent, map_ids=:map_ids, fids=:fids, enter_time=:enter_time, update_time=:update_time";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql, array('uid' => $info['uid'], 'map_parent' => $info['map_parent'], 'map_ids' => $info['map_ids'], 'fids' => $info['fids'], 'enter_time' => $info['enter_time'], 'update_time' => $info['update_time']));
    }

}