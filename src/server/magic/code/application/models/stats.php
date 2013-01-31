<?php

defined('SYSPATH') or die('No direct access allowed.');

class Stats_Model {

    protected $db = array();
    protected static $table_role = "role";
    protected static $table_rolebyitem = "role_buyitem_log";
    protected static $libnum;
    protected static $instance;

    /**
     * Loads the database instance, if the database is not already loaded.
     *
     * @return  void
     */
    public function __construct() {
        self::$libnum = Kohana::config('base.cut_database_num');
        // Load the default database
        if (self::$libnum == 0) {
            $db_config_name = 'main';
            $config = Kohana::config('database.' . $db_config_name);
            $config ['connection'] ['type'] = "Mysql";
            $this->db [0] = Database::instance($db_config_name, $config);
        } else {
            for ($i = 0; $i < self::$libnum; $i++) {
                $db_config_name = 'main1_' . $i;
                $config = Kohana::config('database.' . $db_config_name);
                $config ['connection'] ['type'] = "Mysql";
                $this->db [$i] = Database::instance($db_config_name, $config);
            }
        }
    }

    public static function instance() {
        $class_name = get_called_class();
        self::$instance [$class_name] = new $class_name ();
        return self::$instance [$class_name];
    }

    //拼接表名
    public static function getTableName($tname, $i = 0) {
        if ($i == 0) {
            return $tname;
        } else {
            return $tname . '_' . $i;
        }
    }

    //执行SQL
    public function select($table, $select = array(), $where = array(), $order_by = array()) {
        $tconfig = Kohana::config('table.cut');
        $data = array();
        if (self::$libnum == 0 && $tconfig [$table] == 0) {
            $data [0] [0] = $this->db [0]->select($select)->where($where)->orderby($order_by)->get(self::getTableName($table));
        }
        if (self::$libnum == 0 && $tconfig [$table] != 0) {
            for ($i = 0; $i < $tconfig [$table]; $i++) {
                $data [0] [$i] = $this->db [0]->select($select)->where($where)->orderby($order_by)->get(self::getTableName($table, $i));
            }
        }
        if (self::$libnum != 0 && $tconfig [$table] == 0) {
            for ($i = 0; $i < self::$libnum; $i++) {
                $data [$i] [0] = $this->db [$i]->select($select)->where($where)->orderby($order_by)->get(self::getTableName($table));
            }
        }
        if (self::$libnum != 0 && $tconfig [$table] != 0) {
            for ($i = 0; $i < self::$libnum; $i++) {
                for ($j = 0; $j < $tconfig [$table]; $j++) {
                    $data [$i] [$j] = $this->db [$i]->select($select)->where($where)->orderby($order_by)->get(self::getTableName($table, $j));
                }
            }
        }
        $result = array();
        $j = 0;
        for ($i = 0; $i < count($data); $i++) {
            for ($k = 0; $k < count($data [$i]); $k++) {
                foreach ($data [$i] [$k] as $item) {
                    $result [$j] = $item;
                    $j++;
                }
            }
        }
        return $result;
    }

    //根据日期查询用户消费记录
    public function getBuyItemLogByTime($where) {
        $select = array("id", "rid", "rlevel", "itemid", "num", "price", "total", "sumtime");
        $data = $this->select(self::$table_rolebyitem, $select, $where);
        return $data;
    }

    public function getInfoCreatByTime($where) {
        $select = array("id", "created_time", "newbie");
        $data = $this->select(self::$table_role, $select, $where);
        return $data;
    }

    public function getInfoLoginByTime($where) {
        $select = array("id", "login_time", "level");
        $data = $this->select(self::$table_role, $select, $where);
        return $data;
    }

    /* ---public function test() {
      for($i = 0; $i < 20000; $i ++) {
      $arr = array ("id" => $i, "name" => "zyz", "max_mp" => 9999, "invite_friends_num" => 5, "study_magic_num" => 5, "fiddle_students" => 5, "tile_x_length" => 5, "tile_z_length" => 5, "cur_scene_id" => 5, "popularity" => 5, "login_time" => 1, "updated_time" => date ( "Y-m-d H:i:s", time () ) );
      $result = $this->db [0]->query("INSERT INTO `".self::$table_role."`(`id`,`name`,`exp`,`level`,`max_exp`,`max_mp_add`,`mp`,`max_mp`,`mp_set_time`,`mp_recovery_rate_plus`,`red`,`blue`,`green`,`gmoney`,`major_magic`,`deal_level`,`house_name`,`avatar_id`,`invite_friends_num`,`study_magic_num`,`newbie`,`trans_type`,`trans_start_time`,`fiddle_students`,`tile_x_length`,`tile_z_length`,`cur_scene_id`,`popularity`,`login_time`,`created_time`,`updated_time`) VALUES ( '".$i."','222','222','22','100','0','0','0','0','0','0','0','0','0','0','1','2','2','2','2','2','0','0','0','2','2','2','2','2','0000-00-00 00:00:00',CURRENT_TIMESTAMP)");
      unset($arr);
      }
      return count ( $result );
      }---- */
}