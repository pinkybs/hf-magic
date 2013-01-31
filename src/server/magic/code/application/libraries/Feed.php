<?php

class Feed {
	//单例
	private static $instance;
	private $role_id;
	
	//缓存类实例
	protected $cache;
	
    /**
     * Memcached object
     *
     * @var mixed memcached object
     */
    protected $_memcached = null;
	
	public function __construct($role_id)
	{
		$this->role_id = $role_id;
		$this->cache = new Cache();
		//$this->_memcached = $mc;
	}
	
	/**
	 * Singleton instance of Basic
	 */
	public static function instance($role_id)
	{
		if (!isset(self::$instance[$role_id]))
		{
			// Create a new instance
			self::$instance[$role_id] = new Feed($role_id);
		}

		return self::$instance[$role_id];
	}
	
	/**
	 * add mini feed
	 * 
	 */
	public static function addMiniFeed($role_id, $actor, $target, $template_id, $type, $icon, $title = null)
	{
        $minifeed = array(
        	'role_id' => $role_id,
			'id' => $template_id,  //模板id:
			'actor' => $actor,
			'target' => $target,
			'type' => $type, //类型：系统，好友，
        	'icon' => $icon, //图标: 开心，不开心
			'create_time' => PEAR::getStaticProperty('_APP', 'timestamp')
        );
        if ( isset($title) ) {
        	$minifeed['title'] = $title;
        }
        
        self::insertMiniFeed($minifeed);
	}
	
	/**
	 * insert mini feed
	 *
	 * @param array $feed
	 * @return array
	 */
	public static function insertMiniFeed($feed)
	{
	    $role_id = $feed['role_id'];
	    
	    $newfeed = array(
	    	$feed['role_id'], $feed['id'], $feed['type'], $feed['actor'], $feed['target'], $feed['title'], $feed['icon'], $feed['create_time']
	    );
	    
	    self::insertMiniFeedCache($role_id, $newfeed);
	    
		//update user feed count
	    self::incNewMiniFeedCount($role_id);
	}

	/**
	 * read feed count
	 *
	 * @param integer $role_id
	 * @return intger
	 */
	public static function readFeedCount($role_id)
	{
		$key = 'feed_count1:' . $role_id;
		$cache = new Cache('feed_cache');
		$feedCount = $cache->get($key);
		if ( !$feedCount || $feedCount === false ) {
			$feedCount = 0;
		}
		return $feedCount;
	}
	
	public static function incNewMiniFeedCount($role_id)
	{
		$key = 'feed_count1:' . $role_id;
		$cache = new Cache('feed_cache');
		$feedCount = $cache->get($key);
		if ( !$feedCount || $feedCount === false ) {
			$feedCount = 0;
		}
		$feedCount++;
		$cache->set($key, $feedCount);
	}

	public static function clearNewMiniFeedCount($role_id)
	{
		$key = 'feed_count1:' . $role_id;
		$cache = new Cache('feed_cache');
		$cache->set($key, 0);
	}
	
    public function insertMiniFeedCache($role_id, $feed, $time = 604800)
    {
    	$key = 'feed_u:' . $role_id . '_t:' . $feed['2'];
    	$try = 5;
    	$null = null;
    	$maxLen = 50;
    	$ok = false;
    	$first = false;
    	
    	while($try > 0) {
			$cache = new Cache('feed_cache');
    	    $data = $cache->get($key);

    	    if ($data === false || !$data ) {
    	    	$data = array();
    	    	$first = true;
    	    }

    		if (count($data) >= $maxLen) {
    			$data = array_splice($data, 0, $maxLen - 1);
    		}
    		array_unshift($data, $feed);
    		
    		if ($first) {
    			$cache->set($key, $data, NULL, $time);
    		} else {
    			$cache->set($key, $data, NULL, $time);
    		}
			
			$ok = true;
			break;
			
			$try--;
    	}
    	
    	return $ok;
    }
    
