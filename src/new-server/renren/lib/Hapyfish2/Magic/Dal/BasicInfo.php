<?php

class Hapyfish2_Magic_Dal_BasicInfo
{
    protected static $_instance;

    protected function getDB()
    {
    	$key = 'db_0';
    	return Hapyfish2_Db_Factory::getBasicDB($key);
    }

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Dal_BasicInfo
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getFeedTemplate()
    {
    	$sql = "SELECT id,title FROM magic_feed_template";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchPairs($sql);
    }

    public function getAvatarList()
    {
    	$sql = "SELECT id,name,type,classname FROM magic_avatar";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getBuildingList()
    {
    	$sql = "SELECT id,type,class_name,name,level,size_x,size_y,size_z,can_sell,sell_coin,isnew,gift_effect_mp,effect_mp,limit_user_level,door_guest_limit,door_cooldown FROM magic_building";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getUserLevelList()
    {
    	$sql = "SELECT level,exp,levelup_exp,limit_desk,limit_student,tile_size_add,tile_size,max_mp,max_mp_add,levelup_gmoney,levelup_item,levelup_decors,coin FROM magic_level";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getMagicStudyList()
    {
    	$sql = "SELECT id,type,name,level,class_name1,class_name2,coin,gold,spend_time,need_mp,gain_coin,gain_exp,abnormal_percent,steal_low,steal_high,steal_friend_low,steal_friend_high,steal_rate_limit,content FROM magic_magic_study";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getMagicMixList()
    {
    	$sql = "SELECT id,type,class_name,name,level,building,coin,gold,need_building,need_item FROM magic_magic_mix";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getMagicTransList()
    {
    	$sql = "SELECT id,name,class_name,level,coin,gold,magic_time,need_mp,gain_items,gain_exp,content FROM magic_magic_trans";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getItemList()
    {
    	$sql = "SELECT id,name,class_name,content,add_mp,coin,gold,type,limit_time,canbuy,new FROM magic_item";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getMonsterList()
    {
    	$sql = "SELECT id,scene_id,hp,hp_return,mp,drop_items,coin,exp,chats,avatar_id,name,heal,num FROM magic_monster";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getTaskTrunkList()
    {
    	$sql = "SELECT id,child_id,`level`,scene_name,start_npc_id,start_scene_id,finish_npc_id,finish_scene_id,icon_class,`name`,intro,condition_intro,`type`,`cid`,`num`,icon_condition,award_prop,award_items,award_decors,story_id FROM magic_task_trunk";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getTaskBranchList()
    {
    	$sql = "SELECT id,scene_name,start_npc_id,start_scene_id,finish_npc_id,finish_scene_id,icon_class,`name`,intro,condition_intro,`type`,`cid`,`num`,icon_condition,award_prop,award_items,award_decors FROM magic_task_branch";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getTaskDailyList()
    {
    	$sql = "SELECT id,`level`,icon_class,`name`,intro,condition_intro,`type`,`cid`,`num`,icon_condition,award_prop,award_items,award_decors,need_field FROM magic_task_daily";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getTaskTutorialList()
    {
    	$sql = "SELECT id,`level`,scene_name,start_npc_id,start_scene_id,finish_npc_id,finish_scene_id,icon_class,`name`,intro,condition_intro,`type`,`cid`,`num`,icon_condition,award_prop,award_items,award_decors FROM magic_task_tutorial";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getNpcList()
    {
    	$sql = "SELECT id,`name`,scene_id,avatar_id,x,y,z,click_type,click_value,talks,shop,face_x,face_y FROM magic_npc";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getTutorialList()
    {
    	$sql = "SELECT id,`index`,`name`,icon,event_type,chats,act_tips,contact,contactevent,gold,items FROM magic_tutorial";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getSceneSizeList()
    {
    	$sql = "SELECT id,level,size,coin,gold,friend_num FROM magic_scene_size";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getHouseLevelList()
    {
    	$sql = "SELECT id,mp,desk_limit,student_limit,coin,gold,items,decors FROM magic_house_level";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getStudentLevelList()
    {
    	$sql = "SELECT id,exp FROM magic_student_level";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getStudentList()
    {
    	$sql = "SELECT id,avatar_id,unlock_mp,unlock_level,content FROM magic_student";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getStudentAwardList($sid)
    {
    	$sql = "SELECT level,prop,items,decors FROM magic_student_awards WHERE sid=:sid";

        $db = $this->getDB();
        $rdb = $db['r'];

        $data = $rdb->fetchAssoc($sql, array('sid' => $sid));
        $list = array();
        if ($data) {
        	foreach ($data as $k => $v) {
        		$list[$k] = array(
        			'level' => $v['level'],
        			'prop' => json_decode($v['prop'], true),
        			'items' => json_decode($v['items'], true),
        			'decors' => json_decode($v['decors'], true)
        		);
        	}
        }

        return $list;
    }

    public function getOneStory($storyId)
    {
    	$sql = "SELECT id,npc_id,avatar_id,x,y,face_x,face_y,content,camera,wait,immediately,hide,task_id,decors,items,coin,gold,chat_time FROM magic_story WHERE story_id=:sid";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql, array('sid' => $storyId));
    }

    public function getTaskTypeList()
    {
    	$sql = "SELECT id,content FROM magic_task_type";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getDailyAwardList()
    {
    	$sql = "SELECT id,base_award,fans_award FROM magic_daily_award";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getActivityList()
    {
    	$sql = "SELECT id,act,link_text,caption,description,content,awards FROM magic_activity";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getCharacterList()
    {
    	$sql = "SELECT a.id,a.name,a.classname,c.price_type,c.price FROM magic_character c, magic_avatar a WHERE c.id=a.id";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }


    /* map basic */
    public function getMapSceneList()
    {
    	$sql = "SELECT * FROM magic_map_copy WHERE type=1 OR type=3";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getMapCopyList()
    {
    	$sql = "SELECT * FROM magic_map_copy WHERE type=2";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getMapBuildingList()
    {
    	$sql = "SELECT * FROM magic_map_building";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getMapMonsterList()
    {
    	$sql = "SELECT * FROM magic_map_monster";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getMapAnimationList()
    {
    	$sql = "SELECT * FROM magic_map_animation";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }

    public function getMapTaskList()
    {
    	$sql = "SELECT * FROM magic_map_task";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }
    /* -map basic */

    /* map copy related transcritp */
    public function getMapCopyDecorList($mapId)
    {
    	$sql = "SELECT * FROM magic_map_copy_decor WHERE map_id=:map_id";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql, array('map_id'=>$mapId));
    }
    public function getMapCopyPortalList($mapId)
    {
    	$sql = "SELECT * FROM magic_map_copy_portal WHERE map_id=:map_id";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql, array('map_id'=>$mapId));
    }
    public function getMapCopyFloorList($mapId)
    {
    	$sql = "SELECT * FROM magic_map_copy_floor WHERE map_id=:map_id";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql, array('map_id'=>$mapId));
    }
    public function getMapCopyGhostList($mapId)
    {
    	$sql = "SELECT * FROM magic_map_copy_ghost WHERE map_id=:map_id";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql, array('map_id'=>$mapId));
    }
    public function getMapCopyMineList($mapId)
    {
    	$sql = "SELECT * FROM magic_map_copy_mine WHERE map_id=:map_id";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql, array('map_id'=>$mapId));
    }
	/* -map copy related transcritp */
}