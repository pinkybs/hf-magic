<?php

class Hapyfish2_Magic_HFC_User
{
	public static function resumeMP($uid, &$userMp)
	{
		$t = time();
		$mp_set_time = $userMp['mp_set_time'];
		if (empty($mp_set_time)) {
			$userMp['mp_set_time'] = $t;
			self::updateUserMp($uid, $userMp);
			return false;
		}

		$resume_mp_time = MP_RECOVERY_TIME;

		//魔法值已经满了
		if ($userMp['mp'] >= $userMp['max_mp']) {
			//时间间隔超过恢复间隔
			if ($userMp['mp_set_time'] + $resume_mp_time < $t) {
				$userMp['mp_set_time'] = $t;
				self::updateUserMp($uid, $userMp);
			}
			return false;
		}

		//魔法值没有满并且超过恢复间隔
		if ($userMp['mp_set_time'] + $resume_mp_time < $t) {
			//$resume_mp_rate = MP_RECOVERY_RATE/100;
			//$rate = floor(($t - $userMp['mp_set_time'])/$resume_mp_time);
			//$mpChange = ceil($userMp['max_mp']*$resume_mp_rate)*$rate;
			$rate = floor(($t - $userMp['mp_set_time'])/$resume_mp_time);
			$mpChange = MP_RECOVERY_MP*$rate;
			if ($userMp['mp'] + $mpChange >= $userMp['max_mp']) {
				$userMp['mp'] = $userMp['max_mp'];
			} else {
				$userMp['mp'] += $mpChange;
			}
			$userMp['mp_set_time'] += $rate*$resume_mp_time;
			self::updateUserMp($uid, $userMp);

			return true;
		}

		return false;
	}

	public static function getUserVO($uid)
	{
		$keys = array(
			'm:u:exp:' . $uid,
			'm:u:coin:' . $uid,
			'm:u:gold:' . $uid,
			'm:u:level:' . $uid,
			'm:u:scene:' . $uid,
			'm:u:avatar:' . $uid,
			'm:u:mp:' . $uid,
			'm:u:trans:' . $uid
		);

		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->getMulti($keys);

		if ($data === false) {
			return null;
		}

		$userVO = array('uid' => $uid);

		$userExp = $data[$keys[0]];
		if ($userExp === null) {
			try {
			    $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
	            $userExp = $dalUser->getExp($uid);
	            $cache->add($keys[0], $userExp);
			} catch (Exception $e) {
				return null;
			}
		}
		if (!$userExp) {
			$userExp = 0;
		}
		$userVO['exp'] = $userExp;

		$userCoin = $data[$keys[1]];
		if ($userCoin === null) {
			try {
			    $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
	            $userCoin = $dalUser->getCoin($uid);
	            $cache->add($keys[1], $userCoin);
			} catch (Exception $e) {
				return null;
			}
		}
		if (!$userCoin) {
			$userCoin = 0;
		}
		$userVO['coin'] = $userCoin;

		$userGold = $data[$keys[2]];
		if ($userGold === null) {
			try {
			    $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
	            $userGold = $dalUser->getGold($uid);
	            $cache->add($keys[2], $userGold, 3600);
			} catch (Exception $e) {
				return null;
			}
		}
		if (!$userGold) {
			$userGold = 0;
		}
		$userVO['gold'] = $userGold;

		$userLevel = $data[$keys[3]];
		if ($userLevel === null) {
			try {
			    $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
	            $userLevel = $dalUser->getLevel($uid);
	            if ($userLevel) {
	            	$cache->add($keys[3], $userLevel);
	            } else {
	            	return null;
	            }
			} catch (Exception $e) {
				err_log($e->getMessage());
				return null;
			}
		}
		$userVO['level'] = $userLevel[0];
		$userVO['house_level'] = $userLevel[1];

		$userScene = $data[$keys[4]];
		if ($userScene === null) {
			try {
			    $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
	            $userScene = $dalUser->getScene($uid);
	            if ($userScene) {
	            	$cache->add($keys[4], $userScene);
	            } else {
	            	return null;
	            }
			} catch (Exception $e) {
				err_log($e->getMessage());
				return null;
			}
		}
		$userVO['tile_x_length'] = $userScene[0];
		$userVO['tile_z_length'] = $userScene[1];
		$userVO['cur_scene_id'] = $userScene[2];
		$userVO['open_scene_list'] = $userScene[3];

		$userAvatar = $data[$keys[5]];
		if ($userAvatar === null) {
			try {
			    $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
	            $userAvatar = $dalUser->getAvatar($uid);
	            $cache->add($keys[5], $userAvatar);
			} catch (Exception $e) {
				return null;
			}
		}
		$userVO['avatar'] = $userAvatar[0];

		$userMpData = $data[$keys[6]];
		if ($userMpData === null) {
			try {
			    $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
	            $userMpData = $dalUser->getMp($uid);
	            if ($userMpData) {
	            	$cache->add($keys[6], $userMpData);
	            } else {
	            	return null;
	            }
			} catch (Exception $e) {
				err_log($e->getMessage());
				return null;
			}
		}
		$userMp = array('mp' => $userMpData[0], 'max_mp' => $userMpData[1], 'mp_set_time' => $userMpData[2]);
		self::resumeMP($uid, $userMp);
		$userVO['mp'] = $userMp['mp'];
		$userVO['max_mp'] = $userMp['max_mp'];
		$userVO['mp_set_time'] = $userMp['mp_set_time'];

		$userTrans = $data[$keys[7]];
		if ($userTrans === null) {
			try {
			    $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
	            $userTrans = $dalUser->getTrans($uid);
	            if ($userTrans) {
	            	$cache->add($keys[7], $userTrans);
	            } else {
	            	return null;
	            }
			} catch (Exception $e) {
				err_log($e->getMessage());
				return null;
			}
		}
		$userVO['trans_type'] = $userTrans[0];
		$userVO['trans_start_time'] = $userTrans[1];

		$userVO['next_level_exp'] = Hapyfish2_Magic_Cache_BasicInfo::getUserLevelExp($userVO['level'] + 1);

        return $userVO;
	}

