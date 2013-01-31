<?php

class Hapyfish2_Admin_Bll_Basic
{
	public static function getBasicTbList()
	{
		$aryList = self::getBasicList();

		//$list = array_keys($aryList);
	    return $aryList;
	}

    public static function getBasicTbByName($tbName)
	{
	    $tbInfo = null;
		$tbList = self::getBasicList();
        if (isset($tbList[$tbName])) {
            $tbInfo = $tbList[$tbName];
        }
	    return $tbInfo;
	}

	public static function generateBasicDataFile($tbName, $fileName)
	{
	    try {
	        $tbInfo = Hapyfish2_Admin_Bll_Basic::getBasicTbByName($tbName);
    	    if (!$tbInfo) {
                return 'table not found,please check.';
            }
            $content = '';
            foreach ($tbInfo['column'] as $col) {
                $content .= $col . "\t";
            }
            $content .= "\n";
            $dal = Hapyfish2_Admin_Dal_Basic::getDefaultInstance();
            $lstData = $dal->getBasicList($tbName);
            foreach ($lstData as $data) {
                foreach ($data as $val) {
                    $content .= $val . "\t";
                }
                $content .= "\n";
            }

            $dir = dirname($fileName);
            if (!is_dir($dir)) {
                mkdir($dir, 0700, true);
            }

            @unlink($fileName);
            $handle = fopen($fileName, 'w');
            if (!$handle) {
                return "can not open file: $fileName";
            }
            if (fwrite($handle, $content) === FALSE) {
                return "file write failed: $fileName";
            }
            fclose($handle);
	    }
	    catch (Exception $e) {
            return "fatal error:".$e->getMessage();
	    }

        return '';
	}

	public static function importBasicDataFromFile($tbName, $fileName, &$aryFailed)
	{

	    try {
	        $tbInfo = Hapyfish2_Admin_Bll_Basic::getBasicTbByName($tbName);
    	    if (!$tbInfo) {
                return false;
            }

            $cntFields = count($tbInfo['column']);

            $dal = Hapyfish2_Admin_Dal_Basic::getDefaultInstance();
            $cntSuccess = 0;
    	    $rowIdx = 0;
    	    $handle = fopen($fileName, "r");
            if ($handle) {
                while (!feof($handle)) {
                    $row = fgets($handle);
                    $row = trim($row);
                    $rowIdx ++;
                    if ($rowIdx==1) {
                        continue;
                    }

                    if (!$row) {
                        //$aryFailed[] = $rowIdx;
                        continue;
                    }
                    $data = explode("\t", $row);
                    if ($cntFields != count($data) && ($cntFields+1) != count($data)) {
                        $aryFailed[] = $rowIdx;
                        continue;
                    }

                    $info = array();
                    $colIdx = 0;
                    foreach ($tbInfo['column'] as $col=>$val) {
                        //$info[$col] = mb_convert_encoding($data[$colIdx], "UTF-8");
                        $info[$col] = ($data[$colIdx]);
                        $colIdx ++;
                    }
                    if ($colIdx > 0) {
                        $rst = $dal->addInfo($tbName, $info);
                        //info_log(json_encode($info), 'bbb');
                        $cntSuccess ++;
                    }
                }
                fclose($handle);
            }
	    }
	    catch (Exception $e) {
            info_log($e->getMessage(), 'errAdmin_Bll_Basic');
	    }

	    return $cntSuccess;
	}



