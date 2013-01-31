<?php

/**
 * Magic Nb Table's Cache
 *
 * @package    Happyfish/Magic/Bll/Cache
 * @copyright  Copyright (c) 
 * @create     2010/07/23    zhangxin
 */
class Happyfish_Magic_Bll_Cache_NbBasicInfo
{
    const PREFIX_NAME = 'Happyfish_Magic_Bll_Cache_NbBasicInfo';

    public static function getCacheKey($salt, $params = null)
    {
    	$key = SNS_PLATFORM . '_' . self::PREFIX_NAME . '_' . $salt . '_';
        if ($params != null) {
            if (is_array($params)) {
                $key .= implode('_', $params);
            }
            else {
                $key .= $params;
            }
        }
        return $key;
    }

    /**
     * get nb building info
     *
     * @param integer $bid
     * @return array
     */
    public static function getNbBuilding($bid)
    {
        $aryData = self::listNbBuilding();
        return $aryData[$bid];
    }
    
	/**
     * list nb info for building
     *
     * @return array
     */
    public static function listNbBuilding()
    {
    	$prex = 'listInfo_';
    	$key = self::getCacheKey($prex . 'NbBuilding');
    	$cache = Happyfish_Cache_Memcached::getInstance();
    	
        if (!$result = $cache->get($key)) {
        	$dalInfo = Happyfish_Magic_Dal_Nbasic_NbBuilding::getDefaultInstance();
            $lstData = $dalInfo->listInfo();
            if ($lstData) {
            	$result = array();
            	foreach ($lstData as $value) {
            		$bid = $value['bid'];
            		$result[$bid] = $value;
            	}
                $cache->add($key, $result, Happyfish_Cache_Memcached::LIFE_TIME_MAX);                
            }
        }
        return $result;
    }
    
	public static function clearNbBuilding()
    {
        self::_clearListInfo('NbBuilding');
    }
    
	/**
     * get nb card info
     *
     * @param integer $cid
     * @return array
     */
    public static function getNbCard($cid)
    {
    	$aryData = self::listNbCard();
        return $aryData[$cid];
    }
    
	/**
     * list nb info for card
     *
     * @return array
     */
    public static function listNbCard()
    {
    	$prex = 'listInfo_';
    	$key = self::getCacheKey($prex . 'NbCard');
    	$cache = Happyfish_Cache_Memcached::getInstance();
    	
        if (!$result = $cache->get($key)) {
        	$dalInfo = Happyfish_Magic_Dal_Nbasic_NbCard::getDefaultInstance();
            $lstData = $dalInfo->listInfo();
            if ($lstData) {
            	$result = array();
            	foreach ($lstData as $value) {
            		$cid = $value['cid'];
            		$result[$cid] = $value;
            	}
                $cache->add($key, $result, Happyfish_Cache_Memcached::LIFE_TIME_MAX);                
            }
        }
        return $result;
    }
    
	public static function clearNbCard()
    {
        self::_clearListInfo('NbCard');
    }
    
	/**
     * get nb guest info
     *
     * @param integer $id
     * @return array
     */
    public static function getNbGuest($id)
    {
    	$result = null;
        $aryData = self::_listInfo('NbGuest');
        foreach ($aryData as $data) {
        	if ($data['id'] == $id) {
        		$result = $data;
        		break; 
        	}
        }
        return $result;
    }
    
	public static function clearNbGuest()
    {
        self::_clearListInfo('NbGuest');
    }
    
    
	/**
     * get nb item info
     *
     * @param integer $id
     * @return array
     */
    public static function getNbItem($id)
    {
    	$aryData = self::listNbItem();
        return $aryData[$id];
    }
    
	/**
     * list nb info for item
     *
     * @return array
     */
    public static function listNbItem()
    {
    	$prex = 'listInfo_';
    	$key = self::getCacheKey($prex . 'NbItem');
    	$cache = Happyfish_Cache_Memcached::getInstance();
    	
        if (!$result = $cache->get($key)) {
        	$dalInfo = Happyfish_Magic_Dal_Nbasic_NbItem::getDefaultInstance();
            $lstData = $dalInfo->listInfo();
            if ($lstData) {
            	$result = array();
            	foreach ($lstData as $value) {
            		$mid = $value['mid'];
            		$result[$mid] = $value;
            	}
                $cache->add($key, $result, Happyfish_Cache_Memcached::LIFE_TIME_MAX);                
            }
        }
        return $result;
    }
    
	public static function clearNbItem()
    {
        self::_clearListInfo('NbItem');
    }
    
    
	/**
     * get nb level info
     *
     * @param integer $id
     * @return array
     */
    public static function getNbLevel($id)
    {
    	$aryData = self::listNbLevel();
        return $aryData[$id];
    }
    