	public static function getUser($uid, $fields)
    {
    	$keys = array();
    	$getExp = false;
    	$getCoin = false;
    	$getLevel = false;

    	if (isset($fields['exp'])) {
    		$keyExp = 'm:u:exp:' . $uid;
    		$keys[] = $keyExp;
    		$getExp = true;
    	}

    	if (isset($fields['coin'])) {
    		$keyCoin = 'm:u:coin:' . $uid;
    		$keys[] = $keyCoin;
    		$getCoin = true;
    	}

		if (isset($fields['level'])) {
			$keyLevel = 'm:u:level:' . $uid;
    		$keys[] = $keyLevel;
    		$getLevel = true;
    	}

    	if (empty($keys)) {
    		return null;
    	}

    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->getMulti($keys);
		if ($data === false) {
			return null;
		}

		$user = array('uid' => $uid);

		if ($getExp) {
			$userExp = $data[$keyExp];
			if ($userExp === null) {
				try {
				    $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
		            $userExp = $dalUser->getExp($uid);
		            $cache->add($keyExp, $userExp);
				} catch (Exception $e) {
					return null;
				}
			}
			if (!$userExp) {
				$userExp = 0;
			}
			$user['exp'] = $userExp;
		}

		if ($getCoin) {
			$userCoin = $data[$keyCoin];
			if ($userCoin === null) {
				try {
				    $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
		            $userCoin = $dalUser->getCoin($uid);
		            $cache->add($keyCoin, $userCoin);
				} catch (Exception $e) {
					return null;
				}
			}
			if (!$userCoin) {
				$userCoin = 0;
			}
			$user['coin'] = $userCoin;
		}

		if ($getLevel) {
			$userLevel = $data[$keyLevel];
			if ($userLevel === null) {
				try {
				    $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
		            $userLevel = $dalUser->getLevel($uid);
		            if ($userLevel) {
		            	$cache->add($keyLevel, $userLevel);
		            } else {
		            	return null;
		            }
				} catch (Exception $e) {
					return null;
				}
			}
			$user['level'] = $userLevel[0];
			$user['house_level'] = $userLevel[1];
		}

        return $user;
    }

