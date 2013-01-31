<?php

class Hapyfish2_Magic_Cache_Magic
{
	public static function getList($uid, $option = false)
    {
        $key = 'm:u:magiclist:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);

        if ($data === false) {
			if ($cache->isNotFound()) {
				try {
				    $dalMagic = Hapyfish2_Magic_Dal_Magic::getDefaultInstance();
		            $data = $dalMagic->get($uid);
		            if ($data) {
		            	$cache->add($key, $data);
		            } else {
		            	return null;
		            }
				} catch (Exception $e) {
					err_log($e->getMessage());
					return null;
				}
			} else {
				return null;
			}
        }

        if ($option) {
        	return array(
				'study_ids' => json_decode($data[0], true),
				'trans_ids' => json_decode($data[1], true)
			);
        } else {
			return array(
				'study_ids' => $data[0],
				'trans_ids' => $data[1]
			);
        }
    }

    public static function update($uid, $magicList)
    {
    	$magicListData = array(
    		'study_ids' => is_array($magicList['study_ids']) ? json_encode($magicList['study_ids']) : $magicList['study_ids'],
    		'trans_ids' => is_array($magicList['trans_ids']) ? json_encode($magicList['trans_ids']) : $magicList['trans_ids']
    	);

    	$ok = self::save($uid, $magicListData);
    	if ($ok) {
        	$key = 'm:u:magiclist:' . $uid;
        	$cache = Hapyfish2_Cache_Factory::getMC($uid);
        	$data = array($magicListData['study_ids'], $magicListData['trans_ids']);
        	$cache->set($key, $data);
    	}

    	return $ok;
    }

    public static function save($uid, $magicListData)
    {
  		$info = array(
  			'study_ids' => $magicListData['study_ids'],
  			'trans_ids' => $magicListData['trans_ids']
  		);
    	try {
		    $dalMagic = Hapyfish2_Magic_Dal_Magic::getDefaultInstance();
            $dalMagic->update($uid, $info);
            return true;
		} catch (Exception $e) {
			err_log($e->getMessage());
			return false;
		}
    }

}