	/**
     * list nb info for level
     *
     * @return array
     */
    public static function listNbLevel()
    {
    	$prex = 'listInfo_';
    	$key = self::getCacheKey($prex . 'NbLevel');
    	$cache = Happyfish_Cache_Memcached::getInstance();
    	
        if (!$result = $cache->get($key)) {
        	$dalInfo = Happyfish_Magic_Dal_Nbasic_NbLevel::getDefaultInstance();
            $lstData = $dalInfo->listInfo();
            if ($lstData) {
            	$result = array();
            	foreach ($lstData as $value) {
            		$id = $value['level'];
            		$result[$id] = $value;
            	}
                $cache->add($key, $result, Happyfish_Cache_Memcached::LIFE_TIME_MAX);                
            }
        }
        return $result;
    }
    
	public static function clearNbLevel()
    {
        self::_clearListInfo('NbLevel');
    }
    
    
	/**
     * get nb magicA info
     *
     * @param integer $id
     * @return array
     */
    public static function getNbMagicA($id)
    {
        $aryData = self::listNbMagicA();
        return $aryData[$id];
    }
    
	/**
     * list nb info for magicA
     *
     * @return array
     */
    public static function listNbMagicA()
    {
    	$prex = 'listInfo_';
    	$key = self::getCacheKey($prex . 'NbMagicA');
    	$cache = Happyfish_Cache_Memcached::getInstance();
    	
        if (!$result = $cache->get($key)) {
        	$dalInfo = Happyfish_Magic_Dal_Nbasic_NbMagicA::getDefaultInstance();
            $lstData = $dalInfo->listInfo();
            if ($lstData) {
            	$result = array();
            	foreach ($lstData as $value) {
            		$id = $value['id'];
            		$result[$id] = $value;
            	}
                $cache->add($key, $result, Happyfish_Cache_Memcached::LIFE_TIME_MAX);                
            }
        }
        return $result;
    }
    
	public static function clearNbMagicA()
    {
        self::_clearListInfo('NbMagicA');
    }
    
    
	/**
     * get nb magicB info
     *
     * @param integer $id
     * @return array
     */
    public static function getNbMagicB($id)
    {
    	$aryData = self::listNbMagicB();
        return $aryData[$id];
    }
    
	/**
     * list nb info for magicB
     *
     * @return array
     */
    public static function listNbMagicB()
    {
    	$prex = 'listInfo_';
    	$key = self::getCacheKey($prex . 'NbMagicB');
    	$cache = Happyfish_Cache_Memcached::getInstance();
    	
        if (!$result = $cache->get($key)) {
        	$dalInfo = Happyfish_Magic_Dal_Nbasic_NbMagicB::getDefaultInstance();
            $lstData = $dalInfo->listInfo();
            if ($lstData) {
            	$result = array();
            	foreach ($lstData as $value) {
            		$id = $value['id'];
            		$result[$id] = $value;
            	}
                $cache->add($key, $result, Happyfish_Cache_Memcached::LIFE_TIME_MAX);                
            }
        }
        return $result;
    }
    
	public static function clearNbMagicB()
    {
        self::_clearListInfo('NbMagicB');
    }
    
    
	/**
     * get nb magicC info
     *
     * @param integer $id
     * @return array
     */
    public static function getNbMagicC($id)
    {
    	$aryData = self::listNbMagicC();
        return $aryData[$id];
    }
    
	/**
     * list nb info for magicC
     *
     * @return array
     */
    public static function listNbMagicC()
    {
    	$prex = 'listInfo_';
    	$key = self::getCacheKey($prex . 'NbMagicC');
    	$cache = Happyfish_Cache_Memcached::getInstance();
    	
        if (!$result = $cache->get($key)) {
        	$dalInfo = Happyfish_Magic_Dal_Nbasic_NbMagicC::getDefaultInstance();
            $lstData = $dalInfo->listInfo();
            if ($lstData) {
            	$result = array();
            	foreach ($lstData as $value) {
            		$id = $value['id'];
            		$result[$id] = $value;
            	}
                $cache->add($key, $result, Happyfish_Cache_Memcached::LIFE_TIME_MAX);                
            }
        }
        return $result;
    }
    
	public static function clearNbMagicC()
    {
        self::_clearListInfo('NbMagicC');
    }
    
    
	/**
     * get nb message info
     *
     * @param integer $id
     * @return array
     */
    public static function getNbMessage($id)
    {
    	$aryData = self::listNbMessage();
        return $aryData[$id];
    }
    
