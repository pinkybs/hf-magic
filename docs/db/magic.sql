/*
SQLyog Community Edition- MySQL GUI v6.14 RC
MySQL - 5.0.51b-community-nt : Database - magic
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

create database if not exists `magic`;

USE `magic`;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `magic_nb_building` */

DROP TABLE IF EXISTS `magic_nb_building`;

CREATE TABLE `magic_nb_building` (
  `bid` int(10) unsigned NOT NULL COMMENT '装饰物id',
  `type` tinyint(1) unsigned default NULL COMMENT '类型：1-课桌 2-门 3-地板 4-墙纸 5-装饰 6-功能类 7-墙上装饰',
  `name` varchar(50) default NULL COMMENT '名称',
  `size_x` tinyint(1) unsigned default '1' COMMENT '长x',
  `size_y` tinyint(1) unsigned default '1' COMMENT '宽y',
  `can_be_cover` tinyint(1) unsigned default '0' COMMENT '能否被覆盖（仅3，4置1）',
  `can_sell` tinyint(1) unsigned default '1' COMMENT '能否分解1-can sell 0-no sell',
  `sell_red` int(10) unsigned default NULL COMMENT '分解得到红水晶数量',
  `sell_blue` int(10) unsigned default NULL COMMENT '分解得到蓝水晶数量',
  `sell_green` int(10) unsigned default NULL COMMENT '分解得到绿水晶数量',
  `can_overlay` tinyint(1) default '0' COMMENT '能否被叠加（1可叠加 0否）',
  `isnew` tinyint(1) default '0' COMMENT '1-新物件 ',
  `effect_mp` int(11) default '0' COMMENT '设施魔法值加成',
  `pic` varchar(255) default NULL,
  PRIMARY KEY  (`bid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `magic_nb_building` */

insert  into `magic_nb_building`(`bid`,`type`,`name`,`size_x`,`size_y`,`can_be_cover`,`can_sell`,`sell_red`,`sell_blue`,`sell_green`,`can_overlay`,`isnew`,`effect_mp`,`pic`) values (1,1,'sadasdasd',1,1,0,1,2,2,2,0,0,0,NULL),(2,1,'dddderrtt',2,2,0,1,NULL,NULL,NULL,0,0,0,NULL);

/*Table structure for table `magic_nb_building_type` */

DROP TABLE IF EXISTS `magic_nb_building_type`;

CREATE TABLE `magic_nb_building_type` (
  `id` tinyint(1) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `magic_nb_building_type` */

insert  into `magic_nb_building_type`(`id`,`name`) values (1,'课桌'),(2,'门'),(3,'地板'),(4,'墙纸'),(5,'装饰'),(6,'功能类'),(7,'墙上装饰');

/*Table structure for table `magic_nb_card` */

DROP TABLE IF EXISTS `magic_nb_card`;

CREATE TABLE `magic_nb_card` (
  `cid` int(10) unsigned NOT NULL COMMENT '道具id',
  `name` varchar(50) default NULL COMMENT '名称',
  `description` text COMMENT '描述',
  `money` int(10) unsigned default NULL COMMENT '游戏币',
  `cool_down` int(11) default '0' COMMENT '单位：分钟',
  `isnew` tinyint(1) default '0' COMMENT '是否新商品,0:非新,1:新 ',
  `pic` varchar(255) default NULL,
  PRIMARY KEY  (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `magic_nb_card` */

insert  into `magic_nb_card`(`cid`,`name`,`description`,`money`,`cool_down`,`isnew`,`pic`) values (1,'card11','sfasdf sa saf d d ',500,0,0,NULL),(2,'card2','sdfasd sd sd',900,0,0,NULL);

/*Table structure for table `magic_nb_guest` */

DROP TABLE IF EXISTS `magic_nb_guest`;

CREATE TABLE `magic_nb_guest` (
  `id` tinyint(1) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `magic_nb_guest` */

insert  into `magic_nb_guest`(`id`,`name`) values (1,'aaa'),(2,'bbb');

/*Table structure for table `magic_nb_item` */

DROP TABLE IF EXISTS `magic_nb_item`;

CREATE TABLE `magic_nb_item` (
  `mid` int(10) unsigned NOT NULL COMMENT '物品id',
  `name` varchar(50) default NULL COMMENT '物品名称',
  `animal_id` int(10) unsigned default NULL COMMENT '变化动物id',
  `animal_name` varchar(50) default NULL COMMENT '变化动物名',
  PRIMARY KEY  (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `magic_nb_item` */

insert  into `magic_nb_item`(`mid`,`name`,`animal_id`,`animal_name`) values (8301,'鸡蛋',8001,'鸡\r'),(8302,'木材',8002,'木头人\r'),(8303,'鼠尾巴',8003,'老鼠\r'),(8304,'石块',8004,'石头人\r'),(8305,'兔毛',8005,'兔子\r'),(8306,'花瓣',8006,'食人花\r'),(8307,'铁',8007,'铁皮人\r'),(8308,'野猪牙',8008,'野猪\r'),(8309,'蛙卵',8009,'青蛙\r'),(8310,'铜',8010,'铜人\r'),(8311,'熊毛',8011,'熊\r'),(8312,'羊毛',8012,'绵羊\r'),(8313,'冰块',8013,'冰块人\r'),(8314,'香蕉',8014,'白银矿工\r'),(8315,'龟背',8015,'乌龟\r'),(8316,'云',8016,'云\r'),(8317,'黄金',8017,'黄金矿工\r'),(8318,'熊抱胶囊',8018,'熊猫\r'),(8319,'白金',8019,'白金矿工\r'),(8320,'鹰眼',8020,'猫头鹰\r');

/*Table structure for table `magic_nb_level` */

DROP TABLE IF EXISTS `magic_nb_level`;

CREATE TABLE `magic_nb_level` (
  `level` int(11) NOT NULL COMMENT '等级',
  `exp` int(10) unsigned default NULL COMMENT '经验',
  `limit_seat` tinyint(1) unsigned default NULL COMMENT '座位上限',
  `limit_person` tinyint(1) unsigned default NULL COMMENT '活动人物上限',
  `house_size` tinyint(1) unsigned default NULL COMMENT '屋子大小',
  `limit_magic_lev` tinyint(1) unsigned default NULL COMMENT '可升级魔法等级',
  `max_mp` int(10) unsigned default NULL COMMENT '魔法值',
  `mp_recover_rate` tinyint(1) unsigned default '1' COMMENT '魔法值回复率（每5分钟回复百分比）',
  `levup_money` tinyint(1) unsigned default NULL COMMENT '升级赠送游戏币',
  `limit_exchange` int(11) unsigned default NULL COMMENT '水晶交换容器大小',
  PRIMARY KEY  (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `magic_nb_level` */

insert  into `magic_nb_level`(`level`,`exp`,`limit_seat`,`limit_person`,`house_size`,`limit_magic_lev`,`max_mp`,`mp_recover_rate`,`levup_money`,`limit_exchange`) values (1,100,2,2,8,1,100,1,2,50),(2,600,3,3,9,1,105,1,2,100),(3,1500,4,4,10,1,110,1,2,200),(4,2800,4,4,11,2,115,1,2,400),(5,4500,5,5,12,2,120,1,2,600),(6,6500,6,6,13,2,125,1,2,800),(7,8800,6,6,13,2,130,1,2,1000),(8,11400,7,7,14,2,135,1,2,1300),(9,14400,7,7,14,3,140,1,2,1500),(10,17800,8,8,15,3,145,1,2,1700),(11,21600,8,8,15,3,150,1,2,1900),(12,26000,8,8,15,3,155,1,2,2100),(13,31000,8,8,15,3,160,1,2,2200),(14,37000,9,9,16,3,175,1,2,2500),(15,44000,9,9,16,3,180,1,2,2700),(16,52000,9,9,16,4,185,1,2,2900),(17,61500,9,9,16,4,190,1,2,3100),(18,72500,10,10,17,4,195,1,2,3300),(19,85000,10,10,17,4,200,1,2,3500),(20,99500,10,10,17,4,205,1,2,3700),(21,116000,10,10,17,4,210,1,2,3900),(22,135200,10,10,17,4,215,1,2,4100),(23,157300,11,11,18,4,220,1,2,4300),(24,182700,11,11,18,4,225,1,2,4500),(25,211700,11,11,18,5,230,1,2,4700),(26,244700,11,11,18,5,235,1,2,4900),(27,282000,11,11,18,5,240,1,2,5200),(28,324100,11,11,18,5,245,1,2,5500),(29,371300,11,11,18,5,250,1,2,5800),(30,424100,12,12,19,5,255,1,2,6200),(31,482900,12,12,19,5,260,1,2,6600),(32,548200,12,12,19,5,265,1,2,7000),(33,620500,12,12,19,5,270,1,2,7500),(34,700300,12,12,19,5,275,1,2,8000),(35,788200,12,12,19,5,280,1,2,8500),(36,884600,12,12,19,5,285,1,2,9000),(37,990100,12,12,19,5,290,1,2,9500),(38,1105300,12,12,19,5,295,1,2,10000),(39,1230800,13,13,20,5,300,1,2,10001),(40,1367200,13,13,20,6,305,1,2,10002),(41,1514900,13,13,20,6,310,1,2,10003),(42,1669300,13,13,20,6,315,1,2,10004),(43,1830600,13,13,20,6,320,1,2,10005),(44,1999100,13,13,20,6,325,1,2,10006),(45,2175100,13,13,20,6,330,1,2,10007),(46,2358700,13,13,20,6,335,1,2,10008),(47,2550300,13,13,20,6,340,1,2,10009),(48,2750100,13,13,20,6,345,1,2,10010),(49,2958400,13,13,20,6,350,1,2,10011),(50,3175400,14,14,21,6,355,1,2,10012),(51,3401400,14,14,21,6,356,1,2,10013),(52,3636800,14,14,21,6,357,1,2,10014),(53,3881800,14,14,21,6,358,1,2,10015),(54,4136700,14,14,21,6,359,1,2,10016),(55,4401800,14,14,21,6,360,1,2,10017),(56,4677400,14,14,21,6,361,1,2,10018),(57,4963900,14,14,21,6,362,1,2,10019),(58,5261600,14,14,21,6,363,1,2,10020),(59,5570800,14,14,21,7,364,1,2,10021),(60,5891800,14,14,21,7,365,1,2,10022),(61,6225000,14,14,21,7,366,1,2,10023),(62,6570700,14,14,21,7,367,1,2,10024),(63,6929300,14,14,21,7,368,1,2,10025),(64,7301100,14,14,21,8,369,1,2,10026),(65,7686600,14,14,21,8,370,1,2,10027),(66,8086000,14,14,21,8,371,1,2,10028),(67,8499800,14,14,21,8,372,1,2,10029),(68,8928300,15,15,21,8,373,1,2,10030),(69,9372000,15,15,21,8,374,1,2,10031),(70,9831200,15,15,21,8,375,1,2,10032);

/*Table structure for table `magic_nb_magic_a` */

DROP TABLE IF EXISTS `magic_nb_magic_a`;

CREATE TABLE `magic_nb_magic_a` (
  `id` int(11) NOT NULL COMMENT '魔法id',
  `type` tinyint(1) unsigned default '1' COMMENT '类型（A1-火 A2-水 A3-木）',
  `name` varchar(50) default NULL COMMENT '魔法名称',
  `level` int(10) unsigned default NULL COMMENT '魔法等级',
  `crystal` int(10) unsigned default NULL COMMENT '学习需要水晶（火/水/木）',
  `money` int(10) unsigned default NULL COMMENT '学习需要游戏币',
  `spend_time` int(10) unsigned default NULL COMMENT '施法时间',
  `need_mp` int(10) unsigned default NULL COMMENT '消耗魔法值',
  `gain_crystal` int(10) unsigned default NULL COMMENT '收益水晶（火/水/木）',
  `gain_exp` int(10) unsigned default NULL COMMENT '获得经验',
  `pic` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `magic_nb_magic_a` */

insert  into `magic_nb_magic_a`(`id`,`type`,`name`,`level`,`crystal`,`money`,`spend_time`,`need_mp`,`gain_crystal`,`gain_exp`,`pic`) values (1001,1,'火01',1,0,0,300,2,5,3,NULL),(1002,1,'火02',1,300,0,600,4,10,6,NULL),(1003,1,'火03',1,600,0,900,7,16,9,NULL),(1004,1,'火04',2,900,0,1300,11,23,12,NULL),(1005,1,'火05',2,1500,1,1800,15,31,17,NULL),(1006,1,'火06',2,2100,2,2100,21,40,22,NULL),(1007,1,'火07',3,2700,2,2600,28,50,27,NULL),(1008,1,'火08',3,3300,3,3000,36,61,32,NULL),(1009,1,'火09',3,4200,3,3600,45,73,40,NULL),(1010,1,'火10',4,5100,3,3900,55,87,48,NULL),(1011,1,'火11',4,7000,4,4500,66,102,56,NULL),(1012,1,'火12',4,8300,4,5000,78,118,64,NULL),(1013,1,'火13',5,11000,5,5800,91,135,71,NULL),(1014,1,'火14',5,15000,5,6300,105,152,78,NULL),(1015,1,'火15',5,20000,6,7100,120,171,85,NULL),(1016,1,'火16',6,28000,6,7600,136,190,92,NULL),(1017,1,'火17',6,35000,8,8200,153,220,110,NULL),(1018,1,'火18',6,45000,10,8700,170,250,115,NULL),(1019,1,'火19',7,60000,15,9500,188,300,130,NULL),(1020,1,'火20',7,100000,20,11000,210,400,145,NULL),(2001,2,'冰01',1,0,0,300,2,5,3,NULL),(2002,2,'冰02',1,300,0,600,4,10,6,NULL),(2003,2,'冰03',1,600,0,900,7,16,9,NULL),(2004,2,'冰04',2,900,0,1300,11,23,12,NULL),(2005,2,'冰05',2,1500,1,1800,15,31,17,NULL),(2006,2,'冰06',2,2100,2,2100,21,40,22,NULL),(2007,2,'冰07',3,2700,2,2600,28,50,27,NULL),(2008,2,'冰08',3,3300,3,3000,36,61,32,NULL),(2009,2,'冰09',3,4200,3,3600,45,73,40,NULL),(2010,2,'冰10',4,5100,3,3900,55,87,48,NULL),(2011,2,'冰11',4,7000,4,4500,66,102,56,NULL),(2012,2,'冰12',4,8300,4,5000,78,118,64,NULL),(2013,2,'冰13',5,11000,5,5800,91,135,71,NULL),(2014,2,'冰14',5,15000,5,6300,105,152,78,NULL),(2015,2,'冰15',5,20000,6,7100,120,171,85,NULL),(2016,2,'冰16',6,28000,6,7600,136,190,92,NULL),(2017,2,'冰17',6,35000,8,8200,153,210,99,NULL),(2018,2,'冰18',6,45000,10,8700,171,220,107,NULL),(2019,2,'冰19',7,60000,15,9500,180,290,115,NULL),(2020,2,'冰20',7,100000,20,11000,200,350,125,NULL),(3001,3,'木01',1,0,0,300,2,5,3,NULL),(3002,3,'木02',1,300,0,600,4,10,6,NULL),(3003,3,'木03',1,600,0,900,7,16,9,NULL),(3004,3,'木04',2,900,0,1300,11,23,12,NULL),(3005,3,'木05',2,1500,1,1800,15,31,17,NULL),(3006,3,'木06',2,2100,2,2100,21,40,22,NULL),(3007,3,'木07',3,2700,2,2600,28,50,27,NULL),(3008,3,'木08',3,3300,3,3000,36,61,32,NULL),(3009,3,'木09',3,4200,3,3600,45,73,40,NULL),(3010,3,'木10',4,5100,3,3900,55,87,48,NULL),(3011,3,'木11',4,7000,4,4500,66,102,56,NULL),(3012,3,'木12',4,8300,4,5000,78,118,64,NULL),(3013,3,'木13',5,11000,5,5800,91,135,71,NULL),(3014,3,'木14',5,15000,5,6300,105,152,78,NULL),(3015,3,'木15',5,20000,6,7100,120,171,85,NULL),(3016,3,'木16',6,28000,6,7600,136,190,92,NULL),(3017,3,'木17',6,35000,8,8200,153,210,99,NULL),(3018,3,'木18',6,45000,10,8700,171,220,107,NULL),(3019,3,'木19',7,60000,15,9500,180,290,115,NULL),(3020,3,'木20',7,100000,20,11000,200,350,125,NULL);

/*Table structure for table `magic_nb_magic_b` */

DROP TABLE IF EXISTS `magic_nb_magic_b`;

CREATE TABLE `magic_nb_magic_b` (
  `id` int(11) NOT NULL COMMENT '魔法id',
  `type` tinyint(1) unsigned default '8' COMMENT '类型（B8-人型变）',
  `name` varchar(50) default NULL COMMENT '魔法名称',
  `level` int(10) unsigned default NULL COMMENT '魔法等级',
  `animal_id` int(10) unsigned default NULL COMMENT '变化术施法后效果-变动物',
  `red` int(10) unsigned default NULL COMMENT '学习需要水晶（火）',
  `blue` int(10) unsigned default NULL COMMENT '学习需要水晶（水）',
  `green` int(10) unsigned default NULL COMMENT '学习需要水晶（木）',
  `money` int(10) unsigned default NULL COMMENT '学习需要游戏币',
  `spend_time` int(10) unsigned default NULL COMMENT '施法时间',
  `need_mp` int(10) unsigned default NULL COMMENT '消耗魔法值',
  `gain_item` int(10) unsigned default NULL COMMENT '收益物品',
  `gain_exp` int(10) unsigned default NULL COMMENT '获得经验',
  `pic` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `magic_nb_magic_b` */

insert  into `magic_nb_magic_b`(`id`,`type`,`name`,`level`,`animal_id`,`red`,`blue`,`green`,`money`,`spend_time`,`need_mp`,`gain_item`,`gain_exp`,`pic`) values (3,8,'qwwqeqwe',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,8,'qweqwew223',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*Table structure for table `magic_nb_magic_c` */

DROP TABLE IF EXISTS `magic_nb_magic_c`;

CREATE TABLE `magic_nb_magic_c` (
  `id` int(11) NOT NULL COMMENT '魔法id',
  `type` tinyint(1) unsigned default '9' COMMENT '类型（C9-装饰变）',
  `name` varchar(50) default NULL COMMENT '魔法名称',
  `level` int(10) unsigned default NULL COMMENT '魔法等级',
  `building` int(10) unsigned default NULL COMMENT '变化出物品',
  `red` int(10) unsigned default NULL COMMENT '变化需要水晶（火）',
  `blue` int(10) unsigned default NULL COMMENT '变化需要水晶（水）',
  `green` int(10) unsigned default NULL COMMENT '变化需要水晶（木）',
  `money` int(10) unsigned default NULL COMMENT '变化需要游戏币',
  `need_building` int(10) unsigned default NULL COMMENT '变化需要装饰物',
  `need_item` int(10) unsigned default NULL COMMENT '变化需要物品',
  `need_item_count` int(10) unsigned default '0' COMMENT '变化需要物品数量',
  `spend_time` int(10) unsigned default NULL COMMENT '施法时间',
  `need_mp` int(10) unsigned default NULL COMMENT '消耗魔法值',
  `gain_building` int(10) unsigned default NULL COMMENT '收获装饰',
  `gain_exp` int(10) unsigned default NULL COMMENT '获得经验',
  `effect_mp` tinyint(1) default NULL COMMENT '设施魔法值加成',
  `pic` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `magic_nb_magic_c` */

insert  into `magic_nb_magic_c`(`id`,`type`,`name`,`level`,`building`,`red`,`blue`,`green`,`money`,`need_building`,`need_item`,`need_item_count`,`spend_time`,`need_mp`,`gain_building`,`gain_exp`,`effect_mp`,`pic`) values (5,9,'ert43t43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL);

/*Table structure for table `magic_nb_magiclevel` */

DROP TABLE IF EXISTS `magic_nb_magiclevel`;

CREATE TABLE `magic_nb_magiclevel` (
  `level` int(11) NOT NULL COMMENT '魔法等级',
  `type` tinyint(1) unsigned NOT NULL COMMENT '1-火 2-水 3-木 8-人型变 9-装饰变',
  `red` int(10) unsigned default '0' COMMENT '需要红水晶',
  `blue` int(10) unsigned default '0' COMMENT '需要蓝水晶',
  `green` int(10) unsigned default '0' COMMENT '需要绿水晶',
  `limit_person_lev` int(10) unsigned default NULL COMMENT '人物等级限制',
  PRIMARY KEY  (`level`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `magic_nb_magiclevel` */

insert  into `magic_nb_magiclevel`(`level`,`type`,`red`,`blue`,`green`,`limit_person_lev`) values (1,1,2,2,2,NULL),(1,2,3,3,3,NULL),(2,1,7,7,7,NULL);

/*Table structure for table `magic_nb_message` */

DROP TABLE IF EXISTS `magic_nb_message`;

CREATE TABLE `magic_nb_message` (
  `id` int(11) NOT NULL,
  `template` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `magic_nb_message` */

insert  into `magic_nb_message`(`id`,`template`) values (1,'aaaaa'),(2,'bbbbbb');

/*Table structure for table `magic_nb_symbol` */

DROP TABLE IF EXISTS `magic_nb_symbol`;

CREATE TABLE `magic_nb_symbol` (
  `id` int(11) NOT NULL auto_increment COMMENT '纹章id',
  `name` varchar(50) default NULL COMMENT '纹章名',
  `limit_task` int(10) unsigned default NULL COMMENT '解锁任务id',
  `red` int(10) unsigned default '0' COMMENT '纹章奖励红水晶',
  `blue` int(10) unsigned default '0' COMMENT '纹章奖励蓝水晶',
  `green` int(10) unsigned default '0' COMMENT '纹章奖励绿水晶',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `magic_nb_symbol` */

insert  into `magic_nb_symbol`(`id`,`name`,`limit_task`,`red`,`blue`,`green`) values (1,'wwwwwwww',NULL,0,0,0),(2,'qqqqqqq',NULL,0,0,0);

/*Table structure for table `magic_nb_task_daily` */

DROP TABLE IF EXISTS `magic_nb_task_daily`;

CREATE TABLE `magic_nb_task_daily` (
  `id` int(11) NOT NULL COMMENT '日常任务id 701~',
  `type` tinyint(1) unsigned default NULL COMMENT '1~9',
  `name` varchar(50) default NULL COMMENT '任务名',
  `description` text COMMENT '任务描述',
  `limit_level` int(10) unsigned default NULL COMMENT '开启等级',
  `close_level` int(10) unsigned default NULL COMMENT '关闭等级',
  `magic_level` int(10) unsigned default NULL COMMENT '使用的魔法等级',
  `need_count` int(10) unsigned default NULL COMMENT '需要次数',
  `gain_red` int(10) unsigned default '0' COMMENT '奖励红水晶',
  `gain_blue` int(10) unsigned default '0' COMMENT '奖励蓝水晶',
  `gain_green` int(10) unsigned default '0' COMMENT '奖励绿水晶',
  `gain_exp` int(10) unsigned default '0' COMMENT '奖励经验',
  `gain_item` int(10) unsigned default NULL COMMENT '奖励道具',
  `gain_item_count` int(11) default '0' COMMENT '奖励道具数量',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `magic_nb_task_daily` */

insert  into `magic_nb_task_daily`(`id`,`type`,`name`,`description`,`limit_level`,`close_level`,`magic_level`,`need_count`,`gain_red`,`gain_blue`,`gain_green`,`gain_exp`,`gain_item`,`gain_item_count`) values (1,1,'kkkkkkk','gdfgdfgdfg df df',NULL,NULL,NULL,NULL,0,0,0,0,NULL,0),(2,2,'eweeeeeee','sdfs sd fsd',NULL,NULL,NULL,NULL,0,0,0,0,NULL,0);

/*Table structure for table `magic_user` */

DROP TABLE IF EXISTS `magic_user`;

CREATE TABLE `magic_user` (
  `uid` int(11) NOT NULL,
  `exp` int(10) unsigned default '0' COMMENT '经验',
  `level` int(11) default '1' COMMENT '等级',
  `red` int(11) default '0' COMMENT '红水晶数 ',
  `blue` int(11) default '0' COMMENT '蓝水晶数 ',
  `green` int(11) default '0' COMMENT '绿水晶数 ',
  `money` int(11) default '0' COMMENT '游戏币',
  `house_name` varchar(50) default NULL COMMENT '魔法教室名',
  `isnew` tinyint(1) default '1' COMMENT '是否新手 1-新手 0-引导完毕',
  `last_login_time` int(10) unsigned default NULL COMMENT '最后登录时间',
  `login_days` int(10) unsigned default '1' COMMENT '登录天数',
  `create_time` int(10) unsigned default NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `magic_user` */

/*Table structure for table `magic_user_building` */

DROP TABLE IF EXISTS `magic_user_building`;

CREATE TABLE `magic_user_building` (
  `id` bigint(20) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `building_id` int(11) NOT NULL COMMENT '装饰物id，参照magic_nb_building表bid',
  `building_type` tinyint(1) unsigned default NULL COMMENT '装饰物type，参照magic_nb_building_type表id',
  `effect_mp` int(11) default '0' COMMENT '装饰物魔法值加成,参照magic_nb_building表effect_mp',
  `pos_x` int(11) default '0' COMMENT 'x坐标',
  `pos_y` int(11) default '0' COMMENT 'y坐标',
  `mirro` tinyint(1) default '0' COMMENT '镜像,0:非镜像,1:镜像',
  `status` tinyint(1) default NULL COMMENT '1:激活，0:非激活',
  `create_time` int(11) default NULL COMMENT '购买时间',
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`,`building_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `magic_user_building` */

/*Table structure for table `magic_user_card` */

DROP TABLE IF EXISTS `magic_user_card`;

CREATE TABLE `magic_user_card` (
  `uid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `card_count` int(10) unsigned default '1',
  PRIMARY KEY  (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `magic_user_card` */

/*Table structure for table `magic_user_magic` */

DROP TABLE IF EXISTS `magic_user_magic`;

CREATE TABLE `magic_user_magic` (
  `uid` int(11) NOT NULL,
  `magic_id` int(11) NOT NULL,
  `use_count` int(10) unsigned default '0',
  `create_time` int(10) unsigned default NULL,
  PRIMARY KEY  (`uid`,`magic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `magic_user_magic` */

/*Table structure for table `magic_user_magic_level` */

DROP TABLE IF EXISTS `magic_user_magic_level`;

CREATE TABLE `magic_user_magic_level` (
  `uid` int(11) NOT NULL,
  `magic_type` tinyint(1) unsigned NOT NULL COMMENT '1-火 2-水 3-木 8-人型变 9-装饰变',
  `level` tinyint(1) unsigned default '0' COMMENT '当前等级',
  `spent` int(10) unsigned default NULL COMMENT '花费',
  `update_time` int(10) unsigned default NULL COMMENT '更新时间',
  PRIMARY KEY  (`uid`,`magic_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `magic_user_magic_level` */

/*Table structure for table `mongo_magic_user_desk` */

DROP TABLE IF EXISTS `mongo_magic_user_desk`;

CREATE TABLE `mongo_magic_user_desk` (
  `uid` int(11) NOT NULL,
  `desk_id` bigint(20) NOT NULL COMMENT '参照magic_user_building。id',
  `status` tinyint(1) default '0' COMMENT '0-没人 1-等待学习 2-学习中 3-学习完了 4-紧急状态',
  `guest_id` tinyint(1) unsigned default NULL COMMENT 'npc人物id',
  `magic_id` int(11) default NULL COMMENT '学习魔法id，参照magic_user_magic.magic_id',
  `start_time` int(10) unsigned default NULL COMMENT '魔法学习开始时间',
  `break_time` int(10) unsigned default NULL COMMENT '魔法学习中断开始时间',
  `isshine` tinyint(1) default '0' COMMENT '是否发光状态（1-是 0-否）',
  `help_uid` int(11) default NULL COMMENT '来帮忙的好友id',
  PRIMARY KEY  (`uid`,`desk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `mongo_magic_user_desk` */

/*Table structure for table `mongo_magic_user_message` */

DROP TABLE IF EXISTS `mongo_magic_user_message`;

CREATE TABLE `mongo_magic_user_message` (
  `actor` int(11) default NULL,
  `target` int(11) default NULL,
  `type` tinyint(4) default NULL,
  `template_id` int(11) default NULL,
  `properties` text,
  `create_time` int(10) unsigned default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `mongo_magic_user_message` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