    public static function getBasicList()
	{
		$list = array();
		$list['magic_activity'] = array(
		    'tbid'		=> 'magic_activity',
		    'name'	    => '平台消息基础表',
			'column'	=> array('id'=>'消息id','act'=>'动作','link_text'=>'连接文字','caption'=>'标题','description'=>'描述','content'=>'内容','awards'=>'奖励物品')
		);

		$list['magic_avatar'] = array(
		    'tbid'		=> 'magic_avatar',
		    'name'	    => '人物avatar基础表',
			'column'	=> array('id'=>'id','name'=>'名称','type'=>'类型','classname'=>'类名')
		);

		$list['magic_building'] = array(
		    'tbid'		=> 'magic_building',
		    'name'	    => '装饰物基础表',
			'column'	=> array('id'=>'id','type'=>'类型','class_name'=>'类名','name'=>'名称','level'=>'建筑等级','size_x'=>'长','size_y'=>'高','size_z'=>'宽','can_sell'=>'能否分解','sell_coin'=>'分解得到水晶百分比','isnew'=>'是否新装饰','gift_effect_mp'=>'赠送魔法值加成','effect_mp'=>'魔法加点','limit_user_level'=>'用户解锁等级','door_guest_limit'=>'门人数限制','door_cooldown'=>'门放人冷却时间')
		);

        $list['magic_character'] = array(
		    'tbid'		=> 'magic_character',
		    'name'	    => '人物换装基础表',
			'column'	=> array('id'=>'avatarid','price_type'=>'解锁价格类型','price'=>'解锁价格')
		);

		$list['magic_daily_award'] = array(
		    'tbid'		=> 'magic_daily_award',
		    'name'	    => '连续登陆奖励基础表',
			'column'	=> array('id'=>'天数','base_award'=>'基本奖励','fans_award'=>'加粉丝奖励')
		);

		$list['magic_feed_template'] = array(
		    'tbid'		=> 'magic_feed_template',
		    'name'	    => '消息基础表',
			'column'	=> array('id'=>'id','title'=>'消息内容模板')
		);

		$list['magic_gift'] = array(
		    'tbid'		=> 'magic_gift',
		    'name'	    => '礼物基础表',
			'column'	=> array('gid'=>'物品id','type'=>'类型','need_lev'=>'解锁等级','name'=>'名称','class_name'=>'类名','sort'=>'顺序号','is_online'=>'是否在线')
		);

		$list['magic_house_level'] = array(
		    'tbid'		=> 'magic_house_level',
		    'name'	    => '魔法值上限基础表',
			'column'	=> array('id'=>'id','mp'=>'魔法值','desk_limit'=>'限制书桌数','student_limit'=>'限制学生数','coin'=>'奖励金币','gold'=>'奖励宝石','items'=>'奖励道具','decors'=>'奖励装饰')
		);

		$list['magic_item'] = array(
		    'tbid'		=> 'magic_item',
		    'name'	    => '道具物品基础表',
			'column'	=> array('id'=>'物品id','name'=>'名称','class_name'=>'类名','content'=>'介绍','add_mp'=>'回复Mp值','coin'=>'金币价格','gold'=>'宝石价格','type'=>'类型','limit_time'=>'使用限制','canbuy'=>'是否可买','new'=>'是否新商品')
		);

		$list['magic_level'] = array(
		    'tbid'		=> 'magic_level',
		    'name'	    => '人物等级基础表',
			'column'	=> array('level'=>'等级','exp'=>'经验','levelup_exp'=>'到下级还需经验','limit_desk'=>'课桌限制','limit_student'=>'学生人数限制','tile_size_add'=>'增加面积','tile_size'=>'房间面积','max_mp'=>'最大Mp','max_mp_add'=>'最大Mp增加','levelup_gmoney'=>'升级奖励宝石','levelup_item'=>'升级奖励道具','levelup_decors'=>'升级奖励装饰','coin'=>'升级奖励金币')
		);

		$list['magic_magic_mix'] = array(
		    'tbid'		=> 'magic_magic_mix',
		    'name'	    => '合成基础表',
			'column'	=> array('id'=>'id','type'=>'类型','class_name'=>'类名','name'=>'名称','level'=>'魔法等级','building'=>'变化出物品','coin'=>'需要金币','gold'=>'需要宝石','need_building'=>'需要装饰','need_item'=>'需要道具')
		);

		$list['magic_magic_study'] = array(
		    'tbid'		=> 'magic_magic_study',
		    'name'	    => '学习魔法基础表',
			'column'	=> array('id'=>'魔法id','type'=>'类型','name'=>'魔法名称','level'=>'需要人物等级','class_name1'=>'图标素材','class_name2'=>'动效','coin'=>'学习需要金币','gold'=>'学习需要宝石','spend_time'=>'施法时间','need_mp'=>'消耗魔法值','gain_coin'=>'收益水晶','gain_exp'=>'获得经验','abnormal_percent'=>'发生异常概率','steal_low'=>'偷取获得百分比(低)','steal_high'=>'偷取获得百分比(高)','steal_friend_low'=>'被偷最低获得','steal_friend_high'=>'被偷最高获得','steal_rate_limit'=>'保护百分比','content'=>'描述')
		);

		$list['magic_magic_trans'] = array(
		    'tbid'		=> 'magic_magic_trans',
		    'name'	    => '变化术基础表',
			'column'	=> array('id'=>'魔法id','name'=>'魔法名称','class_name'=>'类名','level'=>'魔法等级','coin'=>'学习需要金币','gold'=>'学习需要宝石','magic_time'=>'变化时间','need_mp'=>'消耗魔法值','gain_items'=>'收益物品','gain_exp'=>'获得经验','content'=>'描述')
		);

		$list['magic_monster'] = array(
		    'tbid'		=> 'magic_monster',
		    'name'	    => '怪物基础表',
			'column'	=> array('id'=>'id','scene_id'=>'场景id','hp'=>'血量','hp_return'=>'回血速度','mp'=>'消耗Mp','drop_items'=>'掉落物品','coin'=>'掉落金币','exp'=>'掉落经验','chats'=>'死后台词','avatar_id'=>'avatarid','name'=>'名称','heal'=>'回血量','num'=>'数量')
		);

		$list['magic_npc'] = array(
		    'tbid'		=> 'magic_npc',
		    'name'	    => 'npc基础表',
			'column'	=> array('id'=>'id','name'=>'npc名字','scene_id'=>'所在场景','avatar_id'=>'avatarid','x'=>'场景坐标','y'=>'场景坐标','z'=>'场景坐标','click_type'=>'clicktype','click_value'=>'clickvalue','talks'=>'对白','shop'=>'shop','face_x'=>'face_x','face_y'=>'face_y')
		);

		$list['magic_scene'] = array(
		    'tbid'		=> 'magic_scene',
		    'name'	    => '场景基础表',
			'column'	=> array('id'=>'id','name'=>'名称','content'=>'描述','icon_classname'=>'类名','x'=>'bgiconn坐标','y'=>'bgiconn坐标','bg'=>'对应大BG','state'=>'初始状态','need_level'=>'需要人物等级','condition1'=>'开启条件1','condition2'=>'开启条件2','mp'=>'移动消耗魔法','monster_xy'=>'怪坐标','entrances'=>'出入口','node_str'=>'场景数据date','size_x'=>'numCols','size_y'=>'numRows','isostart_x'=>'isoStartX','isostart_y'=>'isoStartY')
		);

		$list['magic_scene_size'] = array(
		    'tbid'		=> 'magic_scene_size',
		    'name'	    => '房间大小基础表',
			'column'	=> array('id'=>'id','level'=>'max_mp','size'=>'扩展后大笑','coin'=>'需金币','gold'=>'需宝石','friend_num'=>'需好友数')
		);

		$list['magic_story'] = array(
		    'tbid'		=> 'magic_story',
		    'name'	    => '剧情基础表',
			'column'	=> array('id'=>'id','story_id'=>'story_id','npc_id'=>'npc_id','avatar_id'=>'avatarid','x'=>'x','y'=>'y','face_x'=>'faceX','face_y'=>'faceY','content'=>'对白','camera'=>'镜头跟随','wait'=>'等待','immediately'=>'是否立即s','hide'=>'hide','task_id'=>'taskId','decors'=>'decorId','items'=>'itemId','coin'=>'获得金币','gold'=>'获得宝石','chat_time'=>'chatTime')
		);

		$list['magic_student'] = array(
		    'tbid'		=> 'magic_student',
		    'name'	    => '学生基础表',
			'column'	=> array('id'=>'id','avatar_id'=>'avatarid','unlock_mp'=>'最大魔法值','unlock_level'=>'解锁需要教室等级','content'=>'学生介绍')
		);

		$list['magic_student_awards'] = array(
		    'tbid'		=> 'magic_student_awards',
		    'name'	    => '学生升级奖励基础表',
			'column'	=> array('sid'=>'id','level'=>'等级','prop'=>'奖励用户属性','items'=>'奖励道具','decors'=>'奖励装饰')
		);

		$list['magic_student_level'] = array(
		    'tbid'		=> 'magic_student_level',
		    'name'	    => '学生等级基础表',
			'column'	=> array('id'=>'id','exp'=>'需要经验')
		);

		$list['magic_task_branch'] = array(
		    'tbid'		=> 'magic_task_branch',
		    'name'	    => '订单任务基础表',
			'column'	=> array('id'=>'id','scene_name'=>'场景名称','start_npc_id'=>'start_npc_id','start_scene_id'=>'场景id','finish_npc_id'=>'finish_npc_id','finish_scene_id'=>'场景id','icon_class'=>'iconclass','name'=>'订单','intro'=>'任务描述','condition_intro'=>'任务条件描述','type'=>'type','cid'=>'id','num'=>'数量','icon_condition'=>'图标显示用type','award_prop'=>'奖励属性','award_items'=>'奖励物品','award_decors'=>'奖励装饰')
		);

		$list['magic_task_daily'] = array(
		    'tbid'		=> 'magic_task_daily',
		    'name'	    => '日常任务基础表',
			'column'	=> array('id'=>'id','level'=>'开启条件（LV)','icon_class'=>'iconclass','name'=>'名称','intro'=>'描述','condition_intro'=>'条件描述','type'=>'type','cid'=>'id','num'=>'数量','icon_condition'=>'显示用type','award_prop'=>'奖励用户属性','award_items'=>'奖励道具','award_decors'=>'奖励装饰物','need_field'=>'need_field')
		);

		$list['magic_task_trunk'] = array(
		    'tbid'		=> 'magic_task_trunk',
		    'name'	    => '主线任务基础表',
			'column'	=> array('id'=>'id','child_id'=>'子任务ID','level'=>'(LV)','scene_name'=>'场景','start_npc_id'=>'接NPC(ID)','start_scene_id'=>'接场景ID','finish_npc_id'=>'交NPC(ID)','finish_scene_id'=>'交场景ID','icon_class'=>'iconclass','name'=>'名称','intro'=>'描述','condition_intro'=>'条件说明','type'=>'type','cid'=>'id','num'=>'num','icon_condition'=>'图标显示用type','award_prop'=>'奖励用户属性','award_items'=>'奖励道具','award_decors'=>'奖励装饰物','story_id'=>'剧情任务')
		);

		$list['magic_task_tutorial'] = array(
		    'tbid'		=> 'magic_task_tutorial',
		    'name'	    => '新手任务基础表',
			'column'	=> array('id'=>'id','level'=>'(LV)','scene_name'=>'场景','start_npc_id'=>'接NPC(ID)','start_scene_id'=>'接任务场景id','finish_npc_id'=>'交NPC(ID)','finish_scene_id'=>'交任务场景id','icon_class'=>'iconclass','name'=>'名称','intro'=>'描述','condition_intro'=>'条件说明','type'=>'type','cid'=>'id','num'=>'num','icon_condition'=>'图标显示用type','award_prop'=>'奖励用户属性','award_items'=>'奖励道具','award_decors'=>'奖励装饰物')
		);

		$list['magic_task_type'] = array(
		    'tbid'		=> 'magic_task_type',
		    'name'	    => '任务说明',
			'column'	=> array('id'=>'id','content'=>'描述')
		);

		$list['magic_tutorial'] = array(
		    'tbid'		=> 'magic_tutorial',
		    'name'	    => '新手引导基础表',
			'column'	=> array('id'=>'id','index'=>'index','name'=>'name','icon'=>'icon','event_type'=>'eventType','chats'=>'chats','act_tips'=>'actTips','contact'=>'contact','contactevent'=>'contactevent','gold'=>'奖励宝石','items'=>'奖励道具')
		);

	$list['line_1'] = array(
	    'tbid'		=> 'line_1',
	    'name'	    => 'line'
	);

		$list['magic_map_building'] = array(
		    'tbid'		=> 'magic_map_building',
		    'name'	    => '副本装饰基础表',
			'column'	=> array('id'=>'id','type'=>'类型12-门 13-地板 15-装饰 ','class_name'=>'类名','name'=>'名称','level'=>'等级','size_x'=>'长','size_y'=>'高','size_z'=>'宽')
		);

		$list['magic_map_monster'] = array(
		    'tbid'		=> 'magic_map_monster',
		    'name'	    => '副本怪矿基础表',
			'column'	=> array('id'=>'id','type'=>'类型1-怪 2-矿石','name'=>'名称','avatar_id'=>'avatarid','hp'=>'最大hp','size_x'=>'长','size_y'=>'高','size_z'=>'宽','is_boss'=>'是否boss','need_conditions'=>'消耗','award_conditions'=>'奖励','final_conditions'=>'最终掉落','defend_conditions'=>'怪攻击')
		);

		$list['magic_map_animation'] = array(
		    'tbid'		=> 'magic_map_animation',
		    'name'	    => '副本怪矿主角动画',
			'column'	=> array('id'=>'id','cid'=>'怪矿cid','label'=>'动画标签','ptime'=>'播放时间','type'=>'type攻or防','cover_label'=>'动效类名','cover_delay'=>'DELAY时间','cover_times'=>'coverTimes')
		);

		$list['magic_map_copy'] = array(
		    'tbid'		=> 'magic_map_copy',
		    'name'	    => '副本地图基础表',
			'column'	=> array('id'=>'id','parent_id'=>'总id','parent_scene_id'=>'父大地图id','name'=>'名称','content'=>'描述','icon_classname'=>'类名','x'=>'bgiconn坐标','y'=>'bgiconn坐标','bg'=>'对应大BG','state'=>'初始状态','need_level'=>'需要等级','condition1'=>'开启条件1','condition2'=>'开启条件2','mp'=>'消耗魔法','monster_xy'=>'怪坐标','entrances'=>'出入口','node_str'=>'场景数据date','type'=>'type','bgsound'=>'bgsound','bgtype'=>'bgtype','size_x'=>'numCols','size_y'=>'numRows','isostart_x'=>'isoStartX','isostart_y'=>'isoStartY','default_pos'=>'人物刷新点')
		);

		$list['magic_map_task'] = array(
		    'tbid'		=> 'magic_map_task',
		    'name'	    => '副本任务基础表',
			'column'	=> array('id'=>'id','map_parent_id'=>'地图总id','name'=>'名称','intro'=>'描述','condition_intro'=>'条件说明','prev_task_id'=>'前置任务id','start_npc_id'=>'接任务npc','start_map_id'=>'接任务场景id','finish_npc_id'=>'交任务npc','finish_map_id'=>'交任务场景id','icon_class'=>'图标类名','type'=>'任务类型','cid'=>'需要id','num'=>'需要数量','icon_condition'=>'图标显示用type','need_conditions'=>'消耗','award_conditions'=>'奖励')
		);


		$list['magic_map_copy_decor'] = array(
		    'keynum'	=> 2,
		    'candel'	=> 'map_id',
		    'tbid'		=> 'magic_map_copy_decor',
		    'name'	    => '副本编辑器装饰_1',
			'column'	=> array('id'=>'id首位1','map_id'=>'地图id','cid'=>'类id','pos_x'=>'坐标x','pos_z'=>'坐标z','mirror'=>'mirror')
		);

		$list['magic_map_copy_portal'] = array(
		    'keynum'	=> 2,
			'candel'	=> 'map_id',
		    'tbid'		=> 'magic_map_copy_portal',
		    'name'	    => '副本编辑器门_2',
			'column'	=> array('id'=>'id首位2','map_id'=>'地图id','cid'=>'类id','pos_x'=>'坐标x','pos_z'=>'坐标z','mirror'=>'mirror','tar_map_id'=>'下一地图id')
		);

		$list['magic_map_copy_ghost'] = array(
		    'keynum'	=> 2,
			'candel'	=> 'map_id',
		    'tbid'		=> 'magic_map_copy_ghost',
		    'name'	    => '副本编辑器怪_3',
			'column'	=> array('id'=>'id首位3','map_id'=>'地图id','cid'=>'类id','pos_x'=>'坐标x','pos_z'=>'坐标z','fiddle_range_x'=>'移动x','fiddle_range_z'=>'移动z','rate_onstage'=>'出现概率')
		);

		$list['magic_map_copy_mine'] = array(
		    'keynum'	=> 2,
		    'candel'	=> 'map_id',
		    'tbid'		=> 'magic_map_copy_mine',
		    'name'	    => '副本编辑器矿_4',
			'column'	=> array('id'=>'id首位4','map_id'=>'地图id','cid'=>'类id','pos_x'=>'坐标x','pos_z'=>'坐标z','rate_onstage'=>'出现概率')
		);

		$list['magic_map_copy_floor'] = array(
		    'tbid'		=> 'magic_map_copy_floor',
		    'candel'	=> 'map_id',
		    'name'	    => '副本编辑器地板_5',
			'column'	=> array('map_id'=>'地图id', 'data'=>'数据')
		);

	    return $list;
	}
}