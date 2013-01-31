<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Allows a template to be automatically loaded and displayed. Display can be
 * dynamically turned off in the controller methods, and the template file
 * can be overloaded.
 *
 * To use it, declare your controller to extend this class:
 * `class Your_Controller extends Template_Controller`
 *
 * $Id: template.php 3769 2008-12-15 00:48:56Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
abstract class Role_Controller extends Controller {

	/**
	 * Role loading and setup routine.
	 */
	public function __construct()
	{
		foreach ($_GET as $key => $vl) {
			$_POST[$key] = $vl;
		}
		
		//-------------------常量,全局变量---------------------------------------
		//初始化时间
		$timestamp = &PEAR::getStaticProperty('_APP', 'timestamp');
		$timestamp = time();
		
		$session = Session::instance();
		$session_key = $session->get('session_key');
		//判断session
		if (!$session_key) {
			Network::buffer_error('session_error');
		}
		
		header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');

		if (Router::$routed_uri != 'init' && Router::$routed_uri != 'init/index' && Router::$routed_uri != 'init/createrole') {
			$event_list = &PEAR::getStaticProperty('_APP', 'event_list');
			$role_id = Role::getOwnRoleId();
			$game_event = GameEvent::instance($role_id);
			$event_list = $game_event->getRoleEvent();
			
			Event::add_before('system.shutdown', array('Kohana', 'shutdown'), array('GameEvent', 'staticGetRoleEvent'));
		} 

		//任务处理
		Event::add_before('system.shutdown', array('Kohana', 'shutdown'), array('GameEvent', 'initBranchDailyEvent'));
		Event::add_before('system.shutdown', array('Kohana', 'shutdown'), array('GameEvent', 'storyRet'));
		
		Event::add_before('system.shutdown', array('Kohana', 'shutdown'), array('DbCache', 'updates'));
		Event::add_before('system.shutdown', array('Kohana', 'shutdown'), array('DbCache', 'sync'));
		Event::add('system.shutdown', array('Network', 'send'));
		
		//$prop = new Profiler;
		
		//记录请求时间和请求链接
		Kohana::log('error', microtime(true)." begin ".Router::$current_uri);
		Event::add('system.shutdown', array('common', 'endtime'));
		
		parent::__construct();
	}

} // End Template_Controller