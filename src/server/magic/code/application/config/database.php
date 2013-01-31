<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  Database
 *
 * Database connection settings, defined as arrays, or "groups". If no group
 * name is used when loading the database library, the group named "default"
 * will be used.
 *
 * Each group can be connected to independently, and multiple groups can be
 * connected at once.
 *
 * Group Options:
 *  benchmark     - Enable or disable database benchmarking
 *  persistent    - Enable or disable a persistent connection
 *  connection    - Array of connection specific parameters; alternatively,
 *                  you can use a DSN though it is not as fast and certain
 *                  characters could create problems (like an '@' character
 *                  in a password):
 *                  'connection'    => 'mysql://dbuser:secret@localhost/kohana'
 *  character_set - Database character set
 *  table_prefix  - Database table prefix
 *  object        - Enable or disable object results
 *  cache         - Enable or disable query caching
 *	escape        - Enable automatic query builder escaping
 */
$config['main_0'] = array
(
	'benchmark'     => TRUE,
	'persistent'    => FALSE,
	'connection'    => array
	(
		'type'     => 'Mysql',
		'user'     => 'mysql',
		'pass'     => 'mysql',
		'host'     => '192.168.1.31',
		'port'     => FALSE,
		'socket'   => FALSE,
		'database' => 'hm_main_0'
	),
	'character_set' => 'utf8',
	'table_prefix'  => 'magic_',
	'object'        => false,
	'cache'         => false,
	'escape'        => TRUE
);

$config['main_1'] = array
(
	'benchmark'     => TRUE,
	'persistent'    => FALSE,
	'connection'    => array
	(
		'type'     => 'Mysql',
		'user'     => 'mysql',
		'pass'     => 'mysql',
		'host'     => '192.168.1.31',
		'port'     => FALSE,
		'socket'   => FALSE,
		'database' => 'hm_main_1'
	),
	'character_set' => 'utf8',
	'table_prefix'  => 'magic_',
	'object'        => false,
	'cache'         => false,
	'escape'        => TRUE
);

$config['main_2'] = array
(
	'benchmark'     => TRUE,
	'persistent'    => FALSE,
	'connection'    => array
	(
		'type'     => 'Mysql',
		'user'     => 'mysql',
		'pass'     => 'mysql',
		'host'     => '192.168.1.31',
		'port'     => FALSE,
		'socket'   => FALSE,
		'database' => 'hm_main_2'
	),
	'character_set' => 'utf8',
	'table_prefix'  => 'magic_',
	'object'        => false,
	'cache'         => false,
	'escape'        => TRUE
);

$config['main'] = array
(
	'benchmark'     => TRUE,
	'persistent'    => FALSE,
	'connection'    => array
	(
		'type'     => 'Mysql',
		'user'     => 'mysql',
		'pass'     => 'mysql',
		'host'     => '192.168.1.31',
		'port'     => FALSE,
		'socket'   => FALSE,
		'database' => 'happymagic_main'
	),
	'character_set' => 'utf8',
	'table_prefix'  => 'magic_',
	'object'        => false,
	'cache'         => false,
	'escape'        => TRUE
);

$config['basic'] = array
(
	'benchmark'     => TRUE,
	'persistent'    => FALSE,
	'connection'    => array
	(
		'type'     => 'Mysql',
		'user'     => 'mysql',
		'pass'     => 'mysql',
		'host'     => '192.168.1.31',
		'port'     => FALSE,
		'socket'   => FALSE,
		'database' => 'happymagic_basic'
	),
	'character_set' => 'utf8',
	'table_prefix'  => 'magic_',
	'object'        => false,
	'cache'         => false,
	'escape'        => TRUE
);

$config['map'] = array
(
	'benchmark'     => TRUE,
	'persistent'    => FALSE,
	'connection'    => array
	(
		'type'     => 'Mysql',
		'user'     => 'mysql',
		'pass'     => 'mysql',
		'host'     => '192.168.1.31',
		'port'     => FALSE,
		'socket'   => FALSE,
		'database' => 'happymagic_map'
	),
	'character_set' => 'utf8',
	'table_prefix'  => 'magic_',
	'object'        => false,
	'cache'         => false,
	'escape'        => TRUE
);

$config['statis'] = array
(
	'benchmark'     => TRUE,
	'persistent'    => FALSE,
	'connection'    => array
	(
		'type'     => 'Mysql',
		'user'     => 'mysql',
		'pass'     => 'mysql',
		'host'     => '192.168.1.31',
		'port'     => FALSE,
		'socket'   => FALSE,
		'database' => 'happymagic_statis'
	),
	'character_set' => 'utf8',
	'table_prefix'  => 'magic_',
	'object'        => false,
	'cache'         => false,
	'escape'        => TRUE
);