    public static function getUserScene($uid)
    {
        $key = 'm:u:scene:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$userScene = $cache->get($key);
        if ($exp === false) {
        	try {
        		$dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
	            $userScene = $dalUser->getScene($uid);
	            if ($userScene) {
	            	$cache->add($key, $userScene);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		return null;
        	}
        }

        return array(
        	'tile_x_length' => $userScene[0],
        	'tile_z_length' => $userScene[1],
        	'cur_scene_id' => $userScene[2],
        	'open_scene_list' => $userScene[3]
        );
    }

    public static function getUserAvatar($uid)
    {
        $key = 'm:u:avatar:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
	            $data = $dalUser->getAvatar($uid);
	            $cache->add($key, $data);
        	} catch (Exception $e) {
        		return null;
        	}
        }

        $avatar = array('avatar_id' => $data[0], 'avatar_edit' => $data[1]);

        return $avatar;
    }

    public static function getUserExp($uid)
    {
        $key = 'm:u:exp:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$exp = $cache->get($key);
        if ($exp === false) {
        	try {
	            $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
	            $exp = $dalUser->getExp($uid);
	            $cache->add($key, $exp);
        	} catch (Exception $e) {
        		return null;
        	}
        }

        return $exp;
    }

    public static function getUserCoin($uid)
    {
        $key = 'm:u:coin:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$coin = $cache->get($key);
        if ($coin === false) {
        	try {
	            $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
	            $coin = $dalUser->getCoin($uid);
	            $cache->add($key, $coin);
        	} catch (Exception $e) {
        		return null;
        	}
        }

        return $coin;
    }

    public static function getUserGold($uid)
    {
        $key = 'm:u:gold:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$gold = $cache->get($key);
        if ($gold === false) {
        	try {
	            $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
	            $gold = $dalUser->getGold($uid);
	            $cache->add($key, $gold, 3600);
        	} catch (Exception $e) {
        		return null;
        	}
        }

        return $gold;
    }

    public static function reloadUserGold($uid)
    {
        try {
        	$dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
			$gold = $dalUser->getGold($uid);
        	$key = 'm:u:gold:' . $uid;
        	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
	        $cache->save($key, $gold, 3600);
        } catch (Exception $e) {
        	return null;
		}

        return $gold;
    }

    public static function getUserLevel($uid)
    {
        $key = 'm:u:level:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
	            $data = $dalUser->getLevel($uid);
	            if ($data) {
	            	$cache->add($key, $data);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		return null;
        	}
        }

        return array('level' => $data[0], 'house_level' => $data[1]);
    }

    public static function getUserMp($uid)
    {
        $key = 'm:u:mp:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
	            $data = $dalUser->getMp($uid);
	            if ($data) {
	            	$cache->add($key, $data);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		return null;
        	}
        }

       	$userMp = array('mp' => $data[0], 'max_mp' => $data[1], 'mp_set_time' => $data[2]);
       	self::resumeMP($uid, $userMp);

       	return $userMp;
    }

    public static function getUserTrans($uid)
    {
        $key = 'm:u:trans:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
	            $data = $dalUser->getTrans($uid);
	            if ($data) {
	            	$cache->add($key, $data);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		return null;
        	}
        }

        return array('trans_type' => $data[0], 'trans_start_time' => $data[1]);
    }

    public static function updateUserAvatar($uid, $avatarInfo, $savedb = false)
    {
		$key = 'm:u:avatar:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
        }

        if ($savedb) {
        	$data = array($avatarInfo['avatar_id'], $avatarInfo['avatar_edit']);
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
        			$info = array('avatar_id' => $avatarInfo['avatar_id'], 'avatar_edit' => $avatarInfo['avatar_edit']);
        			$dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
        			$dalUser->update($uid, $info);
        		} catch (Exception $e) {
        			info_log('updateUserAvatar:' . $e->getMessage(), 'HFC_User');
        		}
        	}

        	return $ok;
        } else {
        	return $cache->update($key, $avatar);
        }
    }

    public static function updateUserExp($uid, $userExp, $savedb = false)
    {
		$key = 'm:u:exp:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
        	$ok = $cache->save($key, $userExp);
        	if ($ok) {
        		try {
        			$info = array('exp' => $userExp);
        			$dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
        			$dalUser->update($uid, $info);
        		} catch (Exception $e) {
        		}
        	}

        	return $ok;
        } else {
        	return $cache->update($key, $userExp);
        }
    }

    public static function incUserExp($uid, $expChange)
    {
    	if ($expChange <= 0) {
    		return false;
    	}

    	$userExp = self::getUserExp($uid);
    	if ($userExp === null) {
    		return false;
    	}

    	$userExp += $expChange;

    	$ok = self::updateUserExp($uid, $userExp);

    	if ($ok) {
    		Hapyfish2_Magic_Bll_UserResult::mergeExp($uid, $expChange);
    		Hapyfish2_Magic_Bll_User::checkLevelUp($uid);
    	}

    	return $ok;
    }

    public static function updateUserCoin($uid, $userCoin, $savedb = false)
    {
		$key = 'm:u:coin:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
        	$ok = $cache->save($key, $userCoin);
        	if ($ok) {
        		try {
        			$info = array('coin' => $userCoin);
        			$dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
        			$dalUser->update($uid, $info);
        		} catch (Exception $e) {
        		}
        	}

        	return $ok;
        } else {
        	return $cache->update($key, $userCoin);
        }
    }

    public static function incUserCoin($uid, $coinChange, $savedb = false, $flag = 0)
    {
    	if ($coinChange <= 0) {
    		return false;
    	}

    	$userCoin = self::getUserCoin($uid);
    	if ($userCoin === null) {
    		return false;
    	}

    	$userCoin += $coinChange;

    	$ok = self::updateUserCoin($uid, $userCoin, $savedb);
    	if ($ok) {
    		Hapyfish2_Magic_Bll_UserResult::mergeCoin($uid, $coinChange);
    	}

    	return $ok;
    }

    public static function decUserCoin($uid, $coinChange, $savedb = false)
    {
    	if ($coinChange <= 0) {
    		return false;
    	}
    	$userCoin = self::getUserCoin($uid);
    	if ($userCoin === null) {
    		return false;
    	}
    	if ($userCoin < $coinChange) {
    		return false;
    	}
    	$userCoin -= $coinChange;

    	$ok = self::updateUserCoin($uid, $userCoin, $savedb);
    	if ($ok) {
    		Hapyfish2_Magic_Bll_UserResult::mergeCoin($uid, -$coinChange);
    	}

    	return $ok;
    }

    public static function incUserExpAndCoin($uid, $expChange, $coinChange, $savedb = false, $flag = 0)
    {
    	self::incUserExp($uid, $expChange);
    	self::incUserCoin($uid, $coinChange ,$savedb, $flag);
    }

    public static function decUserMp($uid, $mpChange, $savedb = false)
    {
    	if ($mpChange <= 0) {
    		return false;
    	}

    	$userMp = self::getUserMp($uid);
    	if ($userMp === null) {
    		return false;
    	}

    	if ($userMp['mp'] < $mpChange) {
    		return false;
    	}

    	$userMp['mp'] -= $mpChange;

    	$ok = self::updateUserMp($uid, $userMp, $savedb);

    	if ($ok) {
    		Hapyfish2_Magic_Bll_UserResult::mergeMp($uid, -$mpChange);
    	}

    	return $ok;
    }

    public static function incUserMp($uid, &$mpChange, $savedb = false)
    {
    	if ($mpChange <= 0) {
    		return false;
    	}

    	$userMp = self::getUserMp($uid);
    	if ($userMp === null) {
    		return false;
    	}

    	if ($userMp['mp'] + $mpChange > $userMp['max_mp']) {
    		$mpChange = $userMp['max_mp'] - $userMp['mp'];
    		$userMp['mp'] = $userMp['max_mp'];
    	} else {
    		$userMp['mp'] += $mpChange;
    	}

    	$ok = self::updateUserMp($uid, $userMp, $savedb);

    	if ($ok) {
    		Hapyfish2_Magic_Bll_UserResult::mergeMp($uid, $mpChange);
    	}

    	return $ok;
    }

    public static function decUserMaxMp($uid, $maxMpChange, $savedb = false)
    {
    	if ($maxMpChange <= 0) {
    		return false;
    	}

    	$userMp = self::getUserMp($uid);
    	if ($userMp === null) {
    		return false;
    	}

    	if ($userMp['max_mp'] < $maxMpChange) {
    		return false;
    	}

    	$userMp['max_mp'] -= $maxMpChange;

    	return self::updateUserMp($uid, $userMp, $savedb);
    }

    public static function incUserMaxMp($uid, &$maxMpChange, $savedb = false)
    {
    	if ($maxMpChange <= 0) {
    		return false;
    	}

    	$userMp = self::getUserMp($uid);
    	if ($userMp === null) {
    		return false;
    	}

    	$userMp['max_mp'] += $maxMpChange;

    	return self::updateUserMp($uid, $userMp, $savedb);
    }

    public static function updateUserTrans($uid, $transInfoInfo, $savedb = false)
    {
		$data = array($transInfoInfo['trans_type'], $transInfoInfo['trans_start_time']);

    	$key = 'm:u:trans:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
        			$info = array('trans_type' => $transInfoInfo['trans_type'], 'trans_start_time' => $transInfoInfo['trans_start_time']);
        			$dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
        			$dalUser->update($uid, $info);
        		} catch (Exception $e) {
        		}
        	}

        	return $ok;
        } else {
        	return $cache->update($key, $data);
        }
    }

    public static function updateUserLevel($uid, $levelInfo)
    {
		$data = array($levelInfo['level'], $levelInfo['house_level']);

    	$key = 'm:u:level:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        //$savedb = $cache->canSaveToDB($key, 900);
        $savedb = true;
        if ($savedb) {
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
        			$info = array('level' => $levelInfo['level'], 'house_level' => $levelInfo['house_level']);
        			$dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
        			$dalUser->update($uid, $info);
        		} catch (Exception $e) {
        		}
        	}

        	return $ok;
        } else {
        	return $cache->update($key, $data);
        }
    }

    public static function updateUserMp($uid, $mpInfo, $savedb = false)
    {
		$data = array($mpInfo['mp'], $mpInfo['max_mp'], $mpInfo['mp_set_time']);

    	$key = 'm:u:mp:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
        			$info = array('mp' => $mpInfo['mp'], 'max_mp' => $mpInfo['max_mp'], 'mp_set_time' => $mpInfo['mp_set_time']);
        			$dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
        			$dalUser->update($uid, $info);
        		} catch (Exception $e) {
        		}
        	}

        	return $ok;
        } else {
        	return $cache->update($key, $data);
        }
    }

    public static function updateUserScene($uid, $sceneInfo, $savedb = false)
    {
		$data = array(
			$sceneInfo['tile_x_length'],
			$sceneInfo['tile_z_length'],
			$sceneInfo['cur_scene_id'],
			$sceneInfo['open_scene_list']
		);

    	$key = 'm:u:scene:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
        			$info = array(
        				'tile_x_length' => $sceneInfo['tile_x_length'],
        				'tile_z_length' => $sceneInfo['tile_z_length'],
        				'cur_scene_id' => $sceneInfo['cur_scene_id'],
        				'open_scene_list' => $sceneInfo['open_scene_list']
        			);
        			$dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
        			$dalUser->update($uid, $info);
        		} catch (Exception $e) {
        		}
        	}

        	return $ok;
        } else {
        	return $cache->update($key, $data);
        }
    }


    public static function getUserLoginInfo($uid)
    {
        $key = 'm:u:login:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
	            $data = $dalUser->getLoginInfo($uid);
	            if ($data) {
	            	$cache->add($key, $data);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		return null;
        	}
        }

        $loginInfo = array(
        	'last_login_time' => $data[0],
        	'today_login_count' => $data[1],
        	'active_login_count' => $data[2],
        	'max_active_login_count' => $data[3],
			'all_login_count' => $data[4]
        );

        return $loginInfo;
    }

    public static function updateUserLoginInfo($uid, $loginInfo, $savedb = false)
    {

    	$key = 'm:u:login:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 3600);
        }

        $data = array(
    		$loginInfo['last_login_time'], $loginInfo['today_login_count'],
    		$loginInfo['active_login_count'], $loginInfo['max_active_login_count'],
			$loginInfo['all_login_count']
    	);

        if ($savedb) {
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
        			$dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
        			$dalUser->update($uid, $loginInfo);
        		} catch (Exception $e) {
        		}
        	}

        	return $ok;
        } else {
        	return $cache->update($key, $data);
        }
    }

}