	/**
     * list nb info for message
     *
     * @return array
     */
    public static function listNbMessage()
    {
    	$prex = 'listInfo_';
    	$key = self::getCacheKey($prex . 'NbMessage');
    	$cache = Happyfish_Cache_Memcached::getInstance();
    	
        if (!$result = $cache->get($key)) {
        	$dalInfo = Happyfish_Magic_Dal_Nbasic_NbMessage::getDefaultInstance();
            $lstData = $dalInfo->listInfo();
            if ($lstData) {
            	$result = array();
            	foreach ($lstData as $value) {
            		$id = $value['id'];
            		$result[$id] = $value;
            	}
                $cache->add($key, $result, Happyfish_Cache_Memcached::LIFE_TIME_MAX);                
            }
        }
        return $result;
    }
    
	public static function clearNbMessage()
    {
        self::_clearListInfo('NbMessage');
    }
    
    
	/**
     * get nb task daily info
     *
     * @param integer $id
     * @return array
     */
    public static function getNbTaskDaily($id)
    {
    	$aryData = self::listNbTaskDaily();
        return $aryData[$id];
    }
    
	/**
     * list nb info for task daily
     *
     * @return array
     */
    public static function listNbTaskDaily()
    {
    	$prex = 'listInfo_';
    	$key = self::getCacheKey($prex . 'NbTaskDaily');
    	$cache = Happyfish_Cache_Memcached::getInstance();
    	
        if (!$result = $cache->get($key)) {
        	$dalInfo = Happyfish_Magic_Dal_Nbasic_NbTaskDaily::getDefaultInstance();
            $lstData = $dalInfo->listInfo();
            if ($lstData) {
            	$result = array();
            	foreach ($lstData as $value) {
            		$id = $value['id'];
            		$result[$id] = $value;
            	}
                $cache->add($key, $result, Happyfish_Cache_Memcached::LIFE_TIME_MAX);                
            }
        }
        return $result;
    }
    
	public static function clearNbTaskDaily()
    {
        self::_clearListInfo('NbTaskDaily');
    }
    
    
    
	/**
     * get nb magic level info
     *
     * @param integer $id
     * @param integer $type
     * @return array
     */
    public static function getNbMagicLevel($id, $type)
    {
    	$result = null;
        $aryData = self::_listInfo('NbMagicLevel');
        foreach ($aryData as $data) {
        	if ($data['level'] == $id && $data['type'] == $type) {
        		$result = $data;
        		break; 
        	}
        }
        return $result;
    }
    
	public static function clearNbMagicLevel()
    {
        self::_clearListInfo('NbMagicLevel');
    }
    
    
	/**
     * get nb market level info
     *
     * @param integer $id
     * @return array
     */
    public static function getNbMarketLevel($id)
    {
    	$result = null;
        $aryData = self::_listInfo('NbMarketLevel');
        foreach ($aryData as $data) {
        	if ($data['id'] == $id) {
        		$result = $data;
        		break; 
        	}
        }
        return $result;
    }
    
	public static function clearNbMarketLevel()
    {
        self::_clearListInfo('NbMarketLevel');
    }
    
    
	
    

	/**
     * get nb symbol info
     *
     * @param integer $id
     * @return array
     */
    public static function getNbSymbol($id)
    {
    	$result = null;
        $aryData = self::_listInfo('NbSymbol');
        foreach ($aryData as $data) {
        	if ($data['id'] == $id) {
        		$result = $data;
        		break; 
        	}
        }
        return $result;
    }
    
	public static function clearNbSymbol()
    {
        self::_clearListInfo('NbSymbol');
    }
    
    

    
	public static function clearAll()
    {
    	self::clearNbBuilding();
    	self::clearNbCard();
    	self::clearNbGuest();
    	self::clearNbItem();
    	self::clearNbLevel();
    	self::clearNbMagicA();
    	self::clearNbMagicB();
    	self::clearNbMagicC();
    	self::clearNbMagicLevel();
    	self::clearNbMarketLevel();
    	self::clearNbMessage();
    	self::clearNbSymbol();
    	self::clearNbTaskDaily();
    }

    /**
     * list nb info for cache
     *
     * @param string $name
     * @return array
     */
    public static function _listInfo($name)
    {
    	$key = self::getCacheKey('listInfo_' . $name);
    	$cache = Happyfish_Cache_Memcached::getInstance();
    	
        if (!$result = $cache->get($key)) {
        	$refObj = 'Happyfish_Magic_Dal_Nbasic_' . $name;
        	if (is_callable(array($refObj, 'getDefaultInstance'))) {
	        	$dalInfo = call_user_func(array($refObj, 'getDefaultInstance'));
	            $result = $dalInfo->listInfo();
	            if ($result) {
	                $cache->add($key, $result, Happyfish_Cache_Memcached::LIFE_TIME_MAX);                
	            }
        	}
        }
        return $result;
    }
    
    /**
     * clear cache info
     *
     * @param integer $name
     */
    private static function _clearListInfo($name)
    {
        $key = self::getCacheKey('listInfo_' . $name);
        $cache = Happyfish_Cache_Memcached::getInstance();
        $cache->delete($key);
    }
}