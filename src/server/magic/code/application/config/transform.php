<?php
$config['decorClass'] = array(
	'id' => 'd_id',//test
	'name' => 'name',
	'type' => 'type',
	'' => 'type_show',
	'class_name' => 'class_name',
	'magic_type' => 'magic_type',
	'size_x' => 'size_x',
	'size_y' => 'size_y',
	'size_z' => 'size_z',
	'door_cooldown' => 'door_refresh_time',
	'door_guest_limit' => 'door_guest_limit',
	'effect_mp' => 'max_magic',
);
$config['levelInfos'] = array(
	'level' => 'level',
	'exp' => 'max_exp',
	'limit_desk' => 'desk_limit',
	'limit_student' => 'student_limit',
	'max_mp' => 'magic_limit',
	'tile_size' => 'tile_x_length',
	'0|tile_size' => 'tile_z_length',
	'levelup_gmoney' => 'gem',
	'coin' => 'coin',
	'levelup_item' => array('items', 'json_decode'),
	'levelup_decors' => array('decors', 'json_decode'),
);
$config['magicClass'] = array(
	'id' => 'magic_id',
	'name' => 'name',
	'type' => 'magic_type',
	'class_name1' => 'class_name',
	'class_name2' => 'actMovie',
	'need_mp' => 'mp',
	'gain_exp' => 'exp',
	'spend_time' => 'time',
	'level' => 'need_level',
	'gain_coin' => 'coin',
	'coin' => 'learn_coin',
	'money' => 'learn_gem',
	'content' => 'content',
);
$config['mixMagicClass'] = array(
	'id' => 'mix_mid',
	'name' => 'name',
	'type' => 'type',
	'building' => 'd_id',
	'coin' => 'coin',
	'need_building' => array('decorId', 'json_decode'),
	'need_item' =>  array('itemId', 'json_decode'),
	'level' => 'needLevel',
	'class_name' => 'class_name',
	'money' => 'gem',
);
$config['transMagicClass'] = array(
	'id' => 'trans_mid',
	'name' => 'name',
	'class_name' => 'class_name',
	'need_mp' => 'mp',
	'level' => 'needLevel',
	'gain_items' => array('itemId', 'json_decode'),
	'coin' => 'coin',
	'gain_exp' => 'exp',
	'money' => 'gem',
	'content' => 'content',
	//'avatar_id' => 'avatarId',
	'magic_time' => 'time',
);
$config['decorList'] = array(
	'id' => 'id',
	'x' => 'x',
	'y' => 'y',
	'z' => 'z',
	'mirror' => 'mirror',
	'bag_type' => 'bag_type',
	'building_id' => 'd_id',
	'building_type' => 'type',
	//'num' => 'num',
	'door_left_students_num' => 'door_left_students_num',
	'door_left_time' => 'door_left_time',
);
$config['user'] = array(
	'id' => 'uid',
	'name' => 'name',
	'tinyurl' => 'face',
	'level' => 'level',
	'exp' => 'exp',
	'max_exp' => 'max_exp',
	//'major_magic' => 'magic_type',
	'coin' => 'coin',
	'gmoney' => 'gem',
	'mp' => 'mp',
	'max_mp' => 'max_mp',
	//'max_mp' => 'door_limit',
	'tile_x_length' => 'tile_x_length',
	'tile_z_length' => 'tile_z_length',
	//'max_mp' => 'eat_limit',
	//'deal_level' => 'switchBagLevel',
	'house_level' => 'roomLevel',
	'avatar_id' => 'avatar',
	'trans_start_time' => 'trans_time',
	'trans_type' => 'trans_mid',
	'cur_scene_id' => 'currentSceneId',
	'mp_set_time' => 'replyMp_time',
	'avatar_id' => 'avatar',
	'popularity' => 'popularity',
);
$config['student'] = array(
	//'avatar_id' => 'avatar_id',
	'student_id' => 'sid',	
	'id' => 'decor_id',
	'state' => 'state',
	'end_time' => 'time',
	'magic_id' => 'magic_id',
	'event_time' => 'event_time',
	'coin' => 'coin',
	'stone_time' => 'stone_time',
	'key1' => 'can_steal',
	'student_id' => 'sid',
);
$config['itemClass'] = array(
	'id' => 'i_id',
	'name' => 'name',
	'content' => 'content',
	'type' => 'type',
	'add_mp' => 'add_mp',
	'gem' => 'gem',
	'coin' => 'coin',
	'class_name' => 'class_name',
	'canbuy' => 'sale',
);
$config['taskClass'] = array(
	'id' => 't_id',
	'' => 'index',
	'name' => 'name',
	'intro' => 'content',
	'icon_class' => 'icon_class',
	'start_scene_id' => 'sceneId',
	'start_npc_id' => 'npcId',
	'finish_npc_id' => 'finishNpcId',
	'finish_scene_id' => 'finishSceneId',
	'condition_intro' => 'quest_str',
	'icon_condition' => 'finish_condition',
	'' => 'awards',
);
$config['switchLevel'] = array(
	'id' => 'level',
	'num' => 'num',
	'price' => 'price',
);
$config['sceneClass'] = array(
	'id' => 'sceneId',
	'name' => 'name',
	'content' => 'content',
	'icon_classname' => 'className',
	'bg' => 'bg',
	'x' => 'x',
	'y' => 'y',
	'condition1' => 'needs1',
	'condition2' => 'needs2',
	'mp' => 'mp',
	'monster_xy' => array('enemy_xy', 'json_decode'),
	'entrances' => array('entrances', 'json_decode'),
	'node_str' => 'nodeStr',
);
$config['npcClass'] = array(
	'scene_id' => 'sceneId',
	'id' => 'npcId',
	'avatar_id' => 'avatarId',
	'name' => 'name',
	'x' => 'x',
	'y' => 'y',
	'z' => 'z',
	'click_type' => 'clickType',
	'click_value' => 'clickValue',
	'talks' => 'chats',
);
$config['avatarClass'] = array(
	'id' => 'avatarId',
	'name' => 'name',
	'classname' => 'className',
	'type' => 'type',
);
$config['guideClass'] = array(
	'id' => 'gid',
	'name' => 'name',
	'icon' => 'icon',
	'index' => 'index',
	'event_type' => 'eventType',
	'chats' => 'chats',
	'act_tips' => 'actTips',
	'contact' => 'contact',
	'contactevent' => 'contactevent',
);
$config['enemyClass'] = array(
	'id' => 'enemyCid',
	'name' => 'name',
	'avatar_id' => 'avatarId',
	'hp' => 'hp',
	'heal' => 'heal',
);
$config['enemys'] = array(
	'id' => 'enemyId',
	'eid' => 'enemyCid',
);
$config['addItem'] = array(
	'i_id' => 'i_id',
	'num' => 'num',
);
$config['removeItems'] = array(
	'i_id' => 'i_id',
	'num' => 'num',
);
$config['roomSizeClass'] = array(
	'id' => 'id',
	'0|size' => 'sizeX',
	'1|size' => 'sizeZ',
	'crystal' => 'crystal',
	'money' => 'gem',
	'level' => 'needLevel',
);
$config['studentLevelClass'] = array(
	'id' => 'level',
	'exp' => 'exp',
);
$config['studentClass'] = array(
	'id' => 'sid',
	'avatar_id' => 'avatar_id',
	'unlock_level' => 'unLockLevel',
);
$config['roomLevelClass'] = array(
	'id' => 'level',
	'mp' => 'needMaxMp',
	'student_limit' => 'student_limit',
	'desk_limit' => 'desk_limit',
	'coin' => 'coin',
	'gem' => 'gem',
	'items' => array('items', 'json_decode'),
	'decors' => array('decors', 'json_decode'),
);
$config['studentStates'] = array(
	'student_id' => 'sid',
	'exp' => 'exp',
	'level' => 'level',
	'award_flg' => 'needAward',
	'student_state' => 'student_state',
);
$config['actions'] = array(
	'npc_id' => 'npcId',
	'avatar_id' => 'avatarId',
	'x' => 'x',
	'y' => 'y',
	'face_x' => 'faceX',
	'face_y' => 'faceY',
	'content' => 'content',
	'camera' => 'camera',
	'wait' => 'wait',	
	'immediately' => 'immediately',	
	'hide' => 'hide',	
);
