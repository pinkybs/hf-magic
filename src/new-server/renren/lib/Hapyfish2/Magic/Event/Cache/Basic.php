<?php

class Hapyfish2_Magic_Event_Cache_Basic
{
	public static function getBasicMC()
	{
		$key = 'mc_1';
		return Hapyfish2_Cache_Factory::getBasicMC($key);
	}

	public static function getCollectionList($eCode)
	{
		$key = 'magic:event:collect:'.$eCode;
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadCollection($eCode);
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getCollectionInfo($id, $eCode)
	{
		$list = self::getCollectionList($eCode);
		if ($list && isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadCollection($eCode)
	{
		$db = Hapyfish2_Magic_Event_Dal_Basic::getDefaultInstance();
		$list = $db->getCollectionList($eCode);
		if ($list) {
			$key = 'magic:event:collect:'.$eCode;
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

}