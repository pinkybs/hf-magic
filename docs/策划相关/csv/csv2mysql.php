<?php
$tables = array(
	"magic_mix","magic_study","level","magic_trans","event_trunk","event_branch",
	"event_daily","event_newbie","init","avatar","npc","scene","basic","item",
	"newbie","monster","building","size_scene","student","student_awards","house_level","student_level","story","story_talk"
);

// ���ӣ�ѡ�����ݿ�
$link = mysql_connect("192.168.1.31", "mysql", "mysql")
    or die("Could not connect: " . mysql_error());
echo "Connected successfully"."\n";
mysql_select_db("happymagic_basic") or die("Could not select database");

$table_pre = 'magic_basic_';

//������ݿ�
foreach($tables as $vl) {
	mysql_query("TRUNCATE TABLE $table_pre$vl");
}

//��ȡcsv,����sql
foreach($tables as $vl) {
	$data = file_get_contents("$vl.csv");
	$data = iconv("GBK", "UTF-8", $data);
	$config_content = explode("\r\n", $data, 3);
	file_put_contents("$table_pre$vl.sql", $config_content[2]);
}

// ִ�� SQL ��ѯ
foreach($tables as $vl) {
	$query = ' LOAD DATA LOCAL INFILE  "'.$table_pre.$vl.'.sql" INTO TABLE  `'.$table_pre.$vl.'` FIELDS TERMINATED BY  "," ENCLOSED BY  \'"\' ESCAPED BY  "\\\" LINES TERMINATED BY  "\r\n"';
	$result = mysql_query($query) or die("Query failed: " . mysql_error());
}

// �ͷŽ����
//mysql_free_result($result);

// �ر�����
mysql_close($link);

echo 'csv 2 mysql OK';
?>