	/**
	 * read feed
	 *
	 * @param integer $role_id
	 * @return array
	 */
	public static function readFeed($role_id, $pageSize1 = 50, $pageSize2 = 50, $pageSize3 = 50)
	{
		//get user mini feed
        $feeds = self::getFeedData($role_id);
        
        if (empty($feeds)) {
        	return array();
        }
        
        self::clearNewMiniFeedCount($role_id);
        
        return self::buildFeed($feeds);
	}
	
	public static function getFeedData($role_id)
	{
		//$key = 'feed_u:' . $role_id . '_t:' . $feed['type'];
		$cache = new Cache('feed_cache');
		$data1 = $cache->get('feed_u:' . $role_id . '_t:1');
		$data2 = $cache->get('feed_u:' . $role_id . '_t:2');
		$data3 = $cache->get('feed_u:' . $role_id . '_t:3');
		
		$data1 = empty($data1) ? array() : $data1;
		$data2 = empty($data2) ? array() : $data2;
		$data3 = empty($data3) ? array() : $data3;
		$data = array_merge($data1, $data2, $data3);
		
		if ( $data === false || !$data ) {
			return array();
		}
		
		$result = array();
		foreach ( $data as $feed ) {
			$result[] = array(
				'role_id' => $feed[0],
				'id' => $feed[1],
				'type' => $feed[2],
				'actor' => $feed[3],
				'target' => $feed[4],
				'title' => $feed[5],
				'icon' => $feed[6],
				'create_time' => $feed[7]
			);
		}
		
		return $result;
	}
	
    protected static function buildFeed(&$feeds)
    {
        $tpl = array();
		$basic_model = new Basic_Model();
		$feedTemplate = $basic_model->getFeedTemplate();
		
		foreach ( $feedTemplate as $key=>$data ) {
			$tpl[$data[0]['id']] = $data[0]['title'];
		}
        
    	for($i = 0, $count = count($feeds); $i < $count; $i++) {
    		$template_id = $feeds[$i]['id'];
        	$tplTitle = isset($tpl[$template_id]) ? $tpl[$template_id] : '';
        	$feedTitle = isset($feeds[$i]['title']) ? $feeds[$i]['title'] : array();
        	
        	$title = self::buildTemplate($feeds[$i]['actor'], $feeds[$i]['target'], $tplTitle, $feedTitle, $template_id);
        	if ($title) {
                $feeds[$i]['content'] = $title;
            }
            else {
                $feeds[$i]['content'] = '';
            }
            unset($feeds[$i]['role_id']);
            $feeds[$i]['createTime'] = $feeds[$i]['create_time'];
            unset($feeds[$i]['create_time']);
            unset($feeds[$i]['actor']);
            unset($feeds[$i]['target']);
        }

        return $feeds;
    }
    
    protected static function buildTemplate($actor_id, $target_id, $tplTitle, $feedTitle, $template_id)
    {
        if ($feedTitle == null) {
            $feedTitle = array();
        }

        if (!is_array($feedTitle)) {
            return false;
        }

        /*$role_actor = Role::create($actor_id);
        $actor = $role_actor->getUser();*/
        $actor = array('nickname' => 'user1');

        if (empty($actor)) {
            $actor_name = "____";
        }
        else {
            $actor_name = '<a href="event:' . $actor_id . '"><font color="#00CC99">' . $actor['nickname'] . '</font></a>';
        }

        $feedTitle['actor'] = $actor_name;

        if ($target_id) {
	        /*$role_target = Role::create($target_id);
	        $target = $role_target->getUser();*/
        	$target = array('nickname' => 'user2');
	        
            if (empty($target)) {
                $target_name = "____";
            }
            else {
            	$target_name = '<a href="event:' . $target_id . '"><font color="#00CC99">' .  $target['nickname'] . '</font></a>';
            }

            $feedTitle['target'] = $target_name;
        }

        $keys = array();
        $values = array();
		foreach ($feedTitle as $k => $v) {
			$keys[] = '{*' . $k . '*}';
			$values[] = $v;
		}
		
        return str_replace($keys, $values, $tplTitle);
    }
	
	
}