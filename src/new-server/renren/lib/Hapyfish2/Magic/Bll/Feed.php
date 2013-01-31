<?php

class Hapyfish2_Magic_Bll_Feed
{
	
	public static function getFeedData($uid)
	{
		$data = Hapyfish2_Magic_Cache_Feed::getFeedData($uid);
		if ($data === false) {
			return array();
		}
		
		$result = array();
		foreach ($data as $feed) {
			$result[] = array(
				'uid' => $feed[0],
				'template_id' => $feed[1],
				'actor' => $feed[2],
				'target' => $feed[3],
				'type' => $feed[4],
				'icon' => $feed[5],
				'title' => $feed[6],
				'create_time' => $feed[7]
			);
		}
		
		return $result;
	}
	
	public static function flushFeedData($uid)
	{
		Hapyfish2_Magic_Cache_Feed::flush($uid);
	}
	
	public static function insertMiniFeed($feed)
	{
	    $uid = $feed['uid'];
	    
	    $newfeed = array(
	    	$feed['uid'], $feed['template_id'], $feed['actor'], $feed['target'], $feed['type'], $feed['icon'], $feed['title'], $feed['create_time']
	    );
	    
	    Hapyfish2_Magic_Cache_Feed::insertMiniFeed($uid, $newfeed);

		//update user feed status
        Hapyfish2_Magic_Cache_Feed::incNewMiniFeedCount($uid);
	}
	
	public static function getFeed($uid, $pageIndex = 1, $pageSize = 50)
    {
		//get user mini feed
        $feeds = self::getFeedData($uid);
        
        if (empty($feeds)) {
        	return array();
        }
        
        Hapyfish2_Magic_Cache_Feed::clearNewMiniFeedCount($uid);
        
        return self::buildFeed($feeds);
    }
    
    protected static function buildFeed(&$feeds)
    {
        $tpl = Hapyfish2_Magic_Cache_BasicInfo::getFeedTemplate();
    	for($i = 0, $count = count($feeds); $i < $count; $i++) {
    		$template_id = $feeds[$i]['template_id'];
        	$tplTitle = isset($tpl[$template_id]) ? $tpl[$template_id] : '';
        	$feedTitle = isset($feeds[$i]['title']) ? $feeds[$i]['title'] : array();
        	$content = self::buildTemplate($feeds[$i]['actor'], $feeds[$i]['target'], $tplTitle, $feedTitle, $template_id);
    	    if ($content) {
                $feeds[$i]['content'] = $content;
            }
            else {
                $feeds[$i]['content'] = '';
            }
            unset($feeds[$i]['uid']);
            unset($feeds[$i]['template_id']);
            unset($feeds[$i]['title']);
            $feeds[$i]['createTime'] = $feeds[$i]['create_time'];
            unset($feeds[$i]['create_time']);
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

        $actor = Hapyfish2_Platform_Bll_User::getUser($actor_id);

        if (empty($actor)) {
            $actor_name = "____";
        }
        else {
            $actor_name = '<a href="event:' . $actor_id . '"><font color="#00CC99">' . $actor['name'] . '</font></a>';
        }

        $feedTitle['actor'] = $actor_name;

        if ($target_id) {
            $target = Hapyfish2_Platform_Bll_User::getUser($target_id);

            if (empty($target)) {
                $target_name = "____";
            }
            else {
            	$target_name = '<a href="event:' . $target_id . '"><font color="#00CC99">' .  $target['name'] . '</font></a>';
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