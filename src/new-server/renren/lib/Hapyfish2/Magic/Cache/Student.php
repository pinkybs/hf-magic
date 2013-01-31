<?php

class Hapyfish2_Magic_Cache_Student
{
	public static function getUnlockStudentIds($uid)
	{
		$key = 'm:u:unlocksids:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);

		$studentIds = $cache->get($key);
		if ($studentIds === false) {
			try {
				$dalStudent = Hapyfish2_Magic_Dal_Student::getDefaultInstance();
				$studentIds = $dalStudent->getUnlockStudentIds($uid);
				if ($studentIds) {
					$cache->add($key, $studentIds);
				} else {
					return null;
				}
			} catch (Exception $e) {
				return null;
			}
		}

		return split(',', $studentIds);
	}

	public static function reloadStudentIds($uid)
	{
		try {
			$dalStudent = Hapyfish2_Magic_Dal_Student::getDefaultInstance();
			$studentIds = $dalStudent->getUnlockStudentIds($uid);
			if ($studentIds) {
				$key = 'm:u:unlocksids:' . $uid;
				$cache = Hapyfish2_Cache_Factory::getMC($uid);
				$cache->set($key, $studentIds);
			} else {
				return null;
			}
		} catch (Exception $e) {
			return null;
		}
